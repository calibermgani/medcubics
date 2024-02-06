@extends('admin')
@section('toolbar')
<?php  $uniquepatientid = $patient_id;  ?>
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Statements </span>  </small>
        </h1>

        <ol class="breadcrumb">
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @include ('patients/layouts/patientstatement_icon')	

            @include ('patients/layouts/swith_patien_icon')	
<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
 <!--li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                    
</li-->
            @include('layouts.practice_module_stream_export', ['url' => '/patients/'.@$patient_id.'/patientpayment/export/'])
            <li><a href="#js-help-modal" data-url="{{url('help/wallet_history')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')<?php  $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'decode'); ?>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'no'])
@stop

@section('practice')
<?php
	$activetab = 'wallet history';
//	$tot_pat_pmt = App\Http\Helpers\Helpers::getPatientBilledAmount(@$patient_id);
    $tot_pat_pmt = App\Models\Patients\Patient::getFinancialData(@$patient_id);  
    $tot_pat_pmt = $tot_pat_pmt['pat_paid'];
	//$tot_last_pmt = App\Http\Helpers\Helpers::getPatientLastPaymentAmount(@$patient_id);
	//$tot_pat_pmt_date = App\Http\Helpers\Helpers::getPatientLastPaymentDate(@$patient_id);
	
	$pat_last_pmt = App\Http\Helpers\Helpers::getPatientLastPaymentAmount(@$patient_id, 'Patient');
	$patlastPmtDate = isset($pat_last_pmt['created_at']) ? $pat_last_pmt['created_at'] : '-';
	$patlastPmtAmt = isset($pat_last_pmt['total_paid']) ? $pat_last_pmt['total_paid'] : 0;
	
	$patient_aging = App\Models\Patients\Patient::getOutstandingData(@$patient_id);
	$id = Route::getCurrentRoute()->parameter('id');
	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_id,'encode'); 
?>	
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->	   
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        @include ('patients/checkreturn/tab')	
    </div>
    <!-- Tab Ends -->

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-b-30 no-padding" style="border-bottom: 2px solid #fff;">
        <div class="box-body form-horizontal  padding-4 margin-t-20">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 table-responsive  tab-r-b-1 p-l-0  md-display tabs-lightgreen-border">
                        <table class="popup-table-wo-border table margin-b-5 margin-t-5">                    
                            <tbody>
                                <tr>
                                    <td class="font600 line-height-30">Total Patient Payment</td>
                                    <td><span class="pull-right line-height-30 font600">{!! $tot_pat_pmt !!}</span></td>
                                </tr>
                                <tr>
                                    <td class="font600 line-height-26">Last Patient Payment</td>
                                    <td><span class="pull-right line-height-30 font600">{!! $patlastPmtAmt !!}</span></td>
                                </tr>  
                                <tr>
                                    <td class="font600 line-height-30" style="width:50%">Last Patient Payment Date</td>
                                    <td><span class="pull-right line-height-30 font600">
									@if($patlastPmtDate != '-')
										{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$patlastPmtDate) !!}
									@else
										{!! $patlastPmtDate !!}
									@endif
									</span></td> 
                                </tr>  
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 table-responsive p-r-0">
                        <h4 class="med-orange margin-t-5 font16">Patient Aging</h4>
                        <table class="popup-table-border  table-separate table m-b-m-1">                    
                            <thead>
                                <th class="font600 med-green text-center" style="background: #96dcd8; border-right: 5px solid #f0f0f0">0-30</th>
                                <th class="font600 med-green text-center" style="background: #96dcd8; border-right: 5px solid #f0f0f0">31-60</th> 
                                <th class="font600 med-green text-center" style="background: #96dcd8; border-right: 5px solid #f0f0f0">61-90</th>
                                <th class="font600 med-green text-center" style="background: #96dcd8; border-right: 5px solid #f0f0f0">91-120</th>   
                                <th class="font600 med-green text-center" style="background: #96dcd8; border-right: 5px solid #f0f0f0">+120</th>   
                                <th class="font600 med-green text-center" style="background: #96dcd8;">Total</th>
                            </thead>
                            <tbody>

                                <tr>                               
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0"><span> 
									{!! App\Http\Helpers\Helpers::priceFormat(@$patient_aging['Outstanding'][0]) !!}</span></td> 
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">
									{!! App\Http\Helpers\Helpers::priceFormat(@$patient_aging['Outstanding'][1]) !!}</td>
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">
									{!! App\Http\Helpers\Helpers::priceFormat(@$patient_aging['Outstanding'][2]) !!}</td>
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">
									{!! App\Http\Helpers\Helpers::priceFormat(@$patient_aging['Outstanding'][3]) !!}</td>
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">
									{!! App\Http\Helpers\Helpers::priceFormat(@$patient_aging['Outstanding'][4]) !!}
									</td>
                                    <td class="med-orange font600 bg-white text-center">
									{!! App\Http\Helpers\Helpers::priceFormat(@$patient_aging['Outstanding'][5]) !!}
									</td>
                                </tr>                                 
                                <tr>                               
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0"><span>{!! @$patient_aging['Percentage'][0] !!}%</span></td> 
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">{!! @$patient_aging['Percentage'][1] !!}%</td>
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">{!! @$patient_aging['Percentage'][2] !!}%</td>
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">{!! @$patient_aging['Percentage'][3] !!}%</td>
                                    <td class="font600 text-center bg-white" style="border-right: 5px solid #f0f0f0">{!! @$patient_aging['Percentage'][4] !!}%</td>
                                    <td class="med-orange font600 bg-white text-center">{!! @$patient_aging['Percentage'][5] !!}%</td>
                                </tr> 
                            </tbody>
                        </table>
                    </div>    
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20 p-b-20"  style="border-bottom: 2px solid #f0f0f0;">
            <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Statement History</h4>
            <div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <table class="table table-striped table-bordered space table-separate " id="example1">
                    <thead>
                        <tr>    
							<?php /*
                            <th class="td-c-2"></th>
							*/ ?>
                            <th>Generated On</th>                     
                            <th>Recipient </th>
                            <th>Statement Due</th> 
                            <th>Pay By Date</th>
                            <th>Type</th>                  
							<?php /*
                            <th class="td-c-2"></th>
							*/ ?>
                        </tr>
                    </thead>
                    <tbody>
						@foreach($statments AS $st)
                        <tr>    
							<?php /*
                            <td><input type="checkbox" class="flat-red"></td>
							*/ ?>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat($st->send_statement_date, 'date') }}</td>
                            <td>{{ App\Models\Payments\ClaimInfoV1::GetPatientName(@$st->patient_id) }}</td>							
                            <td>{{ $st->balance }}</td>
                            <td>{{ App\Http\Helpers\Helpers::timezone($st->pay_by_date, 'm/d/Y') }}</td>
							<td>{{ $st->type_for }}</td>
							<?php /* @todo download option not available now. needs to work on it
                            <td><i class="fa fa-file-pdf-o med-green"></i></td>
							*/ ?>
                        </tr>
						@endforeach

                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20"  style="border-bottom: 2px solid #f0f0f0;">
            <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Wallet History</h4>
            <div class="box no-border no-shadow bg-transparent">				

                <div class="box-body table-responsive">
                    <table id="example2" class="table table-striped ">
                        <thead>
                            <tr>
                                <th style="background: #96dcd8; color: #00877f;"><span class="med-green font600">Payment ID</span></th>  
                                <th style="background: #96dcd8;"><span class="med-green font600">Check No/Mode</span></th>
                                <th style="background: #96dcd8;"><span class="med-green font600">Check Date</span></th>
                                <th style="background: #96dcd8;"><span class="med-green font600">Check Amt($)</span></th>
                                <th style="background: #96dcd8;"><span class="med-green font600">Posted($)</span></th>
                                <th style="background: #96dcd8;"><span class="med-green font600">Un Posted($)</span></th>
                                <th style="background: #96dcd8;"><span class="med-green font600">Posted Date</span></th>
                                <th style="background: #96dcd8;"><span class="med-green font600">Posted By</span></th>
                            </tr>
                        </thead>               
                        <tbody>                       
                            @foreach($payments as $payment_detail)
                            <?php
                                $type = ($payment_detail->source != 'refundwallet') ? $payment_detail->pmt_type : "Refund";
                                $check_date = '';
                                $check_no   = '';
                                if ($payment_detail->pmt_mode == "Check" ) {
                                    $check_no = @$payment_detail->check_details->check_no;								
                                    $check_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_detail->check_details->check_date);
                                } else if ($payment_detail->pmt_mode == "EFT") {
                                    $check_no = @$payment_detail->eft_details->eft_no;
                                    $check_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_detail->eft_details->eft_date);
                                } else if ($payment_detail->pmt_mode == "Cash") {
                                    $check_no = "Cash";
                                } else if ($payment_detail->pmt_mode == "Credit Balance") {
                                    $check_no = "Credit Balance";
                                } elseif($payment_detail->pmt_mode == "Credit" ){
                                    if(isset($payment_detail->credit_card_details))
                                        $check_no = (isset($payment_detail->credit_card_details->card_last_4) ? @$payment_detail->credit_card_details->card_last_4." - " : '')."Credit ";            	
                                    else 
                                        $check_no = "Credit ";
                                } elseif($payment_detail->pmt_mode == "Money Order"){
                                    $check_no = (isset($payment_detail->check_details->check_no) ? str_replace("MO-", "",@$payment_detail->check_details->check_no)." - " : '')."Money Order";
                                    $check_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_detail->check_details->check_date);
                                } else {
                                    $check_no = "-Nil-";
                                }
                                if ($payment_detail->pmt_type == "Refund") {
                                    $check_no = $check_no." - Refund";
                                }
                                $check_date = ($check_date != '') ? $check_date : "-Nil-";
                                $payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_detail->id, 'encode');
                                $bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;
                            ?>
                            <tr data-toggle="modal" data-payment-info-number = "{{$payment_detail->pmt_no}}" data-url = "{{url('payments/getpaymentdata/'.$payment_detail_id)}}" class = "js-modalboxopen" data-target="#payment_detail"> 
                                <td class="cur-cursor">{{$payment_detail->pmt_no}}</td>                                
                                <td>{{$check_no}}</td>
                                <td>{{$check_date}}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payment_detail->pmt_amt) !!}</td>
                                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payment_detail->amt_used) !!}</td>
                                <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$bal_amt)!!}</td>
                                <td>{{App\Http\Helpers\Helpers::dateFormat($payment_detail->created_at,'date')}}</td>
                                <td>{{ App\Http\Helpers\Helpers::shortname($payment_detail->created_by) }}</td>		
                            </tr>
                            @endforeach                                                      
                        </tbody>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>


        <div class="col-lg-12 margin-t-10 no-padding hide"  style="border-bottom: 2px solid #f0f0f0;">
            <div class="box-body form-horizontal">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <h4 class="med-darkgray"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Budget Plan</h4>
                            <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">

                                    @include('patients/patients/budgetplan/ajax_form')

                                </div>
                            </div><!-- /.box-body -->   
                        </div>                                
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 margin-t-10 no-padding hide" style="border-bottom: 2px solid #f0f0f0;">
            <div class="box-body form-horizontal">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <h4 class="med-darkgray"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Generate Statement</h4>
                            <p class="text-justify">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                            </p>

                            <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class margin-t-20" id="js-address-general-address">
                                    <div class="form-group">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10  margin-b-15">                                
                                         
                                        <p class="margin-t-15" style="font-size: 15px; display: inline"><span class="demo-checkbox1"> {!! Form::radio('is_self_pay', 'Yes',null,['class'=>'flat-red']) !!} {!! Form::label('r-selfpay', 'Default',['class'=>'form-cursor font600 med-radiogray']) !!}</span> &emsp; 
                                <span class="demo-checkbox1" style="">{!! Form::radio('is_self_pay', 'No',true,['class'=>'flat-red']) !!} {!! Form::label('r-insurance', 'Customize',['class'=>' form-cursor font600 med-radiogray']) !!}</span> </p> 
                                        </div>     
                                                           
                                    </div>

                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', 'Payment Format', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Only Payments</span>&emsp;&emsp; <input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">Insurance/Patients</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', 'Payment Info', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Yes</span>&emsp;&emsp; <input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">No</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', 'Transaction', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Claim</span>&emsp;&emsp;<input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">Line Item</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', 'CPT with Short Description', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Yes</span>&emsp;&emsp; <input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">No</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', 'Primary ICD', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Yes</span>&emsp;&emsp; <input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">No</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', '$0 Ins Balance Service Line', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Yes</span>&emsp;&emsp; <input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">No</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', '$0 Pat Balance Service Line', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           <input type="radio" class="flat-red"> <span class="med-darkgray font600">Yes</span>&emsp;&emsp; <input type="radio" class="flat-red margin-l-15"> <span class="med-darkgray font600">No</span>
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('DOS From', 'Statement Notes', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                           {!! Form::textarea('dos_from',null,['id'=>'js_dosfrom','class'=>'dm-date form-control input-sm-modal-billing','style'=>'height:30px;']) !!}  
                                        </div>                                      
                                    </div>
                                    
                                    <div class="form-group margin-t-15">
                                        {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-10">
                                          <a  class="btn btn-medcubics  margin-t-m-2" type="submit" value="" data-page="eligibility">Generate</a>
                                        </div>                                      
                                    </div>
                                </div>
                            </div><!-- /.box-body -->   
                        </div>                                
                    </div>
                </div>
            </div>
        </div>
        
         <div class="col-lg-12 margin-t-10 no-padding hide" style="border-bottom: 2px solid #f0f0f0;">
            <div class="box-body form-horizontal">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <h4 class="med-darkgray"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Statement Notes</h4>
                            <p class="text-justify">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                            </p>

                            <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class margin-t-20" id="js-address-general-address">
                                    <div class="form-group">
                                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-10">                                
                                            <input type="radio" class="flat-red"> <span class="med-green font600">Notes</span>
                                        </div>       
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">                                
                                            {!! Form::select('insurance_id', array(''=>'-- Select --','1'=>'Statement content 1','1'=>'Statement content 2', '1'=>'Statement content 2','1'=>'Statement content 4'),null,['class'=>'select2 form-control']) !!}                                       
                                        </div>                           
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-3 col-md-3 col-sm-5 col-xs-10">                                
                                            <input type="radio" class="flat-red"> <span class="med-green font600">Custom Notes</span>
                                        </div>  
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">                                
                                            {!! Form::textarea('dos_from',null,['id'=>'js_dosfrom','class'=>'dm-date form-control input-sm-modal-billing','style'=>'height:30px;']) !!}  
                                        </div>                           
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('DOS From', 'Effective Date From', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10">
                                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                                            {!! Form::text('dos_from',null,['id'=>'js_dosfrom','class'=>'dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                                        </div>

                                        {!! Form::label('DOS From', 'To', ['class'=>'col-lg-1 col-md-2 col-sm-4 col-xs-12 control-label star']) !!} 
                                        <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10">
                                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                                            {!! Form::text('dos_from',null,['id'=>'js_dosfrom','class'=>'dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                                        </div>

                                    </div>


                                </div>
                            </div><!-- /.box-body -->   
                        </div>                                
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20 hide"  style="border-bottom: 2px solid #f0f0f0;">
            <h4 class="med-darkgray margin-b-10"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Return Check</h4>
            <div class="box no-border no-shadow bg-transparent">				

                <div class="box-body table-responsive">
                    <table id="" class="table table-bordered table-striped table-separate ">
                        <thead>
                            <tr>                                
                                <th>Check No</th>
                                <th>Check Date</th>
                                <th>Financial Charges</th>                                								                     
                            </tr>
                        </thead>               
                        <tbody> 
                            <tr>
                                <td>464563465</td>
                                <td>12/12/2017</td>
                                <td>3243.00</td>
                            </tr>                               
                        </tbody>
                        </tbody>
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>

        <div class="col-lg-12 margin-t-10 no-padding hide"  style="border-bottom: 2px solid #f0f0f0;">
            <div class="box-body form-horizontal">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
                            <h4 class="med-darkgray"><i class="fa fa-bars i-font-tabs font16 med-orange"></i> Statement Details</h4>
                            <p class="text-justify">
                                By default, the patient will receive a statement for outstanding charges every 30 days
								<ul>
									<li>A statement will be sent to the patient in the next batch on or after the <b class="med-green"><i>Next statement date</i></b> below.</li>
									<li>If the next statement date is blank, outstanding charges will be billed in the next batch</li>                           
								</ul>
                            </p>

                            <div class="box-body form-horizontal no-padding margin-b-15 margin-t-10">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-address-class margin-t-20" id="js-address-general-address">

                                    <div class="form-group">
                                        {!! Form::label('DOS From', 'Hold Statement', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-3 col-md-2 col-sm-5 col-xs-10">                                
                                            <input type="radio" class="flat-red"> Yes &emsp;&emsp; <input type="radio" class="flat-red margin-l-15" checked="checked"> No
                                        </div>                           
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('DOS From', 'Delivered Online Only', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-3 col-md-2 col-sm-5 col-xs-10">                                
                                            <input type="radio" class="flat-red"> Yes &emsp;&emsp; <input type="radio" class="flat-red margin-l-15" checked="checked"> No
                                        </div>                           
                                    </div>

                                    <div class="form-group">
                                        {!! Form::label('DOS From', 'Next Statement Date', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                                        <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10">
                                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing"></i> 
                                            {!! Form::text('dos_from',null,['id'=>'js_dosfrom','class'=>'dm-date form-control input-sm-modal-billing form-cursor','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}  
                                        </div> 

                                    </div>

                                    <div class="form-group margin-t-20">
                                        {!! Form::label('', '', ['class'=>'col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 

                                        <div class="col-lg-6" >
                                            <a  class="btn btn-medcubics js-patient-eligibility_check margin-t-m-2" type="submit" value="" data-page="eligibility" data-patientid ="{{$patient_id}}">Set Next Statement Date</a>
                                            <a  class="btn btn-medcubics js-patient-eligibility_check margin-t-m-2" type="submit" value="" data-page="eligibility" data-patientid ="{{$patient_id}}">Bill in Next Batch</a>
                                        </div>
                                    </div>

                                </div>
                            </div><!-- /.box-body -->   
                        </div>                                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('patients/paymentwallet/payments_popup')
@stop

@push('view.scripts')
<script>
    
</script>
@endpush