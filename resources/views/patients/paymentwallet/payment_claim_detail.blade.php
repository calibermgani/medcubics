<?php 
if(@$payment_details->payment_method == "Patient") {
        $link = 'patient';
        $popup_class = "";
        $insurance_name = "Patient";
      } else{
        $link = 'insurance';
        $popup_class = "js-show-patientsearch";
        $insurance_name = App\Http\Helpers\Helpers::getInsuranceName(@$payment_details->insurancedetail->id);        
      }
      $created_date = (!empty($payment_details->created_at))?App\Http\Helpers\Helpers::dateFormat($payment_details->created_at):'';       
      $check_date = (!empty($payment_details->check_date) && $payment_details->check_date != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($payment_details->check_date):'-Nil-';
      $payment_detail_id =  App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$payment_details->id, 'encode');    
      $balance_amt =  @$payment_details->balance;
      if(@$payment_details->payment_mode == "Check" || @$payment_details->payment_mode == "EFT")  
      {
          $check_no = $payment_details->check_no;            
            
      } else if(@$payment_details->payment_mode == "Cash")   
      {
          $check_no = "Cash";
           
      } else{

          $check_no = "Credit ".@$payment_details->card_no;
             
      } 
?>
<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0">
            <p class="margin-t-m-5 pull-right margin-b-5">                    
                    <a href = "#" data-toggle="modal" data-target="#choose_claim" data-url = "{{url('payments/paymentinsurance/'.$link.'/'.$payment_detail_id)}}"
                       class="{{$popup_class}} claimdetail form-cursor font600 p-l-10 p-r-10"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i> New Claim</a><span class="margin-r-05">|</span>  
                   <td><a href = "#" data-toggle="modal" data-payment-info = "{{$payment_details->paymentnumber}}" data-url = "{{url('payments/editcheck/'.$payment_detail_id)}}" class = "js-modalboxopen font600" data-target="#payment_editpopup"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a></td>
                           
            </p>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">            
            <div class="box box-info no-shadow tabs-border">
                <div class="box-body border-radius-4">                    
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b margin-t-m-8 m-b-m-8">               
                        <p class=""><span class="med-orange font600">{!!$insurance_name!!}</span></p>                                               
                        <p class="space-m-t-7"><span class="med-green font600">Posted Date  </span> <span class="bg-date pull-right">{{$created_date}}</span></p>     
                        <p class="space-m-t-7"><span class="med-green font600">Check No/Mode  </span> <span class="pull-right">{{$check_no}}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Check Date  </span> <span class="bg-date pull-right">{{$check_date}}</span></p>
                    </div>
                    @if(!empty(@$payment_details->providerdetail))                    
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 billing-create-tab-b m-b-m-8 margin-t-m-8 tab-l-b-2 md-display">               
                        <p class=" "><span class="med-orange font600">Pay to Address </span></p>                                              
                        <p class="space-m-t-7"><span class="med-green">Address :</span><span> {{@$payment_details->providerdetail->address_1}}</span></p>     
                        <p class="space-m-t-7"><span class="med-green">City :</span><span> {{@$payment_details->providerdetail->city}} {!! HTML::decode( !empty($payment_details->providerdetail->state)? "- <span class='bg-state'>".@$payment_details->providerdetail->state.'</span>':"")!!}</span></p>
                        <p class="space-m-t-7 "><span class="med-green">Zip Code :</span><span> {{@$payment_details->providerdetail->zipcode5}} {{ !empty($payment_details->providerdetail->zipcode4)?'-'.$payment_details->providerdetail->zipcode4:""}}</span></p>
                    </div>
                    @endif                    
                    <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-b-m-8  tab-l-b-3 margin-t-m-8 md-display">
                        <p ><span class="med-green font600">Check Amt </span><span class="pull-right font600 js-check-amt">{{@$payment_details->payment_amt}}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Posted Amt </span> <span class="pull-right font600 js-amt-used">{{@$payment_details->amt_used}}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Balance </span> <span class="pull-right font600 med-orange js-bal-amt">{{@$payment_details->balance}}</span></p>
                        <p class="space-m-t-7"><span class="med-green font600">Unapplied </span> <span class="pull-right font600">{{@$payment_details->balance}}</span></p>
                    </div>                    
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-b-10">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Transaction Details</span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive mobile-scroll margin-t-10">
                <table class="popup-table-wo-border table margin-b-5 mobile-width"> 
                    <thead>
                        <tr>  
                            <th>DOS</th>
                            <th>Claim No</th>
                            <th>Patient Name</th>
                            <th>CPT</th>                               
                            <th>Billed</th>
                            <th>Allowed</th>                                                                                        
                            <th>Ded</th>
                            <th>Co-Pay</th>
                            <th>Co-Ins</th>
                            <th>With Held</th>
                            <th>Adj</th>
                            <th>Paid</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody> 
                        @if(!empty($payment_details->payment_claim_detail))                     
                        @foreach($payment_details->payment_claim_detail as $payment_claim_detail) 
                         @if(!empty($payment_claim_detail->claim))                         
                         <?php 
							$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_claim_detail->claim->id, 'encode');
                         	$url = url('patients/payment/popuppayment/'.$claim_id);
                         	$getpayment_data = App\Http\Helpers\Helpers::getClaimPaymentData(@$payment_claim_detail->claim->id);
                         	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_claim_detail->claim->patient->id, 'encode'); 
						?>
                        <tr>
                            <td> <a href="#js-model-popup-payment" 
                            claim_number = "{{$payment_claim_detail->claim->claim_number}}" data-payment-info = "{{$payment_claim_detail->claim->claim_number}}" data-toggle="modal" data-target="#js-model-payment" 
                            data-url="{{$url}}" class="js-modalboxopen">{{@date('m/d/Y',strtotime($payment_claim_detail->claim->date_of_service))}}</a></td>
                            <td>{{@$payment_claim_detail->claim->claim_number}}</td>
                            <?php $patient_name =  App\Http\Helpers\Helpers::getNameformat(@$payment_claim_detail->claim->patient->last_name, @$payment_claim_detail->claim->patient->first_name, @$payment_claim_detail->claim->patient->middle_name);?>
                            <td>
                                <span>                               
                                <a href="{{ url('patients/'.$patient_id.'/ledger') }}" target="_blank"> <span class="someelem" data-id="{{$payment_claim_detail->claim->patient->id}}" id="someelem{{$payment_claim_detail->claim->patient->id}}">@if($payment_claim_detail->claim->patient->title){{ @$payment_claim_detail->claim->patient->title }}. @endif{{ str_limit($patient_name,25,'...') }}</span></a> 
                                </span>
                                <div class="on-hover-content js-tooltip_{{$payment_claim_detail->claim->patient->id}}" style="display:none;">
                                    <span class="med-orange font600">@if($payment_claim_detail->claim->patient->title){{ @$payment_claim_detail->claim->patient->title }}. @endif{{ $patient_name }}</span> 
                                    <p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$payment_claim_detail->claim->patient->account_no }}
                                    <br>
                                    @if($payment_claim_detail->claim->patient->dob !='1901-01-01' && $payment_claim_detail->claim->patient->dob !='0000-00-00' && $payment_claim_detail->claim->patient->dob !='')<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$payment_claim_detail->claim->patient->dob,'claimdate') }}
										<span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$payment_claim_detail->claim->patient->dob) }} @endif
                                    <span class="font600">Gender :</span> {{ $payment_claim_detail->claim->patient->gender }}<br>
                                    <span class="font600">Ins :</span> {{ App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$payment_claim_detail->claim->patient->id)}}<br>
                                    <span class="font600">Address :</span> {{ $payment_claim_detail->claim->patient->address1 }}<br>
                                     {{ $payment_claim_detail->claim->patient->city}}, {{ $payment_claim_detail->claim->patient->state}}, {{ $payment_claim_detail->claim->patient->zip5}}-{{ $payment_claim_detail->claim->patient->zip4}}<br>
                                    @if(@$payment_claim_detail->claim->patient->phone)<span class="font600">Home Phone :</span>{{$payment_claim_detail->claim->patient->phone}} <br>@endif
                                    @if(@$payment_claim_detail->claim->patient->work_phone)<span class="font600">Work Phone :</span> {{$payment_claim_detail->claim->patient->work_phone}}@endif
                                    </p>
                               </div>
                            </td>
                            <td>{{@$payment_claim_detail->claim->cpt_codes}}</td>
                            <td>{{@$payment_claim_detail->claim->total_charge}}</td>
                            <td>{{@$payment_claim_detail->claim->total_allowed}}</td>
                            <td>{{@$getpayment_data->deductable}}</td>
                            <td>{{@$getpayment_data->copay}}</td>
                            <td>{{@$getpayment_data->coinsurance}}</td>
                            <td>{{@$getpayment_data->withheld}}</td>
                            <td>{{@$payment_claim_detail->claim->total_adjusted}}</td>
                            <td>{{@$payment_claim_detail->claim->total_paid}}</td>
                            <td>{{@$payment_claim_detail->claim->balance_amt}}</td>                                   
                        </tr>
                        @else 
                            <tr><td colspan="13" class="text-center"><span class="med-gray-dark">No payments has been done</span> </td></tr>
                        @endif
                        @endforeach
                        @else 
                            <tr><td colspan="13" class="text-center"><span class="med-gray-dark">No payments has been done</span> </td></tr>
                        @endif
                    </tbody>
                </table>                    
            </div>
        </div>
    </div>
</div><!-- /.box-body -->
<!-- Modal Payment Details ends here -->
<div id="choose_claim" class="modal fade in">
   <div class="modal-md-800">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->                         