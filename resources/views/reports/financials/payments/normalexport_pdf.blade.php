<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;
            }
            th {
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-bottom: 1px solid #ccc;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary tbody tr td{font-size:11px !important;} 
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right !important;}
            .text-left{text-align: left !important;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .c-paid, .Paid{color:#02b424;}
            .c-denied, .Denied{color:#d93800;}
            .m-ppaid, .c-ppaid{color:#2698F8}
            .ready-to-submit, .Ready{color:#5d87ff;}
            .Rejection{color: #f07d08;}
            .Hold{color:#110010;}
            .claim-paid{background: #defcda; color:#2baa1d !important;}
            .claim-denied{ color:#d93800 !important;}
            .claim-submitted{background: #caf4f3; color:#41a7a5 !important}
            .claim-ppaid{background: #dbe7fe; color:#2f5dba !important;}
            .Patient{color:#e626d6;}
            .Submitted{color:#009ec6;}
            .Pending{color:#313e50;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>
    <body>
        <?php 
            $payments = $result['payments'];
            $header = $result['header'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center" >{{@$heading_name}} - <i>Payment Analysis – Detailed Report</i></h3> </td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i=1; ?>
                                @if(isset($header) && $header !='')
                                @foreach($header as $header_name => $header_val)
                                <span>{{ @$header_name }}</span> :  {{ @$header_val }} @if ($i < count((array)$header)) | @endif
                                <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none"><span>Created Date ss:</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',@$practice_id) }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">         
            @if(!empty($payments))
            <div>   
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>Acc No</th>
                            <th style="width:150px">Patient Name</th>
                            <th>DOS</th>
                            @if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")
                            <th>Claim No</th>
                            @endif
                            <th>Billing</th>
                            <th>Rendering</th>
                            <th>Facility</th>
                            @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                <th>Payer</th>
                            @endif
                            @if($header->Payer=="Insurance Only")
                            <th>Payment Date</th>
                            @endif
                            @if(@$column->payment_type =='1')
                            <th>Payment Type</th>@endif
                            <th>Check/EFT/CC<?php if($header->Payer!="Insurance Only"){echo'/MO'; }?> No</th>                        
                            <th>Check/EFT/CC<?php if($header->Payer!="Insurance Only"){echo'/MO'; }?> Date</th>
                            @if($header->Payer=="Insurance Only")
                            <th class="text-right">Billed</th>
                            @endif
                            @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                <th class="text-right">Allowed</th>
                                <th class="text-right">W/O</th>
                                <th class="text-right">Ded</th>
                                <th class="text-right">Co-Pay</th>
                                <th class="text-right">Co-Ins</th>
                                <th class="text-right">Other Adjustment</th>
                            @endif
                            @if($header->Payer!="Patient Payments – Detailed Transaction")
                            <th class="text-center">Paid($)</th>
                            @else
                            <th class="text-center">Applied($)</th>
                            @endif
                            <th style="padding-left:5px;">Reference</th>
                            <th style="padding-left:5px;">User</th>
                        </tr>
                    </thead>  
                        <?php
                            $total_ins = 0;
                            $total_pat = 0;
                            $claim_paid = [];
                        ?>
                            
                        <tbody>
                            @foreach($payments as $payments_list)
                            <?php 
								$claim = @$payments_list->claim;
								$patient = @$payments_list->claim_patient_det;
								$check_details = @$payments_list->pmt_info->check_details;
								$eft_details = @$payments_list->pmt_info->eft_details;
								$creditCardDetails = @$payments_list->pmt_info->credit_card_details;
								if($header->Payer=="Insurance Only"){
									$payment_info = @$payments_list->pmt_info;
								}elseif($header->Payer=="Patient Payments"){
									$patient = @$payments_list->patient;
									$check_details = @$payments_list->check_details;
									$eft_details = @$payments_list->eft_details;
									$creditCardDetails = @$payments_list->credit_card_details;
									$payment_info = $payments_list;
								} else{
									$payment_info = $payments_list->pmt_info;
								}
								$set_title = (@$patient->title)? @$patient->title.". ":'';
								$patient_name =    $set_title."". App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name);
                            ?>      
                            <tr> 
                                <td style="padding-left: 5px;">{{ App\Http\Helpers\Helpers::timezone(@$payments_list->created_at, 'm/d/y') }}</td> 
                                <td>{{ @$patient->account_no }}</td> 
                                <td>{{ $patient_name }} </td> 
                                <td>{{ App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service,'claimdate') }}</td>
                                @if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")
                                <td>{{ (isset($claim->claim_number))? @$claim->claim_number:'-Nil-' }}</td>
                                @endif
                                <td>{{ (isset($payments_list->claim->billing_provider->short_name))?@$payments_list->claim->billing_provider->short_name:'-Nil-' }}</td>
                                <td>{{ (isset($payments_list->claim->rendering_provider->short_name))?@$payments_list->claim->rendering_provider->short_name:'-Nil-' }}</td>
                                <td>{{ (isset($payments_list->claim->facility_detail->short_name))?@$payments_list->claim->facility_detail->short_name:'-Nil-' }}</td>
                                @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                <td>
                                    @if($payments_list->payer_insurance_id==0)
                                    Self
                                    @else          
                                    <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceName(@$payments_list->payer_insurance_id); ?>
                                    {{ $insurance_name }}
                                    @endif 
                                </td>
                                @endif
                                @if($header->Payer=="Insurance Only")
                                <td>{{ App\Http\Helpers\Helpers::dateFormat(@$payments_list->posting_date) }} </td>
                                @endif
                                @if(@$column->payment_type =='1')<td> {{ @$payment_info->pmt_mode }}</td>@endif                             
                                @if(@$payment_info->pmt_mode =='Check')
                                <td> 
                                    @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                                    {{ @$check_details->check_no }}
                                    @endif
                                </td>
                                @elseif(@$payment_info->pmt_mode =='EFT')
                                <td> 
                                    @if(!empty(@$eft_details->eft_no) || @$eft_details->eft_no==0)
                                    {{ @$eft_details->eft_no }}
                                    @endif
                                </td>
                                @elseif(@$payment_info->pmt_mode =='Money Order')
                                <td> 
                                    @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                                    <?php $exp = explode("MO-", @$check_details->check_no);
                                    echo $exp[1]; ?>
                                    @endif
                                </td>           
                                @elseif(@$payment_info->pmt_mode =='Credit')
                                <td> 
                                    @if(@$creditCardDetails->card_last_4 != '')
                                    {{ @$creditCardDetails->card_last_4 }}
                                    @endif
                                </td>
                                @elseif(@$payment_info->pmt_mode =='Cash')
                                <td> Nil </td> 
                                @else
                                <td> Nil </td> 
                                @endif
                             
                                @if(@$payment_info->pmt_mode =='Check')
                                <td class="text-left"> 
                                    @if(!empty(@$check_details->check_date))
                                    {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }} 
                                    @endif
                                </td>
                                @elseif(@$payment_info->pmt_mode =='EFT')
                                <td class="text-left"> 
                                    @if(!empty(@$eft_details->eft_date))
                                    {{ App\Http\Helpers\Helpers::dateFormat(@$eft_details->eft_date) }}
                                    @endif
                                </td>
                                @elseif(@$payment_info->pmt_mode =='Money Order')
                                <td class="text-left"> 
                                    @if(!empty(@$check_details->check_date))
                                    {{ App\Http\Helpers\Helpers::dateFormat(@$check_details->check_date) }}
                                    @endif
                                </td>           
                                @elseif(@$payment_info->pmt_mode =='Credit')
                                <td class="text-left"> 
                                    @if(@$creditCardDetails->created_at != '')
                                    {{ App\Http\Helpers\Helpers::dateFormat(@$creditCardDetails->created_at) }}
                                    @endif
                                </td>
                                @elseif(@$payment_info->pmt_mode =='Cash')                                    
                                <td class="text-left"> Nil </td> 
                                @else
                                <td class="text-left"> Nil </td> 
                                @endif
                                @if($header->Payer=="Insurance Only")
                                <td class="text-right" data-format="0.00">{{ (isset($claim->total_charge))?@$claim->total_charge:'-Nil-' }}</td>
                                @endif
                                @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                                <td class="text-right" data-format="0.00">{{ @$payments_list->total_allowed }}</td>
                                <td class="text-right" data-format="0.00">{{ @$payments_list->total_writeoff }}</td>
                                <td class="text-right" data-format="0.00">{{ @$payments_list->total_deduction }}</td>
                                <td class="text-right" data-format="0.00">{{ @$payments_list->total_copay }}</td>
                                <td class="text-right" data-format="0.00">{{ @$payments_list->total_coins }}</td>
                                <td class="text-right" data-format="0.00">{{ @$payments_list->total_withheld }}</td>
                                @endif
                                @if($header->Payer=="Insurance Only")
                                    <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid) !!}</td>
                                @elseif($header->Payer=="Patient Payments")
                                        @if($payment_info->pmt_type =='Credit Balance')
                                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->pmt_amt) !!}</td>
                                        @else
                                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->pmt_amt) !!}</td>
                                        @endif
                                @else
                                    @if($payment_info->pmt_type =='Credit Balance' && $payments_list->source_id==0)
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid*(-1)) !!}</td>
                                    @else
                                        <td class="text-right">@if(!empty(@$payments_list->used)){!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->used) !!}@else{!! App\Http\Helpers\Helpers::priceFormat(@$payments_list->total_paid) !!}@endif</td>
                                    @endif
                                @endif
                                <td class="text-left" style="padding-left:5px;">@if(@$payment_info->reference =='')-Nil-@else{{ @$payment_info->reference }}@endif</td>
                                <td class="text-left" style="padding-left:5px;">{{ @$payment_info->created_user->short_name }}</td>
                                <?php
                                // @todo check and replace new pmt flow
                                $trans_cpt_details = []; //App\Models\Patients\PaymentClaimCtpDetail::ClaimTransationDetail(@$claim->id);

                                $adj = @$payments_list->total_adjusted + @$payments_list->total_withheld;
                                $ins_over_pay = @$claim->insurance_paid - @$claim->total_allowed;
                                $trans_amt = @$trans_cpt_details->co_pay + @$trans_cpt_details->co_ins + @$trans_cpt_details->deductable;
                                $total = @$total + @$payments_list->pmt_amt;
                                if ($payments_list->pmt_method == 'Insurance')
                                    $total_ins = $total_ins + @$payments_list->pmt_amt;
                                else
                                    $total_pat = $total_pat + @$payments_list->pmt_amt;
                                ?>
                            </tr>
                            @endforeach 
                        </tbody>
                </table>
            </div>      
            @if($header->Payer=="Patient Payments – Detailed Transaction")
            <p style="padding-left: 10px; color:#00877f"><b>Note: Amount in red are moved to wallet</b></p>
            @endif  
            @if($header->Payer!="Patient Payments – Detailed Transaction")
            <div class="" style="page-break-inside: avoid;" >
                <h3 class="med-orange" style="margin-left:10px">Summary</h3>
                <table class="table-summary" style="width: 40%; border:1px solid #ccc; margin-left: 10px;margin-top:10px;">
                    <tbody>
                        @if($header !='')
                        @foreach($header as $header_name => $header_val)
                        @if($header_name=='Transaction Date')
                        <tr>
                            <td>Transaction Date</td>
                            <td class="text-right med-green font600">{{ @$header_val }}</td>
                        </tr>
                        @endif
                        @if($header_name=='DOS Date')
                        <tr>
                            <td>DOS</td>
                            <td class="text-right med-green font600">{{ @$header_val }}</td>
                        </tr>
                        @endif
                        @endforeach
                        @endif
                        @if($header->Payer!="Insurance Only")
                        <tr>
                            <td>Total Patient Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->patPmt) !!}</td>
                        </tr>
                        @endif
                        @if($header->Payer=="Insurance Only")
                        <tr>
                            <td>Total Insurance Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->insPmt) !!}</td>
                        </tr>
                        @endif
                        
                        @if($header->Payer!="Patient Payments")
                        <tr>
                            <td>Total Write-Off</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->wrtOff) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Other Adjustments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->other) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Deductible</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->deduction) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Co-Pay</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->copay) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Co-Insurance</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->coins) !!}</td>
                        </tr>
                        <tr>
                            <td>Total EFT Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->eft) !!}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Total Check Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->check) !!}</td>
                        </tr>
                        @if($header->Payer!="Insurance Only")
                        <tr>
                            <td>Total Cash Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cash) !!}</td>
                        </tr>
                        <tr>
                            <td>Total MO Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->mo) !!}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Total CC Payments</td>
                            <td data-format="0.00" class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$dataArr->cc) !!}</td>
                        </tr>
                    </tbody>
                </table>
                
            </div> 
            @endif                        
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
    </body>
</html>