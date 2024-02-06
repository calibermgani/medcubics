@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-user font14"></i> Patient <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('contactdetail') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('patients/payments/tabs-ins')
@stop

@section('practice')
<?php $id = Route::current()->parameters['id']; ?>
<div class="col-md-12"><!-- Inner Content for full width Starts -->

    <!-- Tab Starts  -->
    <?php 
		$activetab = 'payments_list'; 
		$routex = explode('.',Route::currentRouteName());
	?>
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">

            <li class="@if($activetab == 'list') active @endif"><a href="{{ url('patients/'.$id.'/payments') }}"><i class="fa fa-navicon i-font-tabs"></i> List</a></li>           	                      	           
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-money i-font-tabs"></i> Payment Posting</a></li>           	                      	           
        </ul>
    </div>
    <!-- Tab Ends -->    
    <div class="box-body-block padding-t-20 no-border-radius"><!--Background color for Inner Content Starts -->    

        {!! Form::open(['url'=>'patients/payments']) !!}     

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border border-green"><!-- General Details Full width Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >
                            <span class="bg-white font600 padding-0-4">General Details</span>
                        </div>
                        <div class="box-body form-horizontal"><!-- Box Body Starts -->                                                       
                            <div class="form-group-billing">
                                {!! Form::label('type', 'Billed To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">                                     
                                    {!! Form::select('payment_mode', ['Insurance1' => 'Insurance1','Insurance1' => 'Insurance2','Adjustment' => 'Insurance3'],null,['class'=>'select2 form-control']) !!}
                                </div>                                 
                            </div>    


                            <div class="form-group-billing ">
                                {!! Form::label('amt', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">                                     
                                    {!! Form::select('payment_mode', ['Check' => 'Check','Cash' => 'Cash','EFT' => 'EFT','Credit' => 'Credit Card'],null,['class'=>'select2 form-control']) !!}
                                </div>                                 
                            </div>

                            <div class="form-group-billing">                               
                                {!! Form::label('amt', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">                                     
                                    {!! Form::text('cheque_amt',null,['class'=>'form-control allownumericwithdecimal input-sm-header-billing']) !!}
                                </div> 
                            </div> 
                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->                    


                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding tab-l-b-1 border-green"><!--  3rd Content Starts -->                                               
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >

                        </div>
                        <div class="box-body form-horizontal">                                                                                                             
                            <div class="form-group-billing">                               
                                {!! Form::label('Chk No', 'Check No', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('cheque_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                               
                            </div>  

                            <div class="form-group-billing">                               
                                {!! Form::label('Chk No', 'Check Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
                                    <i class="fa fa-calendar-o form-icon-billing"></i>
                                    {!! Form::text('cheque_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing ']) !!}
                                </div>                                
                            </div> 

                            <div class="form-group-billing">                               
                                {!! Form::label('Chk No', 'Deposit Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10"> 
                                    <i class="fa fa-calendar-o form-icon-billing"></i>
                                    {!! Form::text('cheque_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                               
                            </div>  

                        </div>
                    </div><!--  3rd Content Ends -->
                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding tab-l-b-2 md-display border-green"><!--  3rd Content Starts -->                                               
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10" >

                        </div>
                        <div class="box-body form-horizontal">

                            <div class="form-group-billing">
                                {!! Form::label('Billed Amt', 'Billed', ['class'=>'col-lg-6 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">                                    
                                    {!! Form::text('total_charge', null ,['readonly' => 'readonly','maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Paid', 'Paid', ['class'=>'col-lg-6 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('unupplied',null,['readonly' => 'readonly','maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Balance', 'Balance', ['class'=>'col-lg-6 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-6 col-md-7 col-sm-6 col-xs-10">                                    
                                    {!! Form::text('total_due',null,['readonly' => 'readonly','maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>
                        </div>
                    </div><!--  3rd Content Ends -->


                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 no-padding md-display tab-l-b-1 border-green"><!--  4th Content Starts -->
                        <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">                          

                            <div class="form-group-billing">                               
                                {!! Form::label('Chk No', 'Posting Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('cheque_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                               
                            </div>  

                            <div class="form-group-billing">                               
                                {!! Form::label('Ref No', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                     
                                    {!! Form::text('cheque_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing ']) !!}
                                </div>                                
                            </div> 

                            <div class="form-group-billing">
                                {!! Form::label('Unapplied', 'Unapplied', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                    
                                    {!! Form::text('total_due',null,['readonly' => 'readonly','maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                                </div>                                   
                            </div>

                           

                        </div><!-- /.box-body -->
                    </div><!--  4th Content Ends -->

                </div><!--  Box Ends -->
            </div><!-- Only general details Content Ends -->
        </div><!-- General Details Full width Ends -->
    </div><!-- Only general details Content Ends -->  
</div>
</div>
<div class="col-md-12 p-t-2"><!-- Inner Content for full width Starts -->
    <div class="box-body-block no-border"  ><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">                            
            <ul class="billing js-calculateadjus mobile-width" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                <li class="billing-grid">
                    <table class="table-billing-view">
                        <thead>
                            <tr>
                                <th class="td-c-1"></th> 
                                <th class="td-c-6">DOS</th>                                
                                <th class="td-c-6">CPT</th>
                                <th class="td-c-6">Billed</th>
                                <th class="td-c-6">Allowed</th> 
                                <th class="td-c-6">Balance</th> 
                                <th class="td-c-4">Ded</th>
                                <th class="td-c-6">Co-Pay</th>                                
                                <th class="td-c-6">Co-Ins</th>
                                <th class="td-c-6">With Held</th>
                                <th class="td-c-6">Adj</th>      
                                <th class="td-c-6">Paid</th>
                                <th class="td-c-6">Code</th> 
                                <th class="td-c-6">Status</th>
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
                <?php
					$select_ins = [];
					$primary = isset($select_ins['Primary']) ? $select_ins['Primary'] : '';
					$seconday = isset($select_ins['Secondary']) ? $select_ins['Secondary'] : '';
					$tertiary = isset($select_ins['Tertiary']) ? $select_ins['Tertiary'] : '';
                ?> 
                {!! Form::hidden('primary', $primary)!!}
                {!! Form::hidden('secondary',$seconday)!!}
                {!! Form::hidden('tertiary',$tertiary) !!}
                {!! Form::hidden('claim_paid_amt',null,['id' => 'js-paid-amt']) !!}
                {!! Form::hidden('adjust_amt',null,['id' => 'js-adjust']) !!}
                {!! Form::hidden('patient_due',null,['id' => 'js-patientdue']) !!}
                {!! Form::hidden('insurance_due',null,['id' => 'js-insurancedue']) !!}

                @if(!empty($claims_list->dosdetails))
                <?php $count = count($claims_list->dosdetails); ?>
                @for($i=0;$i<$count;$i++)
                <?php 
					$date_to = '';
					$date_from = '';                
					if (!empty($claims_list->dosdetails[$i]->dos_from)) {
						$date_from = (@$claims_list->dosdetails[$i]->dos_from && $claims_list->dosdetails[$i]->dos_from != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claims_list->dosdetails[$i]->dos_from)) : '';
					}
					$billed = ($claims_list->dosdetails[$i]->charge != '0.00') ? $claims_list->dosdetails[$i]->charge : '';
					$allowed = ($claims_list->dosdetails[$i]->cpt_allowed_amt != '0.00') ? $claims_list->dosdetails[$i]->cpt_allowed_amt : '';
					$paid = ($claims_list->dosdetails[$i]->paid_amt != '0.00') ? $claims_list->dosdetails[$i]->paid_amt : '';
					$co_ins = ($claims_list->dosdetails[$i]->co_ins != '0.00') ? $claims_list->dosdetails[$i]->co_ins : '';
					$co_pay = ($claims_list->dosdetails[$i]->co_pay != '0.00') ? $claims_list->dosdetails[$i]->co_pay : '';
					$deductable = ($claims_list->dosdetails[$i]->deductable != '0.00') ? $claims_list->dosdetails[$i]->deductable : '';
					$with_held = ($claims_list->dosdetails[$i]->with_held != '0.00') ? $claims_list->dosdetails[$i]->with_held : '';
					$adjustment = ($claims_list->dosdetails[$i]->adjustment != '0.00') ? $claims_list->dosdetails[$i]->adjustment : '';
					$balance = ($claims_list->dosdetails[$i]->balance != '0.00') ? $claims_list->dosdetails[$i]->balance : '';
					$code = ($claims_list->dosdetails[$i]->denial_code) ? $claims_list->dosdetails[$i]->denial_code : '';
					$ins_id = ($claims_list->dosdetails[$i]->insurance_id) ? $claims_list->dosdetails[$i]->insurance_id : '';
					$dos_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claims_list->dosdetails[$i]->id, 'encode');
                ?>              
                <li class="billing-grid js-calculate" id = "<?php echo $i ?>">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>                                                         
                                <td class="td-c-1"><a href="#" class="toggler font600 toggle-plus" data-prod-cat="1"> </a></td>
                                <td class="td-c-6"><input type="text" class="dm-date billing-noborder" readonly = "readonly" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}" ></td>
                                <td class="td-c-6"> <input type="text" readonly = "readonly" class="billing-noborder" name= <?php echo "cpt[" . $i . "]"; ?> value = "{{@$claims_list->dosdetails[$i]->cpt_code}}"></td>  
                                <td class="td-c-6"><input type="text" readonly = "readonly" name= <?php echo "cpt_billed_amt[" . $i . "]"; ?> value = "{{$billed}}" class="js-cpt-billed billing-noborder"></td>
                                <td class="td-c-6"><input type="text" readonly = "readonly" name= <?php echo "cpt_allowed_amt[" . $i . "]"; ?> value = "{{$allowed}}" class="allownumericwithdecimal js-cpt-allowed billing-noborder" onchange ="calculatebalance(<?php echo $i; ?>, 'js-cpt-allowed')"></td>
                                <td class="td-c-6"><input name= <?php echo "balance[" . $i . "]"; ?> value = "{{$balance}}" type="text" readonly = "readonly" class="allownumericwithdecimal js-balance billing-noborder"  onchange ="calculatebalance(<?php echo $i; ?>)"></td> 
                                <td class="td-c-4"><input name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$paid}}" type="text" readonly = "readonly" class="allownumericwithdecimal js-paid-amt billing-noborder"  onchange ="calculatebalance(<?php echo $i; ?>)"></td>
                                <td class="td-c-6"><input name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$paid}}" type="text" readonly = "readonly" class="allownumericwithdecimal js-paid-amt billing-noborder"  onchange ="calculatebalance(<?php echo $i; ?>)"></td>
                                <td class="td-c-6"><input name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$paid}}" type="text" readonly = "readonly" class="allownumericwithdecimal js-paid-amt billing-noborder"  onchange ="calculatebalance(<?php echo $i; ?>)"></td>
                                <td class="td-c-6"><input name= <?php echo "balance[" . $i . "]"; ?> value = "{{$balance}}" type="text" readonly = "readonly" class="allownumericwithdecimal js-balance billing-noborder"  onchange ="calculatebalance(<?php echo $i; ?>)"></td> 
                                 <!--<td  class="td-c-10 billing-select2-disabled-white">{!! Form::select('payment_mode', [''=>'-- Select --','Insurance2' => 'Contractual','Insurance1' => 'Write-off'],@$claims_list->payment_mode,['class'=>'select2 form-control']) !!}</td>> -->  
                                <td class="td-c-6"><input name= <?php echo "balance[" . $i . "]"; ?> value = "{{$balance}}" type="text"  class="allownumericwithdecimal js-balance billing-noborder"  onchange ="calculatebalance(<?php echo $i; ?>)"></td>                              
                                <td class="td-c-6"><input name= <?php echo "paid_amt[" . $i . "]"; ?> value = "{{$paid}}" type="text" readonly = "readonly" class="allownumericwithdecimal js-paid-amt billing-noborder" onchange ="calculatebalance(<?php echo $i; ?>)"></td>
                                <td class="td-c-6 billing-select2-disabled-white">{!! Form::select('remarkcode['.$i.']', array('' => '--')+(array)$remarkcode,$code,['class'=>'select2 form-control']) !!}</td>
                                <td class="td-c-6"><span class=" m-ppaid">P.Paid</span></td> 
								<input name= <?php echo "ids[" . $i . "]"; ?> value = "{{$dos_id}}" type="hidden">                  
							</tr>

							<tr class="cat1" style="display:none;">
								<td class="tr-dropdown" colspan="5">Pri: Cigna Healthcare</td>                                                                                                                              
								<td class="tr-dropdown">$400</td>
								<td class="tr-dropdown">$100</td>
								
								<td class="tr-dropdown">$0.00</td>
								<td class="tr-dropdown">$0.00</td>
								<td class="tr-dropdown"></td>
								<td class="tr-dropdown">$10.00</td>                                        
								<td class="tr-dropdown">$0.00</td>
								<td class="tr-dropdown"></td>                                        
								<td class="tr-dropdown"></td>                               
							</tr>   
							<tr class="cat1" style="display:none;">
								<td class="tr-dropdown" colspan="5">Sec: 1199 National Healthcare</td>                                                                                                                               
								<td class="tr-dropdown">$400</td>
								<td class="tr-dropdown">$100</td>
								
								<td class="tr-dropdown">$0.00</td>
								<td class="tr-dropdown">$0.00</td>
								<td class="tr-dropdown"></td>
								<td class="tr-dropdown">$10.00</td>                                        
								<td class="tr-dropdown">$0.00</td>
								<td class="tr-dropdown"></td>                                        
								<td class="tr-dropdown"></td>                               
							</tr>   
                        </tbody>
                    </table>                                     
                </li>
                @endfor
                <li>
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>       
                                <td class="td-c-1"><span  class="med-green font600"></span></td>
                                <td class="td-c-6"><span  class="med-green font600"></span></td>
                                <td class="td-c-6"><span class="med-green font600"></span></td> 
                                <td class="td-c-6"><span class="med-green font600">$ 83.00</span></td>
                                <td class="td-c-6"><span class="med-green font600">$ 83.00</span></td>
                                <td class="td-c-6"><span  class="med-green font600">$ 61.00</span></td> 
                                <td class="td-c-4"><span  class="med-green font600">$ 61.00</span></td> 
                                <td class="td-c-6"><span  class="med-green font600">$ 61.00</span></td> 
                                <td class="td-c-6"><span  class="med-green font600">$ 00.00</span></td>
                                <td class="td-c-6"><span id = "js-balance" class="med-green font600">$ 100.00</span></td> 
                                <td class="td-c-6"></td>
                                <td class="td-c-6"><span  class="med-green font600">$ 100.00</span></td> 
                                <td class="td-c-6"></td> 
                                <td class="td-c-6"></td> 
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                @endif
            </ul>            
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 bg-aqua yes-border border-brown padding-10">
            

            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-b-m-8 form-horizontal ">
                <div class="form-group-billing">
                    {!! Form::label('Unapplied', 'Responsibility', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">                                    
                        {!! Form::select('insurance_id', array('pateint' => 'Patient'),null,['class'=>'select2 form-control js-insurance']) !!}
                    </div>                                   
                </div>
                <div class="form-group-billing">
                    {!! Form::label('Unapplied', 'Claim Status', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">                                    
                        {!! Form::select('insurance', [''=>'-- Select --','Paid' => 'Paid','Cash' => 'Denail','ppaid'=>'Partial Paid'],null,['class'=>'select2 form-control']) !!}
                    </div>                                   
                </div>
                <div class="form-group-billing">
                    {!! Form::label('Unapplied', 'Hold', ['class'=>'col-lg-5 col-md-6 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                    <div class="col-lg-7 col-md-6 col-sm-6 col-xs-10 select2-white-popup">                                    
                        {!! Form::select('insurance', [''=>'-- Select --','insurance1' => 'Insurance Hold','Cash' => 'Patient Hold'],null,['class'=>'select2 form-control']) !!}
                    </div>                                   
                </div>
            </div>

            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 no-padding tab-l-b-1 border-brown md-display"><!-- VOB Col starts -->
				<div class="box box-view no-shadow no-border-radius margin-t-m-10 no-bottom no-border bg-aqua"><!-- VOB Box starts -->
					<div class="box-header-view bg-aqua no-border-radius">
						<i class="livicon" data-name="responsive-menu"></i> <span class="med-green font600 font12">Remark Codes</span> 
					</div><!-- /.box-header -->
					<div class="box-body table-responsive chat pymt-codes margin-t-m-5"><!-- VOB Box Body starts -->

						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive">
							<p class="no-bottom med-gray-dark"><span class="med-orange font600">45H34 : </span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>  
							<p class="no-bottom med-gray-dark"><span class="med-orange font600">64D4 : </span>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>  
						</div>                                                            
					</div><!-- VOB box-body Ends-->
				</div><!-- VOB box Ends -->
			</div><!-- VOB COl Ends -->
            
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 m-b-m-8 tab-l-b-1  md-display border-brown">
                <p class="margin-t-0"><span class="med-green font600">Patient Balance </span><span class="pull-right font600 med-gray-dark js-patientdue">$ 235.55</span></p>
                <p class="margin-t-m-3"><span class="med-green font600">Insurance Balance </span> <span class="pull-right font12 font600 med-gray-dark js-insurancedue">$ 1341.00</span></p>
                <p class="margin-t-m-3"><span class="med-green font600">Total Balance </span> <span class="pull-right font12 font600 bg-date js-totaldue">$ 1576.55</span></p>

            </div>
        </div>    
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
            <div class="form-group-billing">                               
                {!! Form::label('Chk No', 'Notes', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 p-l-0 control-label-billing med-green font600']) !!}                                                  
                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-10 p-r-0">                                     
                    {!! Form::text('cheque_no',null,['maxlength'=>'25','class'=>'form-control input-sm-header-billing']) !!}
                </div>                               
            </div>  
        </div>


    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->
<div class="col-md-12 padding-t-5"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">                                   
            <div class="payment-links pull-right margin-t-5">
                <ul class="nav nav-pills">
                    <li><a data-toggle = "collapse" data-target = "#view_transaction" > <i class="fa fa-file-text-o"></i> View Transaction</a></li>
                    <li><a> <i class="fa fa-pencil"></i> Edit Charges</a></li>                   
                    <li><a><i class="fa fa-reorder"></i> Workbench</a></li>
                    <li><a><i class="fa fa-file-pdf-o"></i> CMS 1500</a></li>                                        
                    <li><a><i class="fa fa-retweet"></i>Re-Submit</a></li>                   
                </ul>
            </div>
            <div id = "view_transaction" class="collapse out col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive yes-border border-green margin-t-10" >
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10 margin-b-6">
                            <span class="bg-white med-orange padding-0-4 font600"> Transaction Details</span>
                        </div>
                        <table class="popup-table-wo-border table table-responsive no-bottom">                    
                            <thead>
                                <tr> 
                                    <th>CPT</th>
                                    <th>Date</th>                                
                                    <th>Description</th>                                    
                                    <th>Amount</th>
                                    <th>Pat Bal</th>
                                    <th>Ins Bal</th>                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr>                                                
                                    <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="3"> </a> 99088</td> 
                                    <td>12-12-15</td>
                                    <td>Lorem Ipsum is simply dummy </td>
                                    <td>2,300.00</td>                                     
                                    <td>0.00</td>
                                    <td>2,300.00</td>                                    
                                </tr>
                                <tr class="cat3" style="display:none; background: #e7fcf3">                                                
                                    <td></td>
                                    <td>01-10-16</td> 
                                    <td>Lorem Ipsum is simply dummy  text  printing </td>
                                    <td>400.00</td>                                      
                                    <td>0.00</td>
                                    <td>400.00</td>
                                </tr>

                                <tr class="cat3" style="display:none; background: #e7fcf3">                                              
                                    <td></td>
                                    <td>01-12-16</td> 
                                    <td>Lorem Ipsum is simply dummy text of the printing </td>
                                    <td>240.00</td>                                     
                                    <td>0.00</td>
                                    <td>240.00</td>
                                </tr>    
                                <tr>                                                
                                    <td><a href="#" class="toggler font600 toggle-plus" data-prod-cat="4"> </a> 98647</td> 
                                    <td>01-18-16</td>
                                    <td>Lorem Ipsum is simply dummy   </td>
                                    <td>217.00</td>                                        
                                    <td>0.00</td>
                                    <td>217.00</td>
                                </tr>

                                <tr class="cat4" style="display:none; background: #e7fcf3">                                                
                                    <td></td>
                                    <td>01-22-16</td> 
                                    <td>Lorem Ipsum is simply dummy text of the printing </td>
                                    <td>40.00</td>                                        
                                    <td>0.00</td>
                                    <td>40.00</td>
                                </tr>    
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner Content for full width Ends -->
            </div><!--Background color for Inner Content Ends -->            
        </div>        
        <div class="box-footer space20">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
                <a href="{{ url('contactdetail')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>

            </div>
        </div><!-- /.box-footer -->
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->  

{!! Form::close() !!}
<!--End-->
@stop 
@push('view.scripts')                           
<script type="text/javascript">
    $(document).ready(function () {
        $('#js-batch-date').on('change', function () {
            $('#js-payment-validate').bootstrapValidator('revalidateField', 'payment_batch_date');
        });
        $('#js-deposite-date').on('change', function () {
            $('#js-payment-validate').bootstrapValidator('revalidateField', 'deposit_date');
        });
        $('#js-cheque-date').on('change', function () {
            $('#js-payment-validate').bootstrapValidator('revalidateField', 'cheque_date');
        });

        $('#js-payment-validate')
		.bootstrapValidator({
			message: 'This value is not valid',
			excluded: ':disabled',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				payment_batch_date: {
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: 'The value is not a valid date'
						}
					}
				},
				deposit_date: {
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: 'The value is not a valid date'
						}
					}
				},
				cheque_date: {
					validators: {
						date: {
							format: 'MM/DD/YYYY',
							message: 'The value is not a valid date'
						}
					}
				},
				payment_type: {
					validators: {
						notEmpty: {
							message: 'Select payment type'
						},
					}
				},
				payment_mode: {
					validators: {
						notEmpty: {
							message: 'Select paymemnt mode'
						},
					}
				},
			},
		});
    });
</script> 
@endpush