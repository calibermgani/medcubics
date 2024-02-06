<div class="col-md-12"><!-- Inner Content for full width Starts -->
    <!-- Tab Starts  -->    
    <?php 
		$activetab = 'payments_list'; 
		$routex = explode('.',Route::currentRouteName());
		$id = isset($id)?$id:"";
		$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims_lists->id, 'encode');
	?>

    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            @if(!isset($from))
            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/payments') }}"><i class="fa fa-navicon i-font-tabs"></i> List</a></li>
            @endif                                               
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-money i-font-tabs"></i> Payment Posting</a></li>
            @if($post_val['payment_type'] == "Payment" || $post_val['payment_type'] == "Credit Balance") 
            <li class="pull-right"><a accesskey="a" href = "javascript:void(0)" class="js-autopost  pull-right"><span class="btn btn-medcubics-small margin-t-m-4">Auto Post</span></a></li> 
            @endif            
        </ul>
    </div>
    <?php    
        $id = (!is_null($id))?$id:$post_val['patient_id'];  
        $credit_balance_val = App\Models\Patients\Patient::getPatienttabData($id);
        $credit_balance = (!is_null($id) && !empty($credit_balance_val) && is_array(($credit_balance_val))) ? $credit_balance_val['wallet_balance']:"0.00"; 
        //$credit_balance = !is_null($id)?App\Models\Patients\Payment::getPateintWalletCredit($id):"";  
        $final = [];
        $curr = array($claim_id);
        $claim_ids = $post_val['claim_ids'];
        $post_arra = explode(',',$post_val['claim_ids']);
        $final = array_diff($post_arra, $curr);      
        $final = implode(',',array_filter($final));             
        $post_val['claim_ids'] = $final;
        if(count(explode(',',$claim_ids))>1){
            Session::put('post_val',$post_val);
        }else{
            Session::forget('post_val');
        }  
        if($post_val['payment_type'] == "Refund")
            $post_val['payment_mode'] = "Check";
        $post_val = (object) $post_val;       
        $both_disabled = '';
        $check_disabled = '';
        $credit_disabled = $ref_disabled = '';
        $credit_disabled_select = 'disabled';
        $amount_disabled = '';
        $refund_amt_disabled = 'readonly';
        $money = "readonly";
        $calender_class_check = '';
        $calender_class_credit = '';
        $payment_negative_class = '';
        $unapplied_amt = $post_val->payment_amt_calc;        
        if(isset($post_val->payment_mode) &&($post_val->payment_mode == 'Check' || $post_val->payment_mode == 'Money Order')){
            $credit_disabled = "readonly";
            $check_disabled = ($post_val->payment_mode == 'Money Order')?"readonly":"";
            $calender_class_check = "js-auth_datepicker";
        }elseif(isset($post_val->payment_mode) && $post_val->payment_mode == 'Credit'){
            $check_disabled = "readonly";
            $calender_class_credit = "js-payment_datepicker";
            $credit_disabled_select = '';
        } else {
            $check_disabled = "readonly";
            $credit_disabled = "readonly";
        }
        if($post_val->payment_type == "Adjustment" || $post_val->payment_type == "Credit Balance"){
            $check_disabled = "readonly";
            $credit_disabled = "readonly";
            $post_val->payment_mode = "";
            $calender_class_check ='';
            if($post_val->payment_type == "Credit Balance"){
                 $calender_class_check = '';
                $calender_class_credit = '';
                $amount_disabled = $ref_disabled = "readonly";
                 $unapplied_amt = $credit_balance;
            }
            $post_val->payment_amt_calc = $credit_balance;           
                
        }elseif($post_val->payment_type == "Refund")  {
            $unapplied_amt  = $post_val->payment_amt_calc - @$post_val->wallet_refund; 
            $payment_negative_class = "js_avoid_negative";                     
           $refund_amt_disabled = '';
        } 
        $eob_attachment_id = Session::has('eob_attachment')?Session::get('eob_attachment'):"";                  
    ?>
    <!-- Tab Ends --> 
    <div class = "js-alert-popupdisable"> </div>  
    <div class="box-body-block padding-t-20 no-border-radius margin-t-10"><!--Background color for Inner Content Starts -->   
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding border-green"><!-- General Details Full width Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow js-paymenttakebackdisable"><!-- Box Starts -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 right-border border-green"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >
                            <span class="bg-white font600 padding-0-4">General Details</span>
                        </div>
                        <div class="box-body form-horizontal"><!-- Box Body Starts -->                                                       
                            <div class="form-group form-group-billing">
                                {!! Form::label('type', 'Type', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                                    {!! Form::select('payment_type', ['Payment' => 'Payment','Refund' => 'Refund','Adjustment' => 'Adjustment', 'Credit Balance' => 'Credit Balance'],@$post_val->payment_type,['class'=>'select2 form-control', 'id' => 'js-pay-type', 'disabled' => 'disabled']) !!}
                                    {!!Form::hidden('payment_type', $post_val->payment_type)!!}
                                </div>                                 
                            </div> 
                             {!! Form::hidden('payment_amt',@$post_val->payment_amt_calc) !!}
                             {!! Form::hidden('credit_balance',@$credit_balance) !!}
                             {!! Form::hidden('eob_id',@$eob_attachment_id) !!} 
                             {!! Form::hidden('takeback',@$post_val->takeback) !!} 
                            <div class="js-disable-amount">
                                <div class="form-group form-group-billing">                               
                                    {!! Form::label('amt', 'Amount', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">                                     
                                        {!! Form::text('payment_amt',(@$post_val->takeback == 1?@$post_val->payment_amt_pop:@$post_val->payment_amt_calc),['class'=>'form-control allownumericwithdecimal input-sm-header-billing', $amount_disabled, 'accesskey'=>'g']) !!}
                                    </div> 
                                </div>
                            <div class="form-group form-group-billing ">
                                {!! Form::label('amt', 'Mode', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                                    {!! Form::select('payment_mode', ['' => '--', 'Check' => 'Check','Cash' => 'Cash','Money Order' => 'Money Order','Credit' => 'Credit Card'],@$post_val->payment_mode,['class'=>'select2 form-control js-pay-mode', 'disabled' => 'disabled']) !!}
                                </div> 
                                {!! Form::hidden('payment_mode',@$post_val->payment_mode) !!}
                            </div>
                             </div>
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('ref', 'Reference', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('reference',@$post_val->reference,['class'=>'form-control input-sm-header-billing', 'maxlength' => 20, $ref_disabled]) !!}
                                </div> 
                            </div>
                            <?php $adjustment_reason = App\Models\AdjustmentReason::getAdjustmentReason('Patient');?>                                        
                            <div class="form-group js-hide-adjustment">
                                {!! Form::label('adjustment Reason', 'Adj Reason', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label med-green','style'=>'font-weight:600;']) !!}     
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">                                    
                                {!! Form::select('adjustment_reason', array('' => '--')+$adjustment_reason,@$post_val->adjustment_reason,['class'=>'select2 form-control js-adjustment']) !!}
                                 {!! Form::hidden('adjustment_reason',@$post_val->adjustment_reason) !!}   
                                </div>
                            </div>
                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->                    
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding "><!--  3rd Content Starts -->                                               
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >
                            <span class="bg-white font600 padding-0-4">Check Details</span>
                        </div>
                        <div class="box-body form-horizontal js-checkdetail">     
                            <?php  
                                $pmt_mode = $post_val->payment_mode;
                                $label = App\Http\Helpers\Helpers::GetLabelFields($pmt_mode);  
                            ?>                        
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('Check No', $label['label_no'], ['class'=>'col-lg-4 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-10">
                                {!!Form::hidden('checkexist', null)!!}  
                                    <?php $lengthval = Config::get("siteconfigs.payment.check_no_maxlength"); ?>
                                    @if($pmt_mode == "Check")    
                                    {!! Form::text('check_no',@$post_val->check_no,['maxlength'=>$lengthval, 'data-type'=> 'Patient','class'=>'js-check-number form-control input-sm-header-billing' ,$check_disabled, 'accesskey'=>'k']) !!}
                                    @else
                                    {!! Form::text('money_order_no',@$post_val->money_order_no,['maxlength'=>$lengthval, 'data-type'=> 'Patient','class'=>'form-control input-sm-header-billing' ,$check_disabled, 'accesskey'=>'k']) !!}
                                    @endif
                                </div>                               
                            </div>  

                            <div class="form-group form-group-billing">                               
                                {!! Form::label('Check Date', $label['label_date'], ['class'=>'col-lg-4 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-10">  
                                    
                                    @if($pmt_mode == "Check") 
                                    <i class="fa fa-calendar-o form-icon-billing" onclick = "iconclick('check_date')"></i>
                                    {!! Form::text('check_date',@$post_val->check_date,['maxlength' => 10,'class'=>'form-control dm-date input-sm-header-billing '.$calender_class_check, $check_disabled,'maxlength' => 10]) !!}
                                    @else
                                    <i class="fa fa-calendar-o form-icon-billing" onclick = "iconclick('money_order_date')"></i>
                                    {!! Form::text('money_order_date',@$post_val->money_order_date,['maxlength'=>'25','class'=>'form-control dm-date input-sm-header-billing '.$calender_class_check, $check_disabled]) !!}
                                    @endif
                                </div>                                
                            </div>
                           
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('Bank Name', 'Bank Name', ['class'=>'col-lg-4 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('bankname',@$post_val->bankname,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', $check_disabled]) !!}
                                </div>                                                           
                            </div>  
                             <!--
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('Branch', 'Branch', ['class'=>'col-lg-4 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('bank_branch',@$post_val->branch,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', $check_disabled]) !!}
                                </div>                                
                            </div>June 16 2016-->                            
                            
                             <div class="form-group form-group-billing">                               
                                {!! Form::label('', '', ['class'=>'col-lg-4 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-6 col-sm-6 col-xs-10 margin-b-4">
                                    &emsp;
                                </div>                                
                            </div>  
                             
                        </div>
                    </div><!--  3rd Content Ends -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding tab-l-b-1 md-display border-green"><!--  3rd Content Starts -->                                               
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >
                            <span class="bg-white font600 padding-0-4">Card Details</span>
                        </div>
                        <div class="box-body form-horizontal js-carddetail">
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('Chk No', 'Card Type', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
                                {!! Form::select('card_type', ['' => '--','Visa Card' => 'Visa Card','Master Card' => 'Master Card','Maestro Card' => 'Maestro Card','Gift Card' => 'Gift Card'],@$post_val->card_type,['class'=>'select2 form-control',$credit_disabled_select, 'accesskey'=>'d']) !!}
                                {!! Form::hidden('card_type',@$post_val->card_type,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                               
                            </div>
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('Chk No', 'Card No', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('card_no',@$post_val->card_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing ', $credit_disabled]) !!}
                                </div>                                
                            </div>
                            <div class="form-group form-group-billing">                               
                                {!! Form::label('acct_no', 'Name on Card', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600 p-r-0']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('name_on_card',@$post_val->name_on_card,['maxlength'=>'25','class'=>'form-control input-sm-header-billing', $credit_disabled]) !!}
                                </div>                                
                            </div>
                            <div class="form-group form-group-billing js-datepick">                               
                                {!! Form::label('expiry_dt', 'Expiry Date', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10">
                                    <i class="fa fa-calendar-o form-icon-billing" onclick = "iconclick('cardexpiry_date')"></i>
                                    {!! Form::text('cardexpiry_date',(isset($post_val->cardexpiry_date) && $post_val->cardexpiry_date != "0000-00-00" && $post_val->cardexpiry_date != "01/01/1970")?@$post_val->cardexpiry_date:null,['maxlength'=>'25','class'=>'form-control dm-date input-sm-header-billing '.$calender_class_credit, $credit_disabled]) !!}
                                </div>                                
                            </div>
                            <div class="form-group js-hide-adjustment">
                                {!! Form::label('', '', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label med-green','style'=>'font-weight:600;']) !!}     
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    &emsp;
                                </div>
                            </div> 
                        </div>
                    </div><!--  3rd Content Ends -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding tab-l-b-1 md-display border-green"><!--  4th Content Starts -->
                        <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">
                            <div class="form-group form-group-billing">
                                {!! Form::label('Billed Amt', 'Billed', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">                                    
                                    {!! Form::text('tot_billed_amt', @$claims_lists->total_charge ,['readonly' => 'readonly','maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>
                            <div class="form-group form-group-billing">
                                {!! Form::label('Paid', 'Paid', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('tot_paid_amt', @$claims_lists->total_paid,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>
                            <div class="form-group form-group-billing">
                                {!! Form::label('Balance', 'Balance', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('tot_balance_amt',@$claims_lists->balance_amt,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>
                            <div class="form-group form-group-billing">
                                {!! Form::label('Unapplied', 'Unapplied', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">                                    
                                    {!! Form::text('unapplied_amt',@$unapplied_amt,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
                                    {!! Form::hidden('pay_amt',$post_val->payment_amt_calc,['class'=>'form-control allownumericwithdecimal input-sm-header-billing']) !!}
                                    {!! Form::hidden('payment_amt_calc',@$unapplied_amt,['class'=>'form-control allownumericwithdecimal input-sm-header-billing']) !!}
                                </div>                                   
                            </div> 
                            
                            <div class="form-group js-hide-adjustment">
                                {!! Form::label('Walelt refund', 'Wallet Refund', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label med-green','style'=>'font-weight:600;']) !!}     
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
									<?php 
										$wallet_refund_amt = 1*@$post_val->wallet_refund;
										$wallet_refund_amt = number_format($wallet_refund_amt,2);     
									?>
									{!! Form::text('wallet_refund',$wallet_refund_amt,['class'=>'form-control input-sm-header-billing js_need_regex jsrefund allownumericwithdecimal', $refund_amt_disabled]) !!}
                                </div>
                            </div> 
                              {!! Form::hidden('patient_id', $id) !!} 
                              {!! Form::hidden('payment_method', "Patient") !!}
                              {!! Form::hidden('claim_id',@$claim_id) !!}
                        </div><!-- /.box-body -->

                    </div><!--  4th Content Ends -->

                </div><!--  Box Ends -->
            </div><!-- Only general details Content Ends -->
        </div><!-- General Details Full width Ends -->
        
    </div><!-- Only general details Content Ends -->  

</div> 
 <div class="col-md-12 p-t-2"><!-- Inner Content for full width Starts -->
    <div class="box-body-block no-border"  ><!--Background color for Inner Content Starts -->        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">            
            <ul class="billing js-calculateadjus mobile-width" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                <li class="billing-grid">
                    <table class="table-billing-view">
                        <thead>
                            <tr>
                                <th class="td-c-10">Claim No</th>
                                <th class="td-c-10">DOS</th>                                
                                <th class="td-c-10">CPT</th>
                                <th class="td-c-10">Billed</th>
                                <th class="td-c-10">Allowed</th>                               
                                <th class="td-c-10">Paid</th>
                                <th class="td-c-10">Balance</th>                              
                                <th class="td-c-13 js-change-text">Patient Paid</th>
                                <th class="td-c-13">Total Bal</th>
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
                 {!! Form::hidden('resubmit', null)!!}
                 {!! Form::hidden('next', null)!!}
                 {!! Form::hidden('payment_detail_id',@$post_val->payment_detail_id) !!}
                <?php $getremaining_lists = []; static  $i = 0;
                //dd($claims_lists->dosdetails);
                ?>
                @foreach($claims_lists->dosdetails as $claims_list)
                <?php
                    $claim_number = isset($claims_lists->claim_number)?$claims_lists->claim_number:'';
					$dos = @$claims_list->dos;
					$cpt = @$claims_list->cpt_code;
					$billed = $claims_list->charge;
					$paid_amt = @$claims_list->paid_amt;
					$patient_paid = $claims_list->patient_paid;
					$patient_adj = @$claims_list->patient_adjusted;
					$patient_due = $claims_list->patient_balance;   
					$insurance_due = $claims_list->insurance_balance;
					$total_allowed = $claims_list->cpt_allowed_amt;                
					$balance_amt =  strip_tags($claims_list->balance); // Balance amount negative will return with span tag wrapped             
					$balance_amt = number_format(str_replace( ',', '', $balance_amt ),2, '.', '');
					$dos_id = $claims_list->id;
                    $nonevar = '';                 
                ?>     
                <li class="billing-grid js-calculate" id = "<?php echo $i ?>" <?php echo $nonevar;?>>
                    <table class="table-billing-view js-insurance-table superbill-claim">
                        <tbody>
							<tr>                                 
                                <td class="td-c-10"><input type="text" class="billing-noborder" readonly = "readonly" name=<?php echo "claim_number[" . $i . "]"; ?>   value = "{{$claim_number}}" tabindex = -1></td>
                                <td class="td-c-10"><input type="text" class="billing-noborder" readonly = "readonly" name=<?php echo "dos[" . $i . "]"; ?>   value = "{{@$dos}}" tabindex = -1></td>
                                <td class="td-c-10"> <input type="text" readonly = "readonly" class="billing-noborder" name= <?php echo "cpt[" . $i . "]"; ?> value = "{{@$cpt}}" tabindex = -1></td>    
                                <td class="td-c-10 class-readonly"><input type="text" readonly = "readonly" name= <?php echo "cpt_billed_amt[" . $i . "]"; ?> value = "{{$billed}}" class="js-cpt-billed text-right billing-noborder class-readonly" tabindex = -1></td>
                                <td class="td-c-10 class-readonly"><input type="text" name= <?php echo "cpt_allowed_amt[" . $i . "]"; ?> value = "{{$total_allowed}}" class="allownumericwithdecimal text-right billing-noborder js-cpt-allowed class-readonly" readonly = "readonly" tabindex = -1></td>
                                <td class="td-c-10 class-readonly"><input readonly = "readonly" name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$paid_amt}}" type="text"  class="allownumericwithdecimal text-right billing-noborder js-paid-amt class-readonly" tabindex = -1></td>
                                <td class="td-c-10 class-readonly"><input readonly = "readonly" name= <?php echo "balance[" . $i . "]"; ?> value = "{{$balance_amt}}" type="text"  class="allownumericwithdecimal text-right billing-noborder js-balance class-readonly" tabindex = -1></td>                                
                                <td class="td-c-13 text-center">
                                    <input name= <?php echo "patient_paid[" . $i . "]"; ?> value = "0.00" type="text" class="billing-noborder text-right js_need_regex js_pateint_paid allownumericwithdecimal {{$payment_negative_class}}" id = "<?php echo $i;?>"></td>
                                <input type="hidden" name= <?php echo "patient_paid_calc[" . $i . "]"; ?> class="billing-noborder js_patient_paid_calc" value = "{{@$patient_paid}}" id = "<?php echo $i;?>" maxlength = "10">
                                <input type="hidden" name= <?php echo "patient_adjusted[" . $i . "]"; ?> value = "{{@$patient_adj}}" id = "<?php echo $i;?>">
                                <td class="td-c-13">
                                <input readonly = "readonly" name= <?php echo "patient_balance[" . $i . "]"; ?> value = "" type="text"  class="billing-noborder text-right js-patient-balance" tabindex = -1></td>
                                <input name= <?php echo "ids[" . $i . "]"; ?> value = "{{$dos_id}}" type="hidden">
                                <input name= <?php echo "patient_due[" . $i . "]"; ?> value = "{{$patient_due}}" type="hidden">
                                <input name= <?php echo "insurance_due[" . $i . "]"; ?> value = "{{$insurance_due}}" type="hidden">
                          </tr>
                        </tbody>
                    </table>                                     
                </li>
                <?php $i++;?>
                @endforeach               
                <li class="">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>       
                                <td class="td-c-10 text-right"><span class="med-green font600"></span></td>
                                <td class="td-c-10 text-right"><span class="med-green font600"></span></td>
                                <td class="td-c-10 text-right"><span class="med-green font600"></span></td>
                                <td class="td-c-10 text-right"><span class="med-green font600" id = "js-cpt-billed"></span></td> 
                                <td class="td-c-10 text-right"><span class="med-green font600" id = "js-cpt-allowed"></span></td> 
                                <td class="td-c-10 text-right"><span class="med-green font600" id = "js-paid-amt"></span></td>
                                <td class="td-c-10 text-right"><span id = "js-balance" class="med-green font600"></span></td> 
                                <td class="td-c-13 text-right"><span id= "js_pateint_paid" class="med-green font600"></span></td> 
                                <td class="td-c-13 text-right"><span id= "js-patient-balance" class="med-green font600"></span></td>
                            </tr>
                        </tbody>
                    </table>                                     
                </li>              
            </ul>            
        </div>
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->
<div class="col-md-12 padding-t-5"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
				
		<?php $stmt_holdreason = App\Models\STMTHoldReason::getStmtHoldReasonList(); ?>
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="">
			<div class="payment-links pull-left margin-t-5">
                <ul class="nav nav-pills">
					<li class="med-right-border p-r-5 p-b-5">
						<a href= "#" accesskey="n" data-payment-info = "Notes" data-toggle="modal" data-payment-type = "notes" data-target="#post_payments" data-url = "{{url('patients/'.$id.'/payments/patientnotes')}}"class=" form-cursor js-modalboxopen font600 p-r-10 p-l-10 no-border"><i class="fa fa-pencil"></i>Notes</a>
					</li>
					<li class="med-right-border p-r-10 p-l-10">
						<div class="form-group-billing no-bottom">
							<?php 
								$disabled_class = 'disabled';
								$statement_status = ($claims_lists->patient->statements == 'Hold')?'yes':'';
								if($claims_lists->patient->statements == 'Hold') {
									$hold_reason = $claims_lists->patient->hold_reason;
									$hold_release_date = isset($claims_lists->patient->hold_release_date)? App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims_lists->patient->hold_release_date): null;
									$disabled_class = '';
								} else {
									$hold_reason = 0;
									$hold_release_date = '';
								}	
							?>
							{!!Form::checkbox('is_hold_statement','Hold statement',$statement_status, ['style'=>'background-color: #ffffff;','id'=>'hold-statement', 'class' => 'js_pmt_hold_stmt'])!!} <label for="hold-statement" class="med-darkgray font600">Hold Statement</label>
						</div>
					</li>
                    <li class="med-right-border p-r-10" style="width:400px;">
						<div class="form-group-billing">
							{!! Form::label('hold_reason', 'Hold Reason', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
							<div class="col-lg-8 col-md-8 col-sm-6 col-xs-10 select2-white-popup">
								{!! Form::select('hold_reason', array(''=>'-- Select --' )+(array)@$stmt_holdreason,$hold_reason,['class'=>'select2 form-control js_pmt_hold_block','id'=>'hold_reason', $disabled_class]) !!}
							</div>
						</div>
					</li>
					<li @if(!empty($final)) class="med-right-border p-r-10" @endif >
						<div class="form-group-billing ">
							{!! Form::label('hold_release_date', 'Hold Release Date', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
							<div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">
								{!! Form::text('hold_release_date',$hold_release_date,['id'=>'hold_release_date','class'=>'form-control form-cursor dm-date js_pmt_hold_block','placeholder'=>Config::get('siteconfigs.default_date_format'),'id'=>'hold_release_date', $disabled_class]) !!}
							</div>
						</div>
					</li>
					@if(!empty($final))
                        <li><a><i class="fa fa-arrow-right"></i>{!! Form::submit('Next',['class' => 'js-next no-border bg-white', 'accesskey'=>'x']) !!}</a></li>
                    @endif 
				</ul>
			</div>	
		</div>
	
		<?php /*
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="">                                   
            <div class="payment-links pull-right margin-t-5">
                <ul class="nav nav-pills">
                    <li style="display:none;"><a data-toggle = "collapse" data-target = "#view_transaction" > <i class="fa fa-file-text-o"></i> View Transaction</a></li>                    
                    <li><a href= "#" accesskey="n" data-payment-info = "Notes" data-toggle="modal" data-payment-type = "notes" data-target="#post_payments" data-url = "{{url('patients/'.$id.'/payments/patientnotes')}}"class=" form-cursor js-modalboxopen font600 p-r-10 p-l-10"><i class="fa fa-pencil"></i>Notes</a>
                        </li>                                       
                    <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-reorder"></i> Statement</a></a>
                            <ul class="dropdown-menu" style="margin-top: 3px;">
                                <li>
                                    <ul class="menu" style="list-style-type:none; ">
                                        <li class="font13" style="padding-left:3px;">
                                            <?php $statement_status = ($claims_lists->patient->statements == 'Hold')?'yes':'';?>
                                            {!!Form::checkbox('is_hold_statement','Hold statement',$statement_status, ['style'=>'background-color: #ffffff;','id'=>'hold-statement'])!!} <label for="hold-statement" class="med-darkgray font600">Hold Statement</label>
                                        </li>                                            
                                    </ul>
                                </li>
                           </ul>      
                    </li>  
                    @if(!empty($final))
                        <li><a><i class="fa fa-arrow-right"></i>{!! Form::submit('Next',['class' => 'js-next no-border bg-white', 'accesskey'=>'x']) !!}</a></li>
                    @endif                                                                  
                </ul>
            </div>              
        </div>   
		*/ ?>
			
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center selFnBtn hide margin-t-10 margin-b-10">
            {!! Form::submit('Save', ['class'=>'btn btn-medcubics', 'accesskey'=>'s']) !!}
            <?php $url = (isset($from) && $from == "mainpayment")? url('payments'):url('patients/'.$id.'/payments');
             $cancel_payment_class = ($post_val->payment_type == "Payment" || $post_val->payment_type == "Refund")?"js-cancel-payment":"";?>
            <a class = "js-cancel" href="{{ $url}}">{!! Form::button('Cancel', ['id'=> 'patient','class'=>'btn btn-medcubics '.$cancel_payment_class, 'accesskey'=>'c']) !!}</a>
        </div>
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends --> 

<!-- Patient Alert Notes Block Start -->
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
<!-- Patient Alert Notes Block End -->
   <script type="text/javascript">      
    var today_practice = '{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), "m/d/Y") }}'; 
   </script>