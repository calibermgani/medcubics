<?php
try {
    ?>
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: <span class="med-green">@if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</span></h3>
            <div class="pull-right">
                <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
            </div>
        </div>
        <div class="box-body"><!-- Box Body Starts -->
            <?php $i=1; ?>
            @if(isset($header) && $header !='')
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Payment Analysis – Detailed Report</h3>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5 text-center">
                    @foreach($header as $header_name => $header_val)
                    <span class="med-green">{{ @$header_name }}</span> :  {{ @$header_val }} @if ($i < count((array)$header)) | @endif
                        <?php $i++; ?>
                    @endforeach
                </div>
            </div>
            @endif
            @if(isset($payments) && $payments !='')
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
                <div class="box box-info no-shadow no-bottom no-border">
                    <div class="box-body no-padding">
                        <div class="table-responsive mobile-lg-scroll">
                            <table id="sort_list_noorder" class="table table-bordered table-striped  mobile-lg-width">
                                <thead>
                                    <tr>
                                        <th>Transaction Date</th>
                                        <th>Acc No</th>
                                        <th  class="td-c-10">Patient Name</th>
                                        @if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")
                                        <th>DOS</th>                                        
                                        <th>Claim No</th>                                        
                                        <th>Billing</th>
                                        <th>Rendering</th>
                                        <th>Facility</th>
                                        @endif
                                        @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                        <th>Payer</th>
                                        <th>Insurance Type</th>
                                        @endif
                                        @if($header->Payer=="Insurance Only")
                                        <th>Payment Date</th>
                                        @endif
                                        @if(@$column->payment_type =='1')<th>Payment Type</th>@endif
                                        <th>Check/EFT/CC<?php if ($header->Payer != "Insurance Only") {
        echo'/MO';
    } ?> No</th>
                                        <th>Check/EFT/CC<?php if ($header->Payer != "Insurance Only") {
        echo'/MO';
    } ?> Date</th>
                                        @if($header->Payer=="Insurance Only")
                                        <th class="text-right">Billed($)</th>
                                        @endif
                                        @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                        <th class="text-right">Allowed($)</th>
                                        <th class="text-right">W/O($)</th>
                                        <th class="text-right">Ded($)</th>
                                        <th class="text-right">Co-Pay($)</th>
                                        <th class="text-right">Co-Ins($)</th>
                                        <th class="text-right">Other Adjustment($)</th>
                                        @endif
                                        @if($header->Payer!="Patient Payments – Detailed Transaction")
                                        <th class="text-right">Paid($)</th>
                                        @else
                                        <th class="text-right">Applied($)</th>
                                        @endif
                                        <th>Reference</th>
                                        <th>User</th>
                                    </tr>
                                </thead>  
                                <?php
                                        $total_ins = 0;
                                        $total_pat = 0;
                                        $claim_paid = [];
                                    ?> 
                                <tbody>
                                    @foreach($payments as $key => $payments_list)
                                    <?php

                                    $claim = @$payments_list->claim;
                                    $patient = @$payments_list->claim_patient_det;
                                    $check_details = @$payments_list->pmt_info->check_details;
                                    $eft_details = @$payments_list->pmt_info->eft_details;
                                    $creditCardDetails = @$payments_list->pmt_info->credit_card_details;
                                    if ($header->Payer == "Insurance Only") {
                                        $payment_info = @$payments_list->pmt_info;
                                    } elseif ($header->Payer == "Patient Payments") {
                                        $patient = @$payments_list->patient;
                                        $check_details = @$payments_list->check_details;
                                        $eft_details = @$payments_list->eft_details;
                                        $creditCardDetails = @$payments_list->credit_card_details;
                                        $payment_info = $payments_list;
                                    } else {
                                        $payment_info = $payments_list->pmt_info;
                                    }
                                    $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                                    $patient_name = $set_title . "" . App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                                    if ($header->Payer == "Insurance Only") {
                                        $claim = @$payments_list;
                                        $patient = @$payments_list;
                                        $check_details = @$payments_list;
                                        $eft_details = @$payments_list;
                                        $creditCardDetails = @$payments_list;
                                        $payment_info = $payments_list;
                                        $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                                        $patient_name = $set_title . "" . App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                                    }
                                    ?>

                                    <tr style="cursor:default;"> 
                                        {{ logger('claim no - '.@$claim->claim_number.' ## created at Log - '.@$payments_list->created_at) }}
                                        <td>{{ App\Http\Helpers\Helpers::timezone(@$payments_list->created_at, 'm/d/y') }}</td> 
                                        <td>{{ !empty($patient->account_no)? $patient->account_no : '-Nill-' }}</td> 
                                        <td>{{ !empty($patient_name)? $patient_name : '-Nill-' }}</td>
                                        @if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")
                                                <td>{{ (!empty($claim->date_of_service))?App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service,'claimdate'):'-Nil-' }}</td>                                        
                                                <td>{{ (!empty($claim->claim_number))?@$claim->claim_number:'-Nil-' }}</td>                                        
                                            @if($header->Payer=="Insurance Only")
                                                <td>{{ (!empty($payments_list->billing_short_name))?@$payments_list->billing_short_name:'-Nil-' }}</td>
                                                <td>{{ (!empty($payments_list->rendering_short_name))?@$payments_list->rendering_short_name:'-Nil-' }}</td>
                                                <td>{{ (!empty($payments_list->facility_short_name))?@$payments_list->facility_short_name:'-Nil-' }}</td>
                                            @else
                                                <td>{{ (!empty($payments_list->claim->billing_provider->short_name))?@$payments_list->claim->billing_provider->short_name:'-Nil-' }}</td>
                                                <td>{{ (!empty($payments_list->claim->rendering_provider->short_name))?@$payments_list->claim->rendering_provider->short_name:'-Nil-' }}</td>
                                                <td>{{ (!empty($payments_list->claim->facility_detail->short_name))?@$payments_list->claim->facility_detail->short_name:'-Nil-' }}</td>
                                            @endif
                                        @endif
                                        @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                        <td>
                                            @if($payments_list->payer_insurance_id==0)
                                            Self
                                            @else          
    <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceNameWithType($payments_list->payer_insurance_id);?>
                                            {{ !empty($insurance_name['insurance'])? $insurance_name['insurance'] : '-Nil-' }}
                                            @endif 
                                        </td>
										<td>
										{{ @$insurance_name['insuranceType'] }}
										</td>
                                        @endif
                                        @if($header->Payer=="Insurance Only")
                                        <td>{{ !empty($payments_list->posting_date)? App\Http\Helpers\Helpers::dateFormat($payments_list->posting_date) : '-Nil-' }} </td>
                                        @endif
                                        @if(@$column->payment_type =='1')<td> {{ !empty($payment_info->pmt_mode)?@$payment_info->pmt_mode:'-Nil-' }} </td>@endif								
                                        @if(@$payment_info->pmt_mode =='Check')
                                        <td> 
                                            @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                                            {{ @$check_details->check_no }}
                                            @else
                                            {{ '-Nill-' }}
                                            @endif
                                        </td>
                                        @elseif(@$payment_info->pmt_mode =='EFT')
                                        <td> 
                                            @if(!empty(@$eft_details->eft_no) || @$eft_details->eft_no==0)
                                            {{ @$eft_details->eft_no }}
                                            @else
                                            {{ '-Nill-' }}
                                            @endif
                                        </td>
                                        @elseif(@$payment_info->pmt_mode =='Money Order')
                                        <td> 
                                            @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                                            <?php $exp = explode("MO-", @$check_details->check_no);
                                            echo (!empty($exp[1]))?$exp[1]:'-Nill-'; ?>
                                            @endif
                                        </td>           
                                        @elseif(@$payment_info->pmt_mode =='Credit')
                                        <td> 
                                            @if(@$creditCardDetails->card_last_4 != '')
                                            {{ @$creditCardDetails->card_last_4 }}
                                            @else
                                            {{ '-Nill-' }}
                                            @endif
                                        </td>
                                        @elseif(@$payment_info->pmt_mode =='Cash')
                                        <td> -Nil- </td> 
                                        @else
                                        <td> -Nil- </td> 
                                        @endif
                                        @if(@$payment_info->pmt_mode =='Check')
                                        <td> 
                                            @if(!empty(@$check_details->check_date)  && $check_details->check_date !== '0000-00-00')
                                            {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }} 
                                            @else
                                            <?php echo '-Nill-'; ?>
                                            @endif
                                        </td>
                                        @elseif(@$payment_info->pmt_mode =='EFT')
                                        <td> 
                                            @if(!empty(@$eft_details->eft_date))
                                            {{ App\Http\Helpers\Helpers::dateFormat($eft_details->eft_date) }}
                                            @else
                                            {{ '-Nill-' }}
                                            @endif
                                        </td>
                                        @elseif(@$payment_info->pmt_mode =='Money Order')
                                        <td> 
                                            @if(!empty(@$check_details->check_date) && $check_details->check_date !== '0000-00-00')
                                            {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }}
                                            @else
                                            <?php echo '-Nill-'; ?>
                                            @endif

                                        </td>           
                                        @elseif(@$payment_info->pmt_mode =='Credit')
                                        <td> 
                                            @if(@$creditCardDetails->created_at != '')
                                            {{ App\Http\Helpers\Helpers::dateFormat($creditCardDetails->created_at) }}
                                            @else
                                            {{ '-Nill-' }}
                                            @endif
                                        </td>
                                        @elseif(@$payment_info->pmt_mode =='Cash')                                    
                                            <td> -Nil- </td> 
                                        @else
                                            <td> -Nil- </td> 
                                        @endif
                                        @if($header->Payer=="Insurance Only")
                                            <td class="text-right">{{ (!empty($claim->total_charge))?@$claim->total_charge:'-Nil-' }}</td>
                                        @endif
                                        @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                            <td class="text-right">{{ @$payments_list->total_allowed }}</td>
                                            <td class="text-right">{{ @$payments_list->total_writeoff }}</td>
                                            <td class="text-right">{{ @$payments_list->total_deduction }}</td>
                                            <td class="text-right">{{ @$payments_list->total_copay }}</td>
                                            <td class="text-right">{{ @$payments_list->total_coins }}</td>
                                            <td class="text-right">{{ @$payments_list->total_withheld }}</td>
                                        @endif
                                        @if($header->Payer=="Insurance Only")
                                            <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid) !!}</td>
                                        @elseif($header->Payer=="Patient Payments")
                                            @if($payment_info->pmt_type =='Credit Balance')
                                                <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->pmt_amt) !!}</td>
                                            @else
                                                <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->pmt_amt) !!}</td>
                                            @endif
                                        @else
                                            @if($payments_list->pmt_type =='Credit Balance' && $payments_list->source_id==0)
                                                <td class="text-right"> {!! isset($payments_list->total_paid)?App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid*(-1)):'-Nil-' !!}</td>
                                            @else
                                                <td class="text-right"> @if($payments_list->used!=''){!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->used) !!}@else{!! isset($payments_list->total_paid)?App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid):'-Nil-' !!}@endif</td>
                                            @endif
                                        @endif
                                        <td>@if(empty($payment_info)) -Nil- @elseif(@$payment_info->reference=='' ) -Nil- @else {{ @$payment_info->reference }} @endif</td>
                                        <td>{{ App\Http\Helpers\Helpers::shortname($payments_list->created_by) }}</td>
                                        <?php
                                        // @todo check and replace new pmt flow
                                        $trans_cpt_details = []; //App\Models\Patients\PaymentClaimCtpDetail::ClaimTransationDetail(@$claim->id);

                                        $adj = @$payments_list->total_adjusted + @$payments_list->total_withheld;
                                        $ins_over_pay = @$claim->insurance_paid - @$claim->total_allowed;
                                        $trans_amt = @$trans_cpt_details->co_pay + @$trans_cpt_details->co_ins + @$trans_cpt_details->deductable;
                                        $total = $total + @$payments_list->pmt_amt;
                                        if ($payments_list->pmt_method == 'Insurance')
                                            $total_ins = $total_ins + @$payments_list->pmt_amt;
                                        else
                                            $total_pat = $total_pat + @$payments_list->pmt_amt;
                                        ?>
                                    </tr>
                                    @endforeach	
                                </tbody>
                            </table>
                        </div><!-- /.box-body -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                        </div>                       
                        @if($header->Payer=="Patient Payments – Detailed Transaction")
                         <?php /*<p><b>Note: Amount in red are moved to wallet</b></p>*/?>
                        @endif
                    </div>
                </div>		
            </div>
            @if($header->Payer!="Patient Payments – Detailed Transaction")
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"  style="border-top: 1px solid #f0f0f0;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-5">
                    <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                        <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                    </div>
                    <!-- /.box-header -->
                    
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 margin-b-10">
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                        <tbody>
                            @if(isset($header) && $header !='')
                            @foreach($header as $header_name => $header_val)
                            @if($header_name=='Transaction Date')
                            <tr>
                                <td>Transaction Date</td>
                                <td class="med-green font600 text-right">{{ @$header_val }}</td>
                            </tr>
                            @endif
                            @if($header_name=='DOS Date')
                            <tr>
                                <td>DOS</td>
                                <td class="med-green font600 text-right">{{ @$header_val }}</td>
                            </tr>
                            @endif
                            @endforeach
                            @endif
                            @if($header->Payer!="Insurance Only")
                            <tr>
                                <td>Total Patient Payments</td>
                                <td class="@if($dataArr->patPmt>0)med-green @endif font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->patPmt) !!}</td>
                            </tr>
                            
                           
                            @endif
                            @if($header->Payer=="Insurance Only")
                            <tr>
                                <td>Total Insurance Payments</td>
                                <td class="@if($dataArr->patPmt>0)med-green @endif font600 text-right">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->insPmt) !!}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    </div>
					
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate border-radius-4">    
                        <tbody>                                                

                            <tr>                            
                                @if($header->Payer!="Patient Payments")
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->wrtOff) !!}</h3>
                                    <h4 class="text-center"><i>Write-Off</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->other) !!}</h3>
                                    <h4 class="text-center"><i>Other Adjustments</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->deduction) !!}</h3>
                                    <h4 class="text-center"><i>Deductible</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->copay) !!}</h3>
                                    <h4 class="text-center"><i>Co-Pay</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->coins) !!}</h3>
                                    <h4 class="text-center"><i>Co-Insurance</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->eft) !!}</h3>
                                    <h4 class="text-center"><i>EFT Payments</i></h4>
                                </td>
                                @endif

                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->check) !!}</h3>
                                    <h4 class="text-center"><i>Check Payments</i></h4>
                                </td>

                                @if($header->Payer!="Insurance Only")
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cash) !!}</h3>
                                    <h4 class="text-center"><i>Cash Payments</i></h4>
                                </td>
                                <td style="border-right: 1px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->mo) !!}</h3>
                                    <h4 class="text-center"><i>MO Payments</i></h4>
                                </td>
                                @endif

                                <td style="border-right: 0px solid #ccc;border-top:0px solid #fff !important">
                                    <h3 class="text-center dashboard-number">$ {!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cc) !!}</h3>
                                    <h4 class="text-center"><i>CC Payments</i></h4>
                                </td>                               
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>	
            @endif
            @else
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>	
            @endif		

        </div>	
    </div>
    <link href="https://fonts.googleapis.com/css?family=Fjalla+One&display=swap" rel="stylesheet">
    <?php
} catch (Exception $e) {
    \Log::info("Error" . $e->getMessage());
}
?>