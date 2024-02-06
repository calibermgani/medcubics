<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <!-- Tab Starts  -->
    <?php
		$patient_id_routs = Route::current()->parameter('id');
		$id = (isset($patient_id) && empty($patient_id_routs)) ? $patient_id : $patient_id_routs;
		$claim_id = (!empty($claims_list)) ? App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims_list->id, 'encode') : 0;
		$activetab = 'payments_list';
		$routex = explode('.', Route::currentRouteName());
		//dd($post_val);
		$final = [];
		$curr = array($claim_id);
		$claim_ids = $post_val['claim_ids'];
		$post_arra = explode(',', $post_val['claim_ids']);
		$final = array_diff($post_arra, $curr);
		$final = implode(',', array_filter($final));
		$post_val['claim_ids'] = $final;
		if (count(explode(',', $claim_ids)) > 1) {
			Session::put('post_val', $post_val);
		} else {
			Session::forget('post_val');
		}
		$post_val = (object) $post_val;
		$card_type = @$post_val->card_type;
		if (isset($post_val->insurance_id)) {
			$insurance_id = (strpos($post_val->insurance_id, '-') !== FALSE) ? explode('-', @$post_val->insurance_id) : array(1 => @$post_val->insurance_id);
			// For main insurance the category won' come
			$insurance_id = $insurance_id[1];
		} else {
			$insurance_id = 0;
		}
		$date_picker_class = (empty($post_val->payment_detail_id) && $post_val->payment_type_ins != 'Adjustment') ? "call-datepicker" : "";
		$eob_attachment_id = Session::has('eob_attachment') ? Session::get('eob_attachment') : "";
		//$payment_alert = (empty($claims_list->insurance_id) && ($post_val->payment_type_ins == 'Payment' || $post_val->payment_type_ins == 'Adjustment'))?Lang::get("practice/patients/payments.validation.paymentmsg"):""; 
		// This alert used when a user try to do insurance payment for a self billed claim
		$payment_alert = '';
    ?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            @if(!isset($from))
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/payments') }}"><i class="fa fa-navicon i-font-tabs"></i> List</a></li> 
            @endif                                              
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-money i-font-tabs"></i> Payment Posting</a></li>
        </ul>
    </div>
    <!-- Tab Ends --> 
    <div class = "js-alert-popupdisable"> </div>
    <div class="box-body-block margin-t-10 no-border-radius"><!--Background color for Inner Content Starts -->       
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border border-green"><!-- General Details Full width Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >
                            <span class="bg-white font600 padding-0-4">General Details</span>
                        </div>
                        <?php 							
							$ins_category = '';
							$ins_id = '';
							//dd($claims_list);
							$claim_insurance_data = @$claims_list->insurance_id;
							if (!empty($post_val->change_insurance_id)) {
								$post_value = explode('-', $post_val->change_insurance_id);
								$ins_category = $post_value[0];
								$ins_id = $post_value[1];
								if ((!empty($claims_list))) {
									$claims_list->insurance_id = $ins_id;
								}
							}
							$other_pmt_ins_cat = '';
							if(!empty($post_val->pmt_post_ins_cat)) {
								$other_pmt_ins_cat = $post_val->pmt_post_ins_cat;	
							}
                        ?>
                        <div class="box-body form-horizontal"><!-- Box Body Starts -->
                            <div class="form-group-billing">                               
                                {!! Form::label('type', 'Billed To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">
                                    {!! Form::select('claim_insurance_id',$insurance_list_total,@$claims_list->insurance_id,['class'=>'select2 form-control', 'id' => 'js-insurance-list', 'onload' => 'changeinsurance(this.val)', 'disabled' => 'disabled']) !!}
                                </div>                                 
                            </div>                                
                            {!! Form::hidden('change_insurance_category',$ins_category) !!}
                            {!! Form::hidden('card_type',$card_type) !!}
                            {!! Form::hidden('changed_insurance_id',$ins_id) !!}                                
							{!! Form::hidden('other_pmt_ins_cat', $other_pmt_ins_cat) !!}                                
                            {!! Form::hidden('patient_paid',@$claims_list->patient_paid) !!}
                            <!-- Hidden variables -->
                            {!! Form::hidden('type','Insurance') !!}
                            {!! Form::hidden('patient_id',$id) !!}
                            {!! Form::hidden('payment_type',@$post_val->payment_type_ins) !!}
                            <?php $claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$claims_list->id, 'encode'); ?>
                            {!! Form::hidden('claim_id',@$claim_id) !!}
                            {!! Form::hidden('insurance_paid',null) !!}
                            {!! Form::hidden('insurance_id',$insurance_id, ['id' => 'js-insurance-id']) !!}
                            {!! Form::hidden('claim_insurance_id',$claim_insurance_data, ['class' => 'js-insurance-data']) !!}
                            {!! Form::hidden('next_insurance_id',null) !!}
                            {!! Form::hidden('insurance_cat',null) !!}
                            {!! Form::hidden('billing_provider_id',@$post_val->privider_id) !!}
                            {!! Form::hidden('claim_paid_amt',null,['id' => 'js-paid-amt']) !!}
                            {!! Form::hidden('balance_amt',null,['id' => 'js-balance']) !!}
                            {!! Form::hidden('adjust_amt',null,['id' => 'js-adjust']) !!}
                            {!! Form::hidden('patient_due',null,['id' => 'js-patientdue']) !!}
                            {!! Form::hidden('insurance_due',null,['id' => 'js-insurancedue']) !!} 
                            {!! Form::hidden('payment_detail_id',@$post_val->payment_detail_id) !!} 
                            {!!Form::hidden('payment_method', "Insurance")!!} 
                            {!! Form::hidden('eob_id',@$eob_attachment_id) !!}  
                            <?php 
                                $ins_pay_mode = ($post_val->payment_type_ins == "Refund")?"Check":$post_val->insur_payment_mode;
                                $label = App\Http\Helpers\Helpers::GetLabelFields($ins_pay_mode); ?>                               
                            <div class="form-group-billing ">
                                {!! Form::label('amt', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">
                                    {!! Form::select('payment_mode', ['' => '--', 'Check' => 'Check','EFT' => 'EFT', 'Credit' => 'CC'],@$ins_pay_mode,['class'=>'select2 form-control', 'disabled' => 'disabled']) !!}
                                    {!!Form::hidden('payment_mode', $ins_pay_mode)!!}
                                </div>
                            </div>
                            <div class="form-group-billing">                               
                                {!! Form::label('amt', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">                                     
                                    {!! Form::text('payment_amt',@$post_val->payment_amt,['class'=>'form-control allownumericwithdecimal input-sm-header-billing', 'readonly' => 'readonly']) !!}
                                </div> 
                            </div> 
                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->
                    <div class = "js-insurance-data-disable">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding tab-l-b-1 border-green"><!--  3rd Content Starts -->                                               
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            </div>
                            <div class="box-body form-horizontal">
                                
                                <div class="form-group-billing">                               
                                    {!! Form::label('Chk No', $label['label_no'], ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">   
                                        {!!Form::hidden('checkexist', null)!!} 
                                        <?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength"); ?>                                 
                                        {!! Form::text('check_no',@$post_val->check_no,['maxlength'=>$lengthval,'data-type'=> 'Insurance','class'=>'form-control input-sm-header-billing js-check-number']) !!}
                                    </div>                               
                                </div>  

                                <div class="form-group-billing">                               
                                    {!! Form::label('Check Date', $label['label_date'], ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
                                        <i class="fa fa-calendar-o form-icon-billing" onclick = "iconclick('check_date')"></i>
                                        {!! Form::text('check_date',@date('m/d/Y', strtotime($post_val->check_date)),['class'=>'form-control input-sm-header-billing dm-date '.$date_picker_class, 'onchange' => 'changeevent(this.name)','maxlength' => 10]) !!}
                                    </div>                                
                                </div> 
                                 <?php   $today_practice = App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), "m/d/Y") ; ?>
                                <div class="form-group-billing">                               
                                    {!! Form::label('Chk No', 'Deposit Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10"> 
                                        <i class="fa fa-calendar-o form-icon-billing" onclick = "iconclick('deposite_date')"></i>
                                        {!! Form::text('deposite_date',$today_practice,['class'=>'form-control input-sm-header-billing dm-date '.$date_picker_class, 'onchange' => 'changeevent(this.name)']) !!}
                                    </div>                               
                                </div>
                            </div>
                        </div><!--  3rd Content Ends -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding tab-l-b-2 md-display border-green"><!--  3rd Content Starts -->                                               
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >

                            </div>
                            <div class="box-body form-horizontal md-m-l-10">                                                       

                                <div class="form-group-billing">
                                    {!! Form::label('Billed Amt', 'Billed', ['class'=>'col-lg-6 col-md-4 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                                        {!! Form::text('tot_billed_amt', @$claims_list->total_charge ,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                    </div>                                   
                                </div>

                                <div class="form-group-billing">
                                    {!! Form::label('Paid', 'Paid', ['class'=>'col-lg-6 col-md-4 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                                        {!! Form::text('tot_paid_amt',@$claims_list->total_paid,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                    </div>                                   
                                </div>

                                <div class="form-group-billing">
                                    {!! Form::label('Balance', 'Balance', ['class'=>'col-lg-6 col-md-4 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">
                                        {!! Form::text('tot_balance_amt',@$claims_list->balance_amt,['readonly' => 'readonly','maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                    </div>                                   
                                </div>

                            </div>
                        </div><!--  3rd Content Ends -->
                        <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding md-display tab-l-b-1 border-green"><!--  4th Content Starts -->
                            <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">
                                <div class="form-group-billing">                               
                                    {!! Form::label('Chk No', 'Posting Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10"> 
                                        <i class="fa fa-calendar-o form-icon-billing"></i>
                                        {!! Form::text('posting_date',  $today_practice,['class'=>'form-control input-sm-header-billing dm-date', 'onchange' => 'changeevent(this.name)', 'readonly' => 'readonly']) !!}
                                    </div>                               
                                </div>  

                                <div class="form-group-billing">                               
                                    {!! Form::label('Ref No', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                        {!! Form::text('reference',@$post_val->insur_reference,['maxlength'=>'20','class'=>'form-control input-sm-header-billing ']) !!}
                                    </div>                                
                                </div> 

                                <div class="form-group-billing">
                                    {!! Form::label('Unapplied', 'Unapplied', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                        {!! Form::text('insurance_unapplied_amt',@$post_val->unapplied,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                        {!! Form::hidden('payment_unapplied_amt',@$post_val->unapplied,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                    </div>                                   
                                </div>
                            </div><!-- /.box-body -->
                        </div><!--  4th Content Ends -->
                    </div>

                    {!!Form::hidden('claim_balance', @$claims_list->balance_amt)!!}   
                </div><!--  Box Ends -->
            </div><!-- Only general details Content Ends -->
        </div><!-- General Details Full width Ends -->
    </div><!-- Only general details Content Ends --> 
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="margin-top:1px;"><!-- Inner Content for full width Starts -->
        <div class="box-body-block no-border"  ><!--Background color for Inner Content Starts -->
            <p class="no-bottom med-green font600">{{$payment_alert}}</p>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">                            
                <ul class="billing js-calculateadjus mobile-width mask" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                    <li class="billing-grid">
                        <table class="table-billing-view">
                            <thead>
                                <tr>
                                    <?php $all_checked_value = ($check_box_count->total_lineitem_count == $check_box_count->active_count) ? "checked" : ""; ?>
                                    <th class="td-c-2">{!!Form::checkbox('active_lineitem','',null, ['class' => 'js_menu', 'id' => 'js_select_all','style'=>'background-color: #ffffff;', $all_checked_value])!!} </th> 
                                    <th class="td-c-6">DOS</th>                                
                                    <th class="td-c-6">CPT</th>
                                    <th class="td-c-6">Billed</th>
                                    <th class="td-c-6">Allowed</th> 
                                    <th class="td-c-6">Balance</th> 
                                    <th class="td-c-6">Ded</th>
                                    <th class="td-c-6">Co-Pay</th>                                
                                    <th class="td-c-6">Co-Ins</th>                                                                
                                    <th class="td-c-6">Adjustments</th>
                                    <th class="td-c-6">Write-Off</th>      
                                    <th class="td-c-6 js-paid-label">Paid</th>                                
                                    <th class="td-c-15">Denial Codes</th> 
                                </tr>
                            </thead>
                        </table>                                     
                    </li>
                    <li class="billing-grid">
                        <table class="table-billing-view superbill-claim">
                            <tbody>

                            </tbody>
                        </table>                                     
                    </li>
                    <div class = "js-disable-div"> 
                        <?php
							$select_ins = !(empty($insurance_lists)) ? array_keys((array) $insurance_lists) : array();
							$ins = [];
							$payment_type = $post_val->payment_type_ins;
							$payment_negative_class = ($payment_type != "Refund") ? "js_need_regex" : "js_avoid_negative";
							foreach ($select_ins as $key => $select_insurance) {
								$search_string = explode('-', $select_insurance);
								$ins[$search_string[0]] = $select_insurance;
							}
							$primary = isset($ins['Primary']) ? $ins['Primary'] : '';
							$seconday = isset($ins['Secondary']) ? $ins['Secondary'] : '';
							$tertiary = isset($ins['Tertiary']) ? $ins['Tertiary'] : '';
							static $active_count = 0;
                        ?> 
                        {!! Form::hidden('primary', $primary)!!}
                        {!! Form::hidden('secondary',$seconday)!!}
                        {!! Form::hidden('tertiary',$tertiary) !!}

                        @if(!empty($claims_list->dosdetails))
                        <?php $count = count($claims_list->dosdetails); ?>
                        @for($i=0;$i<$count;$i++)
                        <?php
                        $date_to = '';
                        $date_from = '';
                        //dd($claims_list);                
                        $balance_amt = $claims_list->balance_amt;
                        if (!empty($claims_list->dosdetails[$i]->dos)) {
                            $date_from = $claims_list->dosdetails[$i]->dos;
                        }
                        $billed = ($claims_list->dosdetails[$i]->charge != '0.00') ? $claims_list->dosdetails[$i]->charge : '';
                        //dd($claims_list->dosdetails[$i]);
                        $allowed = $billed;
                        $patient_paid = $claims_list->dosdetails[$i]->patient_paid;
                        if ($payment_type == "Refund" && $claims_list->is_insurncePaymentHistory)
                            $allowed = $claims_list->dosdetails[$i]->cpt_allowed_amt; // If no allowed amount given          

                        $balance = ($claims_list->dosdetails[$i]->balance != 0 || $claims_list->dosdetails[$i]->paid_amt != 0 || $claims_list->dosdetails[$i]->adjustment != 0 || $claims_list->dosdetails[$i]->adjustment != 0) ? $claims_list->dosdetails[$i]->balance : $allowed;

                        $insur_balance = $claims_list->dosdetails[$i]->insurance_balance;
                        if (isset($ins_id) && !empty($ins_id) && $claims_list->self_pay == "Yes") {
                            $insur_balance = ($insur_balance < 0) ? $insur_balance : $claims_list->dosdetails[$i]->patient_balance + $patient_paid + $claims_list->dosdetails[$i]->patient_adjusted; // Because adjusted not getting added into balance
                        }
                        if ($payment_type == "Refund") {
                            $insur_balance = $claims_list->dosdetails[$i]->balance;
                        }
                        $balance = $insur_balance;
						$insur_balance = number_format(str_replace( ',', '', strip_tags($insur_balance) ),2, '.', '');
                        //$old_balance =  ($claims_list->dosdetails[$i]->balance != 0 || $claims_list->dosdetails[$i]->paid_amt != 0 || $claims_list->dosdetails[$i]->adjustment != 0 || $claims_list->dosdetails[$i]->adjustment != 0) ? $balance:$allowed; // Nil changed
                        $old_balance = ($claims_list->dosdetails[$i]->balance != 0 || $claims_list->dosdetails[$i]->paid_amt != 0 || $claims_list->dosdetails[$i]->adjustment != 0) ? $insur_balance : $allowed; // Nil changed
                        $patient_adj = $claims_list->dosdetails[$i]->patient_adjusted;
                        $patient_adjustment_excluded = $old_balance + $patient_adj;
                        if ($patient_adj != 0 && count((array)$claims_list->is_insurncePaymentHistory) > 0) {
                            $bal_value = $old_balance + $patient_adj;
                            if ($claims_list->dosdetails[$i]->balance > 0 && $bal_value <= $billed || $claims_list->dosdetails[$i]->balance < 0) {
                                $old_balance = $bal_value;
                            } else {
                                $old_balance = $old_balance;
                            }
                            $patient_adjustment_excluded = $old_balance;
                        }
                        //changed for balances display

                        $balance_secondary = ($claims_list->dosdetails[$i]->balance != 0 || $claims_list->dosdetails[$i]->paid_amt != 0 || $claims_list->dosdetails[$i]->adjustment != 0) ? $insur_balance : $allowed;
                        // $balance_secondary=  ($claims_list->dosdetails[$i]->balance != 0 || $claims_list->dosdetails[$i]->paid_amt != 0 || $claims_list->dosdetails[$i]->adjustment != 0) ? $balance:$allowed;              
                        //$insurance_paid = $claims_list->dosdetails[$i]->insurance_paid;               

                        $payment_detail_id = (isset($post_val->payment_detail_id) && !empty($post_val->payment_detail_id) && $payment_type == "Payment" && isset($post_val->search_val) && !empty($post_val->search_val)) ? $post_val->payment_detail_id : "";
                        //dd($payment_detail_id)               ;
						
						// Adjustment code based on cpt  
						$adjustment_reason = array_filter(App\Models\AdjustmentReason::getAdjustmentReason('Insurance','Cpt',$claims_list->dosdetails[$i]->id)); 
							
                        $insurance_paid = \App\Models\Payments\PMTClaimCPTTXV1::getInsurancePaidAmount($insurance_id, $claims_list->dosdetails[$i]->id, 'paid', $payment_detail_id);

                        $insurance_adjusted = \App\Models\Payments\PMTClaimCPTTXV1::getInsuranceAdjesment($insurance_id, $claims_list->dosdetails[$i]->id, 'writeoff', $payment_detail_id);

                        //$insurance_adjusted =0.00;// App\Models\Patients\Payment::getInsurancePaidAmount($insurance_id,$claims_list->dosdetails[$i]->id, 'writeoff');
                        //$allowed_readonly =  ($claims_list->dosdetails[$i]->status == "Paid") ? "readonly":'';
                        //$allowed_readonly  = "readonly = readonly";               
                        $patient_balance = $claims_list->dosdetails[$i]->patient_balance;
                        $insurance_balance = $claims_list->dosdetails[$i]->insurance_balance;
                        //echo $insurance_balance;
                        $line_item_active = ($claims_list->dosdetails[$i]->is_active) ? "yes" : "";
                        $paid = $co_ins = $co_pay = $deductable = $with_held = $adjustment = $default_val = '0.00';
						$code = '';
                        // If no transaction has been done there is no alowed amount provided for line items
                        $allowed = ($claims_list->is_insurncePaymentHistory) ? $allowed : "0.00";
                        //dd($claims_list);
                        $dos_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims_list->dosdetails[$i]->id, 'encode');
                        $denial_code = config::get('siteconfigs.payment.denial_code');
                        //$get_adjustment_payment = App\Models\Patients\PaymentClaimDetail::where('claim_id',@$claims_list->id)->where('patient_paid_amt', ">", 0)->orwhere('total_adjusted', ">", 0)->count();
                        $text_color = "";
                        if (($post_val->payment_type_ins == "Payment") && ($claims_list->is_insurncePaymentHistory)) { 
						?>
                            <li class="billing-grid js-calculate" id = "<?php echo $i ?>">
                                <table class="table-billing-view js-insurance-table superbill-claim">
                                    <tbody>
                                        <tr>
                                            <td class="td-c-2">{!!Form::checkbox('active_lineitem['.$i.']','',$line_item_active, ['class' => 'js_submenu js_active_lineitem', 'data-id' => $i,'id'=>$i])!!}<label for="{{$i}}" class="no-bottom">&nbsp;</label></td>                                  
                                            <td class="td-c-6 p-r-0"><input type="text" class="dm-date billing-noborder" readonly = "readonly" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}" ></td>                                   
                                            <td class="td-c-6"> <input type="text" readonly = "readonly" class="billing-noborder" name= <?php echo "cpt[" . $i . "]"; ?> value = "{{@$claims_list->dosdetails[$i]->cpt_code}}" id="js-cpt-code-{{$i}}"></td>  
                                            <td class="td-c-6"><input type="text" readonly = "readonly" name= <?php echo "cpt_billed_amt[" . $i . "]"; ?> value = "{{$billed}}" style="text-align:right" class="billing-noborder js-cpt-billed"></td>
                                            <?php
												if ($post_val->payment_type_ins == "Refund") {
													$text_color = "style=color:";
												}
                                            ?>
                                            <td class="td-c-6"><input type="text"  name= <?php echo "cpt_allowed_amt[" . $i . "]"; ?> value = "0.00" style="text-align:right" class="allownumericwithdecimal billing-noborder js_avoid_negative js-cpt-allowed" onchange = "calculationnew(<?php echo $i ?>, 'js-cpt-allowed', this);" id = "<?php echo $i; ?>" dataid = "<?php echo $i; ?>"></td>
                                            <td class="td-c-6 readonlytable">
                                                <input readonly = "readonly" name= <?php echo "balance[" . $i . "]"; ?> value = "{{$insur_balance}}" type="text" style="text-align:right"  class="allownumericwithdecimal js-balance billing-noborder js_need_regex">
                                                <input type = "hidden" name= <?php echo "balance_original[" . $i . "]"; ?> value = "{{$balance}}" type="text" style="text-align:right"  class="">
                                                <input name= <?php echo "balance_secondary[" . $i . "]"; ?> value = "{{$balance_secondary}}" type="hidden">
                                                <input name= <?php echo "secondary_ins"; ?> type="hidden">                                
                                                @if(isset($old_balance)) 
                                                <input style="text-align:right" name= <?php echo "old_balance[" . $i . "]"; ?> value = "{{$old_balance}}" type="hidden">
                                                @endif
                                            </td>
                                            <td class="td-c-6"><input name= <?php echo "deductable[" . $i . "]"; ?> value = "{{$default_val}}" type="text" class="allownumericwithdecimal  billing-noborder js_avoid_negative js-deductible" style="text-align:right" onchange = "calculationnew(<?php echo $i ?>, 'js-deductible', this);"></td>
                                            <td class="td-c-6"><input name= <?php echo "co_pay[" . $i . "]"; ?> value = "{{$default_val}}" type="text" class="allownumericwithdecimal  js_avoid_negative billing-noborder js-copay" style="text-align:right" onchange = "calculationnew(<?php echo $i ?>, 'js-copay', this);"></td>
                                            <td class="td-c-6"><input name= <?php echo "co_ins[" . $i . "]"; ?> value = "{{$default_val}}" type="text"  class="allownumericwithdecimal text-right billing-noborder js_avoid_negative js-coins" style="text-align:right" onchange = "calculationnew(<?php echo $i ?>, 'js-coins', this);"></td>
                                            <td class="td-c-6"><input name= <?php echo "with_held[" . $i . "]"; ?> type="text" class="allownumericwithdecimal js-withheld js_avoid_negative billing-noborder" style="text-align:right"  value = "{{$default_val}}" onchange = "calculationnew(<?php echo $i ?>, 'js-withheld', this);" id=<?php echo $i; ?> readonly = 'readonly'>
                                                <?php /*MR-2794 - co253 popup not located in correct position - Pugazh
                                                 * remove style class tooltip-content ,position:absolute
                                                 */?>
                                                <div class=" font600" href="#">
                                                    @if($post_val->payment_type_ins == "Payment")
                                                    <i class="js-withheld-tootltip js_other_adj_toggle fa fa-sticky-note-o fa-5x cur-pointer bill-lblue payments-adj" style="" id=<?php echo $i; ?> data-type="mktoggle", data-toggle="tooltip" title="CO253"></i>
                                                    @endif
                                                    <div class="js_other_adjust payments-adj-content" id = "js_other_adj_{{$i}}" style="">                                                        
                                                        <div class="form-group js_main_div">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 no-padding">
                                                                <div class="col-lg-5">
                                                                    {!! Form::text('adj_reason['.$i.'][]','CO253',['class'=>'form-control input-sm-header-billing', 'readonly']) !!}
                                                                </div>

                                                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 ">
                                                                    {!! Form::text('adj_reson_amount['.$i.'][]',null,['class'=>'form-control input-sm-header-billing js_other_adj allownumericwithdecimal text-right js_need_regex','data-type'=>"adjcalc",'id' => $i]) !!}
                                                                </div>
                                                                
                                                                <div class="col-lg-2 col-md-2 col-sm-4 no-btn-bg p-l-0 p-r-0">
																	<span class="js-addremove-adj js_other_adj_toggle" id={{$i}} data-type="add"><button class="btn bg-white no-padding"><i class="fa fa-plus cur-pointer med-green"></i></button></span>
																	<span class="js-addremove-adj js_other_adj_toggle" id={{$i}} data-type="remove"><button class="btn bg-white no-padding pull-right margin-r-10"><i class="fa fa-minus cur-pointer med-green "></i></button></span>
																</div>
                                                            </div>
                                                            
                                                        </div>

                                                        <div class="form-group js-div-append hide">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 no-padding">
                                                                <div class="col-lg-5 select2-white-popup">
                                                                    {!! Form::select('adj_reason['.$i.'][]',$adjustment_reason,null,['class'=>'select2 form-control text-left']) !!}
                                                                </div>
                                                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10">
                                                                    {!! Form::text('adj_reson_amount['.$i.'][]',null,['class'=>'form-control text-right input-sm-header-billing js_other_adj js_other_adj_toggle allownumericwithdecimal', 'id' => $i, 'data-type'=>"adjcalc"]) !!}
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 no-btn-bg p-l-0 p-r-0">
                                                                    <span class="js-addremove-adj js_other_adj_toggle " id={{$i}} data-type="add"><button class="btn bg-white no-padding"><i class="fa fa-plus cur-pointer med-green"></i></button></span>
                                                                    <span class="js-addremove-adj js_other_adj_toggle" id={{$i}} data-type="remove"><button class="btn bg-white no-padding pull-right margin-r-10"><i class="fa fa-minus cur-pointer med-green "></i></button></span>
                                                                </div>
                                                            </div>
                                                        </div>       

                                                        <div class="form-group js-total-div hide">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                                                <div class="col-lg-5 text-right margin-t-2 med-green">Total</div>
                                                                <div class="col-lg-5">
                                                                    {!! Form::text('other_adj_total['.$i.']',null,['class'=>'form-control text-right input-sm-header-billing allownumericwithdecimal ']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center no-padding margin-t-10">
                                                            <!--  <a id="{{$i}}"class="btn js_other_adj_toggle" data-type="adjsav">Save</a> -->
                                                            <input type="button" value="Save" data-type="adjsav" class="btn btn-medcubics-small js_other_adj_toggle" id="{{$i}}" />
                                                            <input type="button" value="Cancel" data-type="adjcancel" class="btn btn-medcubics-small js_other_adj_toggle" id="{{$i}}" />
                                                            <!-- <a id="{{$i}}"class="btn js_other_adj_toggle" data-type="adjcancel">Cancel</a> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="td-c-6">
                                                <?php if ($post_val->payment_type_ins == "Adjustment"): ?>
                                                    <input name= <?php echo "adjustment[" . $i . "]"; ?> type="text"  value = "{{$default_val}}" class=" billing-noborder js_need_regex js-adjust js_need_regex" style="text-align:right" onchange = "calculationnew(<?php echo $i ?>, 'js-adjust', this);">
                                                <?php else: ?>
                                                    <input name= <?php echo "adjustment[" . $i . "]"; ?> type="text"  value = "{{$default_val}}" class=" billing-noborder allownumericwithdecimal js_need_regex js-adjust"style="text-align:right" onchange = "allowedfromadjustment(<?php echo $i ?>, 'js-adjust', this);" data-paid ="{{$insurance_adjusted}}">
                                                <?php endif; ?>
                                            </td>                                                           
                                            <td class="td-c-6"><input name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$default_val}}" type="text" class="billing-noborder allownumericwithdecimal js_insurance_paid js-paid-amt {{$payment_negative_class}}" @if($payment_type == 'Adjustment' || $payment_type == 'Refund')readonly = "readonly" @endif style="text-align:right" onchange = "calculationpaid(<?php echo $i ?>, 'js-paid-amt', this);" "<?php echo $text_color; ?>"></td>                               
                                            <td class="td-c-15 billing-select2-disabled-white payment-denial1">
                                                <input type="text"  class="payment-denial billing-noborder p-r-5" style="text-align:right;" name= <?php echo "remarkcode[" . $i . "]"; ?> id="js-payment-denial"></td>
											<input name= <?php echo "insurance_paid[" . $i . "]"; ?> value = "{{$insurance_paid}}" type="hidden">
											<input name= <?php echo "patient_adjusted_excluded[" . $i . "]"; ?> value = "{{$patient_adjustment_excluded}}" type="hidden">
											<input name= <?php echo "ids[" . $i . "]"; ?> value = "{{$dos_id}}" type="hidden">
											{!!Form::hidden('allowed_secondary['.$i.']', $allowed)!!}
											{!!Form::hidden('paid_amt_manual['.$i.']', $paid)!!}
											{!!Form::hidden('patient_balance['.$i.']', null, ['class' => 'js-patient-due'])!!}
											{!!Form::hidden('insurance_balance['.$i.']', null, ['class' => 'js-insurance-due'])!!}
											{!!Form::hidden('patient_exist_balance['.$i.']', $patient_balance, ['class' => 'js-patient-due'])!!}
											{!!Form::hidden('insurance_exist_balance['.$i.']',$insurance_balance, ['class' => 'js-insurance-due'])!!}
											{!!Form::hidden('adjustment_new['.$i.']', $adjustment)!!}
											{!!Form::hidden('allowed_takeback_balance['.$i.']')!!}
										</tr>                          
                                    </tbody>
                                </table>                                     
                            </li>
                        <?php } else { ?>
                            <li class="billing-grid js-calculate" id = "<?php echo $i ?>">
                                <table class="table-billing-view js-insurance-table superbill-claim">
                                    <tbody>
                                        <tr>                                                         
                                            <td class="td-c-2">                                
                                                {!!Form::checkbox('active_lineitem['.$i.']',1,$line_item_active, ['class' => 'js_active_lineitem js_submenu', 'data-id' => $i,'id'=>$i])!!}<label for="{{$i}}" class="no-bottom">&nbsp;</label></td>
                                            <td class="td-c-6"><input type="text" class="dm-date billing-noborder" readonly = "readonly" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}" ></td>                                   
                                            <td class="td-c-6"> <input type="text" readonly = "readonly" class="billing-noborder" name= <?php echo "cpt[" . $i . "]"; ?> value = "{{@$claims_list->dosdetails[$i]->cpt_code}}" id="js-cpt-code-{{$i}}"> </td>  
                                            <td class="td-c-6 class-readonly"><input type="text" readonly = "readonly" name= <?php echo "cpt_billed_amt[" . $i . "]"; ?> value = "{{$billed}}" class="billing-noborder class-readonly text-right js-cpt-billed"></td>
                                            @if($post_val->payment_type_ins == "Refund")
                                            <?php $text_color = "style=color:"; ?> 
                                            <td class="td-c-6"><input type="text"  name= <?php echo "cpt_allowed_amt[" . $i . "]"; ?> value = "{{$allowed}}" class="allownumericwithdecimal text-right billing-noborder js_need_regex js-cpt-allowed" onchange = "calculationnew(<?php echo $i ?>, 'js-cpt-allowed', this);" dataid = "<?php echo $i; ?>"></td>
                                            @else
                                            <td class="td-c-6"><input type="text"  name= <?php echo "cpt_allowed_amt[" . $i . "]"; ?> value = "0.00" class="allownumericwithdecimal text-right billing-noborder js_avoid_negative js-cpt-allowed" onchange = "calculationnew(<?php echo $i ?>, 'js-cpt-allowed', this);" id="js-cpt-code-{{$i}}" dataid = "<?php echo $i; ?>"></td>
                                            @endif                                     
                                            <td class="td-c-6 class-readonly">
                                                <input type = "hidden" name= <?php echo "balance_original[" . $i . "]"; ?> value = "{{$balance}}" type="text" style="text-align:right"  class="text-right">
                                                <input readonly = "readonly" name= <?php echo "balance[" . $i . "]"; ?> value = "{{$insur_balance}}" type="text"  class="allownumericwithdecimal text-right js-balance billing-noborder js_need_regex class-readonly"></td>                                
                                            <td class="td-c-6"><input name= <?php echo "deductable[" . $i . "]"; ?> value = "{{$default_val}}" type="text" class="allownumericwithdecimal text-right billing-noborder js_avoid_negative js-deductible" onchange = "calculationnew(<?php echo $i ?>, 'js-deductible', this);"></td>
                                            <td class="td-c-6"><input name= <?php echo "co_pay[" . $i . "]"; ?> value = "{{$default_val}}" type="text" class="allownumericwithdecimal text-right billing-noborder js_avoid_negative js-copay" onchange = "calculationnew(<?php echo $i ?>, 'js-copay', this);"></td>
                                            <td class="td-c-6"><input name= <?php echo "co_ins[" . $i . "]"; ?> value = "{{$default_val}}" type="text"  class="allownumericwithdecimal text-right billing-noborder js_avoid_negative js-coins" onchange = "calculationnew(<?php echo $i ?>, 'js-coins', this);"></td>
                                            <td class="td-c-6"><input name= <?php echo "with_held[" . $i . "]"; ?> type="text" class="js-withheld billing-noborder  text-right @if($post_val->payment_type_ins != "Adjustment") allownumericwithdecimal js_avoid_negative @endif"  value = "{{$default_val}}" onchange = "calculationnew(<?php echo $i ?>, 'js-withheld', this);" id=<?php echo $i; ?>  readonly = 'readonly' >
                                                <?php /*MR-2794 - co253 popup not located in correct position - Pugazh
                                                 * remove style class tooltip-content ,position:absolute
                                                 */?>
                                                <div class=" font600" href="#">
                                                    @if($post_val->payment_type_ins == "Payment" || $post_val->payment_type_ins == "Adjustment")
                                                    <i class="js-withheld-tootltip js_other_adj_toggle fa fa-sticky-note-o fa-5x cur-pointer bill-lblue payments-adj"  id=<?php echo $i; ?> data-type="mktoggle", data-toggle="tooltip" title="CO253"></i>
                                                    @endif
                                                    <div class="js_other_adjust payments-adj-content" id = "js_other_adj_{{$i}}" style="">                                                        
                                                        <div class="form-group js_main_div">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 no-padding">
                                                                <div class="col-lg-5">
                                                                    {!! Form::text('adj_reason['.$i.'][]','CO253',['class'=>'form-control input-sm-header-billing', 'readonly']) !!}
                                                                </div>

                                                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10 ">
                                                                    {!! Form::text('adj_reson_amount['.$i.'][]',null,['class'=>'form-control input-sm-header-billing js_other_adj allownumericwithdecimal text-right js_need_regex','data-type'=>"adjcalc",'id' => $i]) !!}
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2 col-md-2 col-sm-4 no-btn-bg p-l-0 p-r-0">
                                                                <span class="js-addremove-adj js_other_adj_toggle" id={{$i}} data-type="add"><button class="btn bg-white no-padding"><i class="fa fa-plus cur-pointer med-green"></i></button></span>
                                                                <span class="js-addremove-adj js_other_adj_toggle" id={{$i}} data-type="remove"><button class="btn bg-white no-padding"><i class="fa fa-minus cur-pointer med-green "></i></button></span>
                                                            </div>
                                                        </div>

                                                        <div class="form-group js-div-append hide">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-5 no-padding">
                                                                <div class="col-lg-5 select2-white-popup">
                                                                    {!! Form::select('adj_reason['.$i.'][]',$adjustment_reason,null,['class'=>'select2 form-control text-left']) !!}
                                                                </div>
                                                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-10">
                                                                    {!! Form::text('adj_reson_amount['.$i.'][]',null,['class'=>'form-control text-right input-sm-header-billing js_other_adj js_other_adj_toggle allownumericwithdecimal', 'id' => $i, 'data-type'=>"adjcalc"]) !!}
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 no-btn-bg p-l-0 p-r-0">
                                                                    <span class="js-addremove-adj js_other_adj_toggle " id={{$i}} data-type="add"><button class="btn bg-white no-padding"><i class="fa fa-plus cur-pointer med-green"></i></button></span>
                                                                    <span class="js-addremove-adj js_other_adj_toggle" id={{$i}} data-type="remove"><button class="btn bg-white no-padding"><i class="fa fa-minus cur-pointer med-green "></i></button></span>
                                                                </div>
                                                            </div>
                                                        </div>       

                                                        <div class="form-group js-total-div hide">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                                                <div class="col-lg-5 text-right margin-t-2 med-green">Total</div>
                                                                <div class="col-lg-5">
                                                                    {!! Form::text('other_adj_total['.$i.']',null,['class'=>'form-control text-right input-sm-header-billing allownumericwithdecimal other_adj_total']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center no-padding margin-t-10">
                                                            <!--  <a id="{{$i}}"class="btn js_other_adj_toggle" data-type="adjsav">Save</a> -->
                                                            <input type="button" value="Save" data-type="adjsav" class="btn btn-medcubics-small js_other_adj_toggle" id="{{$i}}" />
                                                            <input type="button" value="Cancel" data-type="adjcancel" class="btn btn-medcubics-small js_other_adj_toggle" id="{{$i}}" />
                                                            <!-- <a id="{{$i}}"class="btn js_other_adj_toggle" data-type="adjcancel">Cancel</a> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="td-c-6">
                                                <?php if ($post_val->payment_type_ins == "Adjustment"): ?>
                                                    <input name= <?php echo "adjustment[" . $i . "]"; ?> type="text"  value = "{{$default_val}}" class="js_need_regex allownumericwithdecimal text-right billing-noborder js-adjust js_need_regex" onchange = "calculationnew(<?php echo $i ?>, 'js-adjust', this);">                               
                                                <?php else: ?>
                                                    <input name= <?php echo "adjustment[" . $i . "]"; ?> type="text"  value = "{{$default_val}}" class=" billing-noborder allownumericwithdecimal text-right js-adjust js_need_regex" onchange = "allowedfromadjustment(<?php echo $i ?>, 'js-adjust', this);" data-paid ="{{$insurance_adjusted}}">
                                                <?php endif; ?>
                                            </td>                                                             
                                            <td class="td-c-6"><input name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$default_val}}" type="text" class="billing-noborder {{$payment_negative_class}} allownumericwithdecimal text-right js_insurance_paid js-paid-amt" onchange = "calculationpaid(<?php echo $i ?>, 'js-paid-amt', this);" <?php echo $text_color; ?>></td>                              
                                            @if(isset($old_balance)) 
											<input name= <?php echo "old_balance[" . $i . "]"; ?> value = "{{$old_balance}}" type="hidden">
											@endif 
											@if($claims_list->is_insurncePaymentHistory)
											<input name= <?php echo "balance_secondary[" . $i . "]"; ?> value = "{{$balance_secondary}}" type="hidden">
											<input name= <?php echo "secondary_ins"; ?> type="hidden">
											@endif  
											{!!Form::hidden('adjustment_new['.$i.']', $adjustment)!!}
											{!!Form::hidden('allowed_secondary['.$i.']', $allowed)!!}
											{!!Form::hidden('paid_amt_manual['.$i.']', $paid)!!}
											{!!Form::hidden('patient_balance['.$i.']', null, ['class' => 'js-patient-due'])!!}
											{!!Form::hidden('insurance_balance['.$i.']', null, ['class' => 'js-insurance-due'])!!}
											{!!Form::hidden('insurance_adjusted['.$i.']', $insurance_adjusted)!!}
											<input name= <?php echo "insurance_paid[" . $i . "]"; ?> value = "{{$insurance_paid}}" type="hidden">
											<td class="td-c-15 billing-select2-disabled-white"><input type="text"  name= <?php echo "remarkcode[" . $i . "]"; ?> class="payment-denial billing-noborder p-r-5" style="text-align:right" id="js-payment-denial"></td>
											<input name= <?php echo "ids[" . $i . "]"; ?> value = "{{$dos_id}}" type="hidden">
											<input name= <?php echo "patient_adjusted_excluded[" . $i . "]"; ?> value = "{{$patient_adjustment_excluded}}" type="hidden">
										</tr>                          
                                    </tbody>
                                </table>                                     
                            </li>
                        <?php } ?>
                        @endfor
                    </div>
                    <li class="billing-grid">
                        <table class="table-billing-view superbill-claim">
                            <tbody>
                                <tr>       
                                    <td class="td-c-2"><span  class="med-green font600"></span></td>
                                    <td class="td-c-6"><span  class="med-green font600"></span></td>
                                    <td class="td-c-6"><span class="med-green font600"></span></td>
                                    <td class="td-c-6" style="text-align:right"><span id = "js-cpt-billed" class="med-green text-right font600"> 00.00</span></td>
                                    <td class="td-c-6" style="text-align:right"><span id = "js-cpt-allowed" class="med-green font600"> 00.00</span></td>
                                    <td class="td-c-6" style="text-align:right"><span id = "js-balance" class="med-green font600"> 00.00</span></td> 
                                    <td class="td-c-6" style="text-align:right"><span id = "js-deductible" class="med-green font600"></span></td> 
                                    <td class="td-c-6" style="text-align:right"><span id = "js-copay" class="med-green font600"></span></td> 
                                    <td class="td-c-6" style="text-align:right"><span id = "js-coins" class="med-green font600"></span></td> 
                                    <td class="td-c-6" style="text-align:right"><span id = "js-withheld" class="med-green font600"></span></td>
                                    <td class="td-c-6" style="text-align:right"><span id = "js-adjust" class="med-green font600"></span></td> 
                                    <td class="td-c-6" style="text-align:right"><span id="js-paid-amt" class="med-green font600"></span></td> 
                                    <td class="td-c-15"></td>                            
                                </tr>
                            </tbody>
                        </table>                                     
                    </li>
                    @endif
                </ul>  
                <span class="js-display-error" style="display:none;">Can't make adjustment when balance was in zero or negative</span>          
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 bg-aqua yes-border border-brown padding-10">
                <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-b-m-8 form-horizontal ">
                    <div class="form-group-billing">
                        {!! Form::label('Responsibility', 'Next Responsibility', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                        <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::select('next_responsibility', array(''=>'-- Select --')+(array)$insurance_lists+array('patient' => 'Patient'),null,['class'=>'select2 form-control', 'id' => 'js-insurance']) !!}
                        </div>                                   
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Claim Status', 'Claim Status', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                        <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">                                    
                            {!! Form::select('status', [''=>'-- Select --','pending'=>'Pending','Denied' => 'Denied'],null,['class'=>'select2 form-control', 'id'=>'js_claim_status']) !!}
                        </div>                                   
                    </div>
                    <div class="form-group-billing">
                        {!! Form::label('Unapplied', 'Hold', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                        <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">  
							<?php 
								$pat_pmt_hold_reason = $pat_hold_reason = $pat_hold_release_date = null; 
								if(!empty($claims_list->patient)) {
									$pat_pmt_hold_reason = (isset($claims_list->patient->statements) && $claims_list->patient->statements == 'Hold') ? 'patient': null;
									$pat_hold_reason = (isset($claims_list->patient->hold_reason) && $claims_list->patient->hold_reason != '') ? $claims_list->patient->hold_reason: null;
									$pat_hold_release_date = (isset($claims_list->patient->hold_release_date) && $claims_list->patient->hold_release_date != '') ? App\Http\Helpers\Helpers::checkAndDisplayDateInInput($claims_list->patient->hold_release_date): null;
								}	
							?>
                            {!! Form::select('payment_hold_reason', [''=>'-- Select --','insurance' => 'Insurance Pending','patient' => 'Statement hold'],$pat_pmt_hold_reason,['class'=>'select2 form-control', 'id'=>'js_hold_reason']) !!}
                        </div>                                   
                    </div>
                    <!-- Statement hold reason block start -->
                    <div class="form-group-billing">
                        {!! Form::label('hold_reason', 'Hold Reason', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                        <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::select('hold_reason', array(''=>'-- Select --' )+(array)@$stmt_holdreason, @$pat_hold_reason, ['class'=>'select2 form-control js_hold_block','id'=>'hold_reason']) !!}
                        </div>
                    </div>
                    <div class="form-group-billing ">
                        {!! Form::label('hold_release_date', 'Hold Release Date', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                        <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                            {!! Form::text('hold_release_date', @$pat_hold_release_date, ['id'=>'hold_release_date','class'=>'form-control input-sm-header-billing form-cursor dm-date js_hold_block','placeholder'=>Config::get('siteconfigs.default_date_format'),'id'=>'hold_release_date']) !!}
                        </div>
                    </div> 
                    <!-- Statement hold reason block ends -->
                </div>
                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding tab-l-b-1 tab-r-b-1 border-brown md-display"><!-- VOB Col starts -->
                    <div class="box box-view no-shadow no-border-radius margin-t-m-10 no-bottom no-border bg-aqua margin-t-xs-20"><!-- VOB Box starts -->
                        <div class="box-header-view-white bg-aqua no-border-radius margin-t-5">
                            <i class="livicon" data-name="responsive-menu"></i> <span class="med-green font600 font12">Denial/Remark Codes</span>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive chat pymt-codes margin-t-m-5"><!-- VOB Box Body starts -->

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive js-remark-append">                                        
                            </div>                                                            
                        </div><!-- VOB box-body Ends-->
                    </div><!-- VOB box Ends -->
                </div><!-- VOB COl Ends -->

                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 m-b-m-8   md-display border-brown">
                    <p class="margin-t-0"><span class="med-green font600">Patient Balance </span><span class="pull-right font600 med-gray-dark js-patientdue">{!!App\Http\Helpers\Helpers::priceFormat("0.00")!!} </span></p>
                    <p class="margin-t-m-3"><span class="med-green font600">Insurance Balance </span> <span class="pull-right font12 font600 med-gray-dark js-insurancedue">{!!App\Http\Helpers\Helpers::priceFormat("0.00")!!}</span></p>
                    <p class="margin-t-m-3 margin-b-4"><span class="med-green font600">Total Balance</span> <span class="pull-right font12 font600 med-orange js-totaldue ">{!!App\Http\Helpers\Helpers::priceFormat("0.00")!!}</span></p>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 charge-notes-bg margin-t-10">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                    {!! Form::text('content',null,['maxlength'=>'150','class'=>'form-control','placeholder'=>'Notes']) !!}
                </div>

                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1"> </div>
                @if(!empty($claims) && ($claims->self_pay == "Yes" ))
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 m-t-sm-5 margin-t-4 p-r-0 text-right font600">
                    <?php $yes_no = ($claims->is_send_paid_amount == "Yes") ? true : false; ?> 
                    {!! Form::checkbox('is_send_paid_amount', null, $yes_no, ['class' => '','id'=>'wo-paid-amount']) !!} <label for="wo-paid-amount" class="med-green font600 cur-pointer">Send claim without paid amount</label>
                </div>
                @endif

            </div>

        </div><!-- Inner Content for full width Ends -->
    </div><!--Background color for Inner Content Ends -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Inner Content for full width Starts -->
        <div class="box-body-block p-t-0"><!--Background color for Inner Content Starts -->

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">     

                <div id = "view_transaction" class="collapse out col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Inner Content for full width Starts -->
                    <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive tabs-border">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange padding-0-4 font600"> CPT Transaction</span>
                            </div>
                            <table class="popup-table-wo-border table table-responsive margin-t-10">                    
                                <thead>
                                    <tr> 
                                        <th></th>
                                        <th>CPT</th>
                                        <th>Trans Date</th>
                                        <th>Responsibility</th>                                             
                                        <th>Description</th>                                    
                                        <th class="text-right">Charges</th>
                                        <th class="text-right">Payments</th>
                                        <th class="text-right">Adj</th>                                     
                                        <th class="text-right">Pat Bal</th>
                                        <th class="text-right">Ins Bal</th>                                          
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
										$j = 1;
										$payment = 1;
										$cpttransactiondetails = isset($claims_list->cpttransactiondetails) ? $claims_list->cpttransactiondetails : [];
									?>                              
									@foreach($cpttransactiondetails as $cptKey => $cpttx)
									<?php
										$cpt_code = $cpttx;
										$style = $dmStyle = "";
										if ($j == 1 && count($cpttx) > 1) {
											$toggler = "toggle-minus";
											$dmStyle = "style = display:none";
										} elseif ($j != 1 && count($cpttx) > 1) {
											$toggler = "toggle-plus";
											$style = "style = display:none";
										} else {
											$toggler = "";
										}
										$i = 0;
										$lTxn = end($cpttx);
										$fTxn = isset($cpttx[0]) ? $cpttx[0] : [];
                                    ?>                                  
                                    @if(count($cpttx) > 1)
                                    <!-- Dummy block start -->
                                    <tr class="blk_{{$j}} med-l-green-bg" {{$dmStyle}} >
                                        <td><!-- Don't remove this inline style. It will affect in safari browser for + icon.  -->
                                            <a href="#" class="txtoggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a>
                                        </td>
                                        <td>{{@$fTxn->cpt_code}}</td>
                                        <td>{{@date('m/d/y', strtotime($lTxn->txn_date))}}</td>
                                        <td>{!! $lTxn->responsibility !!}</td>
                                        <td>
											{!! nl2br($lTxn->description) !!}
											@if(isset($lTxn->resp_category) && $lTxn->resp_category != '')
												<span class="{{ @$lTxn->resp_bg_class }}">{{ substr($lTxn->resp_bg_class, 0, 1) }}</span>
											@endif										
										</td>
                                        <td class="text-right">{!! $fTxn->charges !!}</td>
                                        <td class="text-right">{!! $lTxn->payments !!}</td>
                                        <td class="text-right">{!! $lTxn->adjustment!!}</td>
                                        <td class="text-right">{!! $lTxn->pat_balance!!}</td>
                                        <td class="text-right">{!! $lTxn->ins_balance!!}</td>
                                    </tr>
                                    <!-- Dummy block end -->
                                    @endif

                                    @foreach($cpttx as $ctxn)
                                    <?php
										//$cpt_transaction_count = count($cpttx);
										$payment = 0;
                                    ?>
                                    <tr class="blk_{{$j}} med-l-green-bg" {{$style}}>
                                        <td>
                                            @if($i == 0)
                                            <a href="#" class="txtoggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span></a>
                                            @endif
                                        </td>
                                        <td>@if($i == 0){{@$ctxn->cpt_code}}@endif</td>
                                        <td>{{@date('m/d/y', strtotime($ctxn->txn_date))}}</td>
                                        <td>
											{!! $ctxn->responsibility !!}
											@if(isset($ctxn->resp_category) && $ctxn->resp_category != '')
												<span class="{{ @$ctxn->resp_bg_class }}">{{ substr($ctxn->resp_bg_class, 0, 1) }}</span>
											@endif
										</td>
                                        <td>{!! nl2br($ctxn->description) !!}</td>
                                        <td class="text-right">{!! $ctxn->charges !!}</td>
                                        <td class="text-right">{!! $ctxn->payments !!}</td>
                                        <td class="text-right">{!! $ctxn->adjustment!!}</td>
                                        <td class="text-right">{!! $ctxn->pat_balance!!}</td>
                                        <td class="text-right">{!! $ctxn->ins_balance!!}</td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach

                                    <?php $j++; ?>
                                    @endforeach 
                                    @if($payment)
										<td colspan="10"><p class="text-center no-bottom med-gray margin-t-10">No payment has been done</p></td>
									@endif                                        
                                <?php $patient_paid_amt = App\Models\Payments\ClaimInfoV1::getPatientPaidAmt(@$claims_list->id); ?>
                                @if($patient_paid_amt != 0)
                                <p class="no-bottom margin-t-15 font600"><span class="med-green">Patient Paid : </span> <span class="med-orange">{{$patient_paid_amt}}</span></p>
                                @endif                                          
                                </tbody>
                            </table>
                        </div>
                    </div><!-- Inner Content for full width Ends -->
                </div><!--Background color for Inner Content Ends -->                             

                <div class="payment-links pull-right margin-t-5">
                    <ul class="nav nav-pills">
                        @if(isset($claims_list->cpttransactiondetails) && !empty($claims_list->cpttransactiondetails))
							<li><a data-toggle = "collapse" data-target = "#view_transaction" > <i class="fa fa-file-text-o"></i> View Transaction</a></li>
                        @endif
                        <?php
							$url = url('patients/'.$id.'/billing/create/'.$claim_id);
							$problem_list = url('patients/'.$id.'/payment/'.@$claims_list->claim_number.'/problem/create');
						?>
                        <!--<li><a target = "_blank" href = "{{$url}}">  <i class="fa fa-pencil"></i> Edit Charges</a></li>-->
                        <li class="hide"><a data-index="ledger" data-id ="{{ @$claims_list->claim_number }}" class="claim_assign_all_link form-cursor claimotherdetail font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a></li>
                        {!! Form::hidden('adjustment_reason', @$post_val->adjustment_reason)!!}
                        {!! Form::hidden('resubmit', null)!!}
                        {!! Form::hidden('next', null)!!}
                        <li><a onClick="window.open('{{ url('getcmsform/'.$claim_id) }}', '_blank')"><i class="fa fa-file-pdf-o"></i> CMS 1500</a></li>                                                                        
                        @if(!empty($final))
                        <li><a><i class="fa fa-arrow-right"></i>{!! Form::submit('Next',['class' => 'js-next no-border bg-white', 'accesskey'=>'x']) !!}</a></li>
                        @endif
                    </ul>
                </div>     

            </div>        

        </div><!-- Inner Content for full width Ends -->
    </div><!--Background color for Inner Content Ends --> 

    <div class="box-footer space20">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 selFnBtn margin-t-10 margin-b-10">
            {!! Form::submit('Save', ['class'=>'btn btn-medcubics js-save', 'accesskey'=>'s']) !!}
            <?php
				$url = (isset($from) && $from == "mainpayment") ? url('payments') : url('patients/' . $id . '/payments');
				$cancel_payment_class = ($post_val->payment_type_ins == "Payment" || $post_val->payment_type_ins == "Refund") ? "js-cancel-payment" : "";
            ?>
            <a class ="js-cancel" href="{{ $url}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics '.$cancel_payment_class, 'accesskey'=>'c']) !!}</a>
        </div>
    </div><!-- /.box-footer -->
	<!-- Patient Alert Notes Block Start -->	
	@if(!empty(@$patient_alert_note))
		<?php $pat_alert_note = @$patient_alert_note->content; ?>
		<span id="showmenu" class="cur-pointer alertnotes-icon"><i class="fa fa-bell med-orange"></i></span>
		<div class="snackbar-alert success menu">
			<h5 class="med-orange margin-b-5 margin-l-15 margin-t-6"><span>Alert Notes</span> <span class="pull-right cur-pointer" ><i class="fa fa-times" id="showmenu1"></i></span></h5>            
			<p>{!! $pat_alert_note !!}</p>
		</div>
	@endif
	<!-- Patient Alert Notes Block End -->

    <?php $remark_code = json_encode($remarkcode); ?>
    <script type="text/javascript">
         window.remark = '<?php echo "$remark_code"; ?>';
		 if (typeof changelabel === "function") { 
			changelabel(); // Called this function for paid amount label change for refund check issue.
		 }
		var today_practice = '<?php echo $today_practice; ?>'; 
    </script>