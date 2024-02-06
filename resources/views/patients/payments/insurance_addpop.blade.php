<?php 
    $id = Route::current()->parameter('id'); 
    $check_status = $cc_status = false;
    $eft_status    = "Yes";
    $eft_disabled = $check_disabled =  $cc_disabled = "";
    if(isset($payment_details->pmt_mode) &&$payment_details->pmt_mode == "Check"){
        $check_status = "Yes";
        $eft_status    = false;
        $eft_disabled = "disabled";        
    } else if(isset($payment_details->pmt_mode) &&$payment_details->pmt_mode == "EFT"){
        $check_disabled = "disabled"; 
		$eft_status = "Yes";
		$eft_disabled = "";
    } else if(isset($payment_details->pmt_mode) &&$payment_details->pmt_mode == "Credit"){
		$check_disabled = "disabled";$eft_status    = false;
		$eft_disabled = "disabled";
		$cc_status = "Yes";
        $payment_details->check_no = $payment_details->card_no; 
    }
    $payment_type = isset($payment_details->pmt_type)?$payment_details->pmt_type:"";
    $payment_detail_id = isset($payment_details->id)?App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_details->id, 'encode'):"";
    $check_date = (isset($payment_details->check_date))?App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$payment_details->check_date):"";
    $add_calender = (!empty($payment_details))? "":'call-datepicker';
    $check_validation = empty($payment_details)?"js-check-number":"";
    $sel_claim = @$claim_id;
	$select_claim_count = count((array)$sel_claim) ;
	$claims_count = count($claims_lists);
	$default_checked_main = ($claims_count == $select_claim_count)? 'checked':'';
	if(!isset($get_default_timezone)){
		$get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
	}
?>
<div class="box box-view no-shadow no-border"><!-- VOB Box starts -->
{!! Form::open(['url'=>'patients/'.$id.'/payments/insurancecreate', 'id' => 'js-insurance-form', 'class' => 'jsinsuranceform','files'=>true]) !!} 
<div class="box-body form-horizontal p-b-0">     
     <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-10 margin-b-10"> 
        @if(!empty($payment_type)) 
        <span style="display: none">
            {!! Form::radio('payment_type_ins', $payment_type,'Yes',['class'=>'js-ins-type foc']) !!} {{$payment_type}} &emsp;
          </span>
        @else
            {!! Form::radio('payment_type_ins', 'Payment','Yes',['class'=>'js-ins-type foc','id'=>'c-payment']) !!} {!! Form::label('c-payment', 'Payment',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
            {!! Form::radio('payment_type_ins', 'Refund',null,['class'=>'js-ins-type foc','id'=>'c-refund']) !!} {!! Form::label('c-refund', 'Refund',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp; 
            {!! Form::radio('payment_type_ins', 'Adjustment',null,['class'=>'js-ins-type foc','id'=>'c-adjustment']) !!} {!! Form::label('c-adjustment', 'Adjustment',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
        @endif           
      </div>
    <span class = "js-length"></span>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-t-5 js-popupinsurance-data">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
            <span class="bg-white med-orange margin-l-10 padding-0-4 font600"> Payment Info</span>
        </div>            
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive form-horizontal margin-b-5">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal">
                <div class="form-group">
					<input type="hidden" name="temp_type_id" value="" id="temp_type_id" />
					{!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600 star']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 select2-white-popup">                                    
                    {!! Form::select('insurance_id',array('' => '-- Select -- ')+$patient_ins,@$payment_details->insurance_id,['class'=>'select2 form-control', 'id' => 'js-insurance-id']) !!}
                    </div>
                </div>
                @if(!empty($payment_type)) 
                    {!! Form::hidden('insurance_id',@$payment_details->insurance_id, ['id' => "js-payment-detail-id"]) !!} 
                @endif                                            
				
				@if(!empty($payment_type) && $check_status == "Yes")
					{!! Form::hidden('insur_payment_mode','Check') !!}                                    
				@elseif(!empty($payment_type) && $eft_status == "Yes")
					{!! Form::hidden('insur_payment_mode','EFT') !!} 
				@elseif(!empty($payment_type) && @$cc_status == "Yes")
					{!! Form::hidden('insur_payment_mode','Credit') !!}                                    
				@else
				<div class="form-group js-refund">          
					{!! Form::label('Mode', 'Mode', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600 star']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-8">
						{!! Form::radio('insur_payment_mode', 'EFT',$eft_status,['class'=>'', $eft_disabled,'id'=>'c-eft']) !!} {!! Form::label('c-eft', 'EFT',['class'=>'med-darkgray font600 form-cursor margin-r-10']) !!}
						{!! Form::radio('insur_payment_mode', 'Check', $check_status,['class'=>'', $check_disabled,'id'=>'c-check']) !!} {!! Form::label('c-check', 'Check',['class'=>'med-darkgray font600 form-cursor margin-r-10']) !!} 
						{!! Form::radio('insur_payment_mode', 'Credit',@$cc_status,['class'=>'', $eft_disabled,'id'=>'c-cc']) !!} {!! Form::label('c-cc', 'CC',['class'=>'med-darkgray font600 form-cursor']) !!} 
					</div>
				</div>
				@endif  

                <div class="form-group js-only-show-check" style = "display:none;">          
                {!! Form::label('Mode', 'Mode', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8">                                    
                     {!! Form::radio('insur_payment_mode_refund', 'Check', "Yes",['class'=>'flat-red','id'=>'cc-check']) !!} {!! Form::label('cc-check', 'Check',['class'=>'med-darkgray font600 form-cursor']) !!} &emsp;
                    </div>
                </div>
                <div class="form-group  js-adjustment">
                {!! HTML::decode(Form::label('check no', 'Check/EFT/CC No', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 p-r-0 control-label med-green font600 js-check-no star'])) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8">                                    
                    {!!Form::hidden('checkexist', null)!!}
                    <?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength");?>
                    {!! Form::text('check_no',@$payment_details->check_no,['autocomplete'=>'nope' ,'maxlength'=>$lengthval,'data-type'=> 'Insurance','class'=>'form-control input-sm-header-billing '.$check_validation]) !!}
                    </div>                                   
                </div> 
                 <?php $display = (@$payment_details->pmt_mode == "Credit")?"":"style = display:none;";?>   
                 <div class="form-group js-cc" {{$display}}>
                {!! Form::label('Card Type', 'Card Type', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600']) !!}                                                
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 select2-white-popup" >
						{!! Form::hidden('card_type', @$payment_details->card_type)!!}
						{!! Form::select('card_type', ['' => '--Select--','Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],@$payment_details->card_type,['class'=>'select2 form-control']) !!}
                    </div>                                   
                </div> 
                <div class="form-group js-adjustment">
					{!! Form::label('amount', 'Check/EFT/CC Date', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 p-r-0 control-label med-green font600 js-check-date star']) !!}
					<div class="col-lg-7 col-md-7 col-sm-7 col-xs-8"> 
					 <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('check_date')"></i> 
					  {!! Form::text('check_date', $check_date,['class'=>'form-control input-sm-header-billing  dm-date '. $add_calender,'maxlength' => 10, 'autocomplete'=>'nope']) !!} 
					</div>
				</div>                     
            </div>            
            {!! Form::hidden('payment_detail_id',@$payment_detail_id, ['id' => "js-payment-detail-id"]) !!} 
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form-horizontal "> 
                <div class = "js-adjustment">                                   
                    <div class="form-group">
                        {!! Form::label('amount', 'Amount', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600 star']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 ">                                    
                        {!! Form::text('payment_amt',@$payment_details->pmt_amt,['maxlength'=>'25','class'=>'form-control input-sm-header-billing allownumericwithdecimal js_amt_format', 'autocomplete'=>'nope']) !!}
                        </div>
                    </div>
                     <div class="form-group">
                        {!! Form::label('amount', 'Unapplied', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600 ']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8">
                            {!! Form::text('unapplied',@$payment_details->balance,['class'=>'form-control input-sm-header-billing','readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    </div>
                    <?php $adjustment_reason = App\Models\AdjustmentReason::getAdjustmentReason('Insurance'); ?> 
                     <div class="form-group">
                        {!! Form::label('amount', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-4 control-label med-green font600 ']) !!}                                                  
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 ">
                         {!! Form::text('insur_reference',@$payment_details->reference,['maxlength'=>'20','class'=>'form-control input-sm-header-billing', 'autocomplete'=>'nope']) !!}
                        </div>
                     </div>
                    <!--<div class="form-group js-hide-adjustment" style="display:none;">
                        {!! Form::label('adjustment Reason', 'Reason', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-4 control-label-billing med-green font600 star']) !!}     
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-8 select2-white-popup ">                                    
                            {!! Form::select('adjustment_reason', array('' => '--Select--')+$adjustment_reason,null,['class'=>'select2 form-control']) !!}
                        </div>
                    </div>-->
                
                <div class="form-group js-adjustment" >
                @if(empty($payment_type))
                <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload"> 
                    {!! Form::label('ref', 'Attachment', ['class'=>'col-lg-5 col-md-4 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!}          
                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-8 no-padding  margin-b-10 margin-l-10">
                        <span class="fileContainer upload-payment-btn" data-toggle="modal" data-target="#AddDocEra">Add Doc</span>
                        &emsp;<p class="js-display-error no-bottom" style="display:inline;"></p>
                    </div>
                </div>
                @endif
                </div> 
            </div>               
        </div>
    </div> 
     <div class="js-patient-search" style="display:none;">
         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-12 margin-t-10" >
            <div class="box box-info no-shadow" style="border: 1px solid #f1d392">
                <div class="box-body form-horizontal p-b-0 border-radius-4" style="background: #f9f0db;">
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-4 no-bottom form-horizontal m-b-m-10 margin-t-m-4">
                    <div class="form-group-billing">                    
                        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-10 billing-select2-orange">
                            {!! Form::select('patient_detail',array('name' => 'Name','last_name'=>'Last Name','first_name'=>'First Name','claim_number'=>'Claim No','account_no'=>'Acc no','policy_id'=>'Policy ID', 'dob' => 'DOB', 'ssn' => 'SSN'),null,['class'=>'form-control select2 js-search-popup', 'id' => 'PatientSearch']) !!}
                        </div>                                                     
                    </div>                                    
                    </div>
                    <div class="js-hide-datepicker col-lg-6 col-md-6 col-sm-6 col-xs-6 no-bottom form-horizontal m-b-m-8 margin-t-m-4"  style="border-color:#8ce5bb;">
                        <div class="form-group-billing">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                {!! Form::text('search_val',null,['class'=>'js-search-text form-control input-sm-header-billing textbox-bg-orange yes-border', 'id' => 'js-search-val', 'accesskey'=>'s']) !!}
                            </div>                            
                        </div>                  
                    </div> 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 no-bottom form-horizontal m-b-m-8 margin-t-m-4 js-show-datepicker"  style="border-color:#8ce5bb;display:none;">
                        <div class="form-group-billing">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">  
                             <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing" onclick = "iconclick('search_val_date')"></i>
                                 {!! Form::text('search_val_date',null,['class'=>'form-control input-sm-header-billing textbox-bg-orange yes-border dm-date call-datepicker']) !!}
                            </div>
                        </div>                  
                    </div>                        
                    <div class="col-lg-3 col-md-3 col-sm-2 col-xs-2 no-bottom form-horizontal m-b-m-8 margin-t-m-4"  style="border-color:#8ce5bb; ">
                        <div class="form-group-billing">                                                
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10 ">
                                <a href= "javascript:void(0)" class="btn btn-medcubics-small pull-left js-search-patient margin-t-0">Search</a>
                                <a href= "javascript:void(0)" class="btn btn-medcubics-small js-reset-patient margin-l-10 margin-t-0">Reset</a>
                            </div>
                        </div>                  
                    </div>            
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>            
      </div>        
</div>
   <?php //Session::forget('ar_claim_id'); ?>
     
@if(Session::has('ar_claim_id'))
    <?php 
        $claim_id =  Session::pull('ar_claim_id');
        $claimid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim_id,'decode');
        $ins_id = App\Models\Payments\ClaimInfoV1::where('id', $claimid)->value('insurance_id');
    ?>
    {!! Form::hidden('claim_ids',@$claim_id) !!}        
    <?php /*
	<div class="box-header-view-white text-center">  
       {!! Form::submit("Continue", ['class'=>'btn btn-medcubics-small js-continue-btn margin-b-10 margin-l-10', 'data-type' => 'Insurance', 'data-id' => 'js-insurance-form']) !!}
    </div>  
	*/  
	//Session::forget('ar_claim_id');
    ?>
     {!!Form::hidden('selected_insurance', @$ins_id, ['id' => 'js_selected_insurance'])!!}
     <!--<a href="javascript:void(0)" class="js-list-patientins">Getinsurance</a> -->
@endif    
<?php
	$claims_class = (!empty($claims_lists))?"js_popup_claimpatient_table":""; 
	$style_class = (empty($claims_lists) || !(empty($claim_id))) ? "" : ''; // style=display:none;
?>

<div class = "js-append-mainpayment-table" {{$style_class}}>
    <div class="box-body margin-t-10 no-padding js-paid-cal js_payment payment-pop-scroll"><!-- Notes Box Body starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive ">        
		<table id = "{{$claims_class}}" class="popup-table-wo-border table">
            <thead>
                <tr>   
                <th style="width:2%"><input type="checkbox" id="pat-checkall" class="js_menu_payment" {{$default_checked_main}}><label for='pat-checkall' class="no-bottom">&nbsp;</label></th>
                <th>DOS</th>
                <th>Claim No</th>                                
                <th>Billed To</th>                               
                <th class="text-right">Charge Amt</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Adj</th>
                <th class="text-right">Balance</th>
                <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php            
				$max = Config::get("siteconfigs.payment.max_claim_choose_onsearch"); 
				$sel_claim =  @$claim_id;
				$selected_insurance_id= (isset($sel_claim) &&!empty($sel_claim))?(isset($claims_lists[0]->insurance_id)?$claims_lists[0]->insurance_id: ""):"";
            ?> 
            @if(!empty($selected_insurance_id) || $selected_insurance_id == 0)
                {!!Form::hidden('selected_insurance', @$selected_insurance_id, ['id' => 'js_selected_insurance'])!!}
            @endif           
            @if(!empty($claims_lists))  
                @foreach($claims_lists as $claim)                                
                <tr>                                      
                    <?php 
						$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode');
						$insur_data_val = "Insurance";
						$disabled = '';
						$claim_insurance_id = '';
						$claim_multi_insurance = 0;	
						if(empty($claim->insurance_details)) {
							$insurance_data = "Self";
							//$disabled  = "disabled = disabled" ;                    
							$disabled  = "" ; 
							$claim_insurance_id = "patient";
						} else  {                   
							$insurance_data = App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
							$claim_multi_insurance = App\Http\Helpers\Helpers::checkIsMultiInsurance(@$claim->insurance_details->id, @$claim->patient_id);
						}                         
						$default_checked = ($sel_claim != '' && $sel_claim == $claim_id)? 'checked':'';
						$claim_insurance_id = (!empty($claim->insurance_details))?$claim->insurance_details->id:"patient";
						$disabled_class = (App\Http\Helpers\Helpers::checkForPaymnetRequirement($claim->id,$claim->status, 'Insurance'))?"":"disabled";
                    ?>
                    <td><a href="javascript:void(0)">
                    <input id = "{{$claim_id}}" data-insurance = "patient" type="checkbox" class="js-sel-claim js_submenu_payment" name = "insurance_checkbox" data-insid = "{{$claim_insurance_id}}" data-ismultiins = "{{ $claim_multi_insurance }}" data-max= "{{ $max}}" data-claim = "js-bal-{{$claim->claim_number}}"  data-hold = "{{$claim->status}}" {{$disabled_class}} {{$default_checked}}><label for="{{$claim_id}}" class="no-bottom">&nbsp;</label></a></td>
                    <?php $url = url('patients/popuppayment/'.$claim->id); ?>                              
                    <td><a>{{App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service, 'dob')}}</a></td>     
                    <td>{{@$claim->claim_number}}</td>               
                    <td>{!!$insurance_data!!}</td>                               
                    <td class="text-right">{{@$claim->total_charge}}</td>
                    {!!Form::hidden('patient_paid_amt', $claim->patient_paid,['class' => 'js-bal-'.$claim->claim_number])!!}
                    <td class="text-right">{{@$claim->total_paid}}</td> 
                    <td class="text-right"> {!!$claim->totalAdjustment!!}</td>                   
                    <td class="text-right" id = "js-bal-{{$claim->claim_number}}">{!!App\Http\Helpers\Helpers::priceFormat(@$claim->balance_amt)!!}</td>
                    <td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status }} @endif">{{@$claim->status}}</span></td>
                </tr>
                @endforeach
            @else
               <tr><td colspan="9" class="text-center"><span class="med-gray-dark">No Claims Available</span> </td></tr>
            @endif                       
            </tbody>
        </table>                  
        </div>                                           
    </div><!-- Notes box-body Ends-->       
    <div class="box-header-view-white text-center">  
        {!! Form::hidden('claim_ids',@$sel_claim) !!}                        
        {!! Form::submit("Continue", ['class'=>'btn btn-medcubics-small margin-b-10 js-continue-btn', 'data-type' => 'Insurance', 'data-id' => 'js-insurance-form', 'accesskey'=>'u']) !!} 
        {!!Form::hidden('patient_id', @$patient_id)!!}
        {!! Form::hidden('change_insurance_id',"") !!}
		{!! Form::hidden('pmt_post_ins_cat',"",['id' => 'pmt_post_ins_cat']) !!}
        {!!Form::close()!!}
        <button class="btn btn-medcubics-small padding-2-8 js-close-addclaim margin-b-10" accesskey="c" type="button">Cancel</button> 
        <!--<a href="javascript:void(0)" class="js-list-patientins">Getinsurance</a>        -->
    </div><!-- /.box-header -->
</div>
</div><!-- Notes box Ends -->

<div id="AddDocEra" class="modal fade" role="dialog">
  <div class="modal-md">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
			<button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">
				Add Document
			</h4>
		</div>
      <div class="modal-body">
			<div class="box-body no-bottom no-padding"><!--Background color for Inner Content Starts -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" >
					{!! Form::open(['url'=>'','id'=>'js-bootstrap-validator-adddocument','files'=>true,'method'=>'POST','name'=>'medcubicsform','class'=>'popupmedcubicsform  medcubicsform' ]) !!}
                        <div class="box no-shadow no-bottom">
								<!-- form start -->
                            <div class="box-body form-horizontal no-bottom">                        
                                <div class="form-group">
                                    {!! Form::label('title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('title')) error @endif">
                                        {!! Form::text('title',null,['class'=>'form-control']) !!} 
                                        {!! $errors->first('title', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
								<div class="form-group">
                                    {!! Form::label('category', 'Category', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('category', array('' => '-- Select --') + (array)App\Http\Helpers\Helpers::getDocumentCategory(),'Payer_Reports_ERA_EOB',['class'=>'select2 form-control','id'=>'category','disabled'=>'disabled']) !!}
                                        {!! $errors->first('category', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div>
								
								<div class="form-group">
                                    {!! Form::label('assigned', 'Assigned To', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('assigned', array('' => '-- Select --') + (array)App\Http\Helpers\Helpers::user_list(),null,['class'=>'select2 form-control','id'=>'assigned']) !!}
                                        {!! $errors->first('assigned', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 



                                <div class="form-group">
                                    {!! Form::label('priority', 'Priority', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('priority')) error @endif">
									<?php $priority = array('high'=>'High','moderate'=>'Moderate','low'=>'Low');  ?>
                                        {!! Form::select('priority', array('' => '-- Select --') + (array)$priority,null,['class'=>'select2 form-control','id'=>'priority']) !!}
                                        {!! $errors->first('priority', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('followup', 'Followup Date', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 p-r-0 control-label star ']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
                                        {!! Form::text('followup',null,['class'=>'form-control dm-date datepicker','id'=>'follow_up_date','autocomplete'=>'off']) !!}
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 @if($errors->first('category')) error @endif">
                                        {!! Form::select('status', array('' => '-- Select --') + (array)array('Assigned'=>'Assigned','Inprocess'=>'Inprocess','Pending'=>'Pending','Review'=>'Review','Completed'=>'Completed'),null,['class'=>'select2 form-control','id'=>'status']) !!}
                                        {!! $errors->first('status', '<p> :message</p>')  !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('page', 'Pages', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-7">
                                        {!! Form::text('page',null,['class'=>'form-control js_numeric','autocomplete'=>'off','maxlength'=> 7]) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 

                                <div class="form-group">
                                    {!! Form::label('notes', 'Notes', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label ']) !!} 
                                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                        {!! Form::textarea('notes',null,['class'=>'form-control']) !!} 
                                    </div>
                                    <div class="col-sm-1"></div>
                                </div> 
								<div class="form-group">
                                    <?php 
										$webcam = App\Http\Helpers\Helpers::getDocumentUpload('webcam');
										$scanner = App\Http\Helpers\Helpers::getDocumentUpload('scanner'); 
									?>
                                    @if($webcam || $scanner)  
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                        {!! Form::label('attachment', 'Attachment', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label star']) !!} 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                            {!! Form::radio('upload_type', 'browse',true,['class'=>'flat-red js-upload-type']) !!} Upload &emsp;
                                            @if($webcam){!! Form::radio('upload_type', 'webcam',null, ['class'=>'flat-red js-upload-type']) !!} Picture &emsp;@endif
                                            @if($scanner){!! Form::radio('upload_type', 'scanner',null,['class'=>'flat-red js-upload-type']) !!} Scanner @endif
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div> 
                                    @endif
                                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-upload margin-t-10">
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                                           <div class="dropdown pull-right">
                                               <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                   <i class="fa fa-question-circle margin-t-3 med-green form-icon-billing pull-right"  data-placement="top" data-toggle="tooltip" data-original-title="Info"></i>
                                               </a>
                                               <div class="dropdown-menu1">
                                                   <p class="font12 padding-4">pdf, jpeg, jpg, png, gif, doc, xls, csv, docx, xlsx, txt</p>
                                               </div>
                                           </div>
                                       </div>
                                        
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                                            <span class="fileContainer " style="padding:1px 16px;"> 
                                                <input class="form-control form-cursor uploadFile" name="filefield" type="file" id="filefield1">Upload  </span>
                                                
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                            <div>&emsp;<p class="js-display-error" style="display: inline;"></p>
                                                <span><i class="fa fa-times-circle cur-pointer removeFile margin-l-10 med-red" data-placement="bottom" data-toggle="tooltip" title="Remove" data-original-title="Tooltip on bottom" style="display:none;"></i></span>
                                            </div>
                                        </div>                                        
                                        
                                    </div>							 

                                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-12 no-padding js-photo margin-t-10" style="display:none">
                                        {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-4 control-label']) !!} 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7 no-padding">
                                            <span class="fileContainer js-webcam-class" style="padding:1px 20px;">
                                                <input type="hidden" class="js_err_webcam" /> Webcam</span>
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                            &emsp;<span class="js-display-error"></span>
                                        </div>
                                        <div class="col-sm-1"></div>
                                    </div>
                                    <div class="box-footer js-scanner" style="display:none"> 
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                                            <button type="button" class="btn btn-medcubics" onclick="scan();">Scan</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="upload_type" value="browse">
                                    <input type="hidden" name="scanner_filename" id="scanner_filename">
                                    <input type="hidden" name="scanner_image" id="scanner_image">
                                    @if($errors->first('filefield'))
                                    <div class="form-group">
                                        {!! Form::label('', '', ['class'=>'col-lg-2 col-md-2 col-sm-3 control-label']) !!}
                                        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-7 @if($errors->first('filefield')) error @endif">
                                            {!! $errors->first('filefield',  '<p> :message</p>')  !!} 
                                        </div>                                                          
                                        <div class="col-sm-1"></div>
                                    </div>
                                    @endif
                                </div><!-- /.box-body -->
                                <div class="box-footer no-padding">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                                        {!! Form::submit('Continue', ['class'=>'btn btn-medcubics-small form-group']) !!}
                                        <!-- MR-2894 : clearing form data : 21 Sep 2019 : Selva  -->
                                        {!! Form::button('Cancel', ['class'=>'btn btn-medcubics-small close_popup js-payment-doc-close margin-l-20', 'data-label'=>'close']) !!}	
                                    </div>
                                </div><!-- /.box-footer -->
	
							</div>
						</div>
					{!! Form::close() !!}
				</div>
			</div>						
      </div>     
    </div>
  </div>
</div>
                        
<script type="text/javascript">
$('input[type="text"]').attr('autocomplete','off');
    $(document).ready(function () {
        $(document).on('change', 'input[name="check_date"]',function(){          
        $('#js-insurance-form')
          .bootstrapValidator('updateStatus', 'check_date', 'NOT_VALIDATED')
          .bootstrapValidator('validateField', 'check_date');        
        }); 
        $(document).on('change', 'input[name="payment_amt"]',function(){               
            $('form#js-insurance-form').bootstrapValidator('revalidateField', 'payment_amt'); 
        });            
    });
		
	$(document).ready(function () {
        $('#js-bootstrap-validator-adddocument').bootstrapValidator({
            message: 'This value is not valid',
            excluded: [':disabled'],
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                title: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: title_lang_err_msg
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9 ]+$/,
                            message: alphanumericspace_lang_err_msg
                        }
                    }
                },
				assigned: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Assigned To'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				priority: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Priority'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				'followup': {
					trigger: 'change keyup',
					validators: {
						notEmpty: {
							message: 'Select followup Date'
						},
						date:{
							format:'MM/DD/YYYY',
							message: 'Invalid date format'
						},
						callback: {
							message: '',
							callback: function(value, validator, $field) {
								var fllowup_date = $('#follow_up_date').val();
								var current_date=new Date(fllowup_date);
								var d=new Date();	
								if(fllowup_date != '' && ( d.getTime()-96000000 ) > current_date.getTime()){
									return {
										valid: false,
										message: "Please give future date"
									};
								} else {
									return true;
								}
							}
						}
					}
				},
				status: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: 'Select Status'
                        },
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				notes: {
                    message: '',
                    validators: {
                        callback: {
                            message: attachment_lang_err_msg,
                            callback: function (value, validator) {
                                return true;
                            }
                        }
                    }
                },
				// page: {
    //                 message: '',
    //                 validators: {
				// 		integer: {
				// 			message: 'The value is not an integer'
				// 		},
    //                     notEmpty: {
    //                         message: 'Enter Pages'
    //                     },
    //                     callback: {
    //                         message: attachment_lang_err_msg,
    //                         callback: function (value, validator) {
    //                             return true;
    //                         }
    //                     }
    //                 }
    //             },
                filefield: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: attachment_lang_err_msg
                        },
                        file: {
							maxSize: filesize_max_defined_length * 32768,
                            message: attachment_length_lang_err_msg
                        },
                        callback: {
                            message: attachment_valid_lang_err_msg,
                            callback: function (value, validator) {
                                if ($('[name="filefield"]').val() != "") {
									var extension_Arr 	= ['pdf','jpeg','jpg','png','gif','doc','xls','csv','docx','xlsx','txt','PDF','JPEG','JPG','PNG','GIF','DOC','XLS','CSV','DOCX','XLSX','TXT'];
									var file_name 		= $('[name="filefield"]')[0].files[0].name;
									var temp			= file_name.split(".");
									filename_length = ((temp.length) - 1);
									if(extension_Arr.indexOf(temp[filename_length]) == -1){
										return false;
									}else{
										return true;
									}
                                }
                                return true;
                            }
                        }
                    }
                }
            }
        }).on('success.form.bv', function(e) {
			e.preventDefault();  
			/* var formData = $('form#js-bootstrap-validator-adddocument').serializeArray();
			var image = $('#filefield1')[0].files[0]; */
			var formData = new FormData(this);
			//formData.push({name: 'filefield', value: image});
			$.ajax({
				type 		: 	'POST',
				url  		:	api_site_url+'/documents/paymentPostingUpload',
				data		:	formData,
				processData	: 	false,
				contentType	: 	false,
				success 	:  function(temp_type_id) {
					$('#temp_type_id').val(temp_type_id);
					$('#AddDocEra').modal('toggle');
				}
			});
		});
	});

	$(document).on('click','.js_session_confirm',function(){
		$("#js-bootstrap-validator-adddocument").trigger("reset");
	});

	$(document).ready(function(){
	// MR-2895 : datepicker issues fixed : 21 Sep 2019 : selva
		$("#follow_up_date").datepicker({minDate: 0});
	});

	/* Appending Category for onclick */
	$(document).on('click','span[data-target=#AddDocEra]',function(){
		$('select[name=category]').select2('val','Payer_Reports_ERA_EOB');
	});
	
    // Only numeric allow to enter
    $(document).on('keypress keyup blur','.js_numeric',function(event){
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

	<?php if(isset($get_default_timezone)){?>
		 var get_default_timezone = '<?php echo $get_default_timezone;?>';    
	<?php }?>	
</script>