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
                border-top: 1px solid #ccc;
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
            .text-right{text-align: right !important;padding-right:5px;}
            .text-left{text-align: left !important;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;}
            .med-orange{color:#f07d08}
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;}
            .font13{font-size: 13px}
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
            .p-b-10{padding-bottom: 20px;}
        </style>
    </head>
    <body>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 10px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Claim No : {{@$claim_detail->claim_number}}</i></p></td>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;margin-top:-50px;">
            <div>
                <table>
                    <tr>
                        <th style="border:none;"></th>
                        <th style="border:none;"><span>Status :</span> <span>{{@$claim_detail->status}}</span></th>
                        <th style="border:none;"><span>Charge Amt :</span> <span>{{App\Http\Helpers\Helpers::priceFormat($claim_detail->total_charge)}}</span></th>
                        <th style="border:none;"><span>Paid :</span> <span>{!!App\Http\Helpers\Helpers::priceFormat($claim_detail->total_paid)!!}</span></th>
                        <th style="border:none;"><span>Balance :</span> <span>{!!App\Http\Helpers\Helpers::priceFormat($claim_detail->balance_amt)!!}</span></th>
                    </tr>
                </table>
            </div>
            <div>
                <table>
                    <tr>
                        <td><h3>Claim Details</h3></td>
                    </tr>
                </table>
                <table style="border:1px solid #d7f4f2;">
                    <tbody>
                        <tr>
                            <td class="font600">DOS</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($claim_detail->date_of_service,'dob')}}</td>
                            <td class="font600">Billed To</td>
                            <?php
                                if (!empty($claim_detail->insurance_details)) {
                                    $insurance_detail = App\Http\Helpers\Helpers::getInsuranceName(@$claim_detail->insurance_details->id);
                                } else {
                                    $insurance_detail = "Self";
                                }
                            ?>
                            <td>{!!$insurance_detail!!}</td>
                            <td class="font600">Wallet Balance</td>
                            <?php
                                $credit_balance = App\Models\Patients\Patient::getPatienttabData($claim_detail->patient->id);
                                $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim_detail->patient->id, 'encode');
                                $insurance_refund = '';
                                $patient_refund = '';
                                $ins_overpayment = App\Models\Payments\ClaimInfoV1::InsuranceOverPayment($claim_detail->id);
                                $insurance_refund =  App\Models\Payments\ClaimInfoV1::getRefund($claim_detail->id, 'insurance_paid_amt');
                                $patient_refund = @App\Models\Payments\ClaimInfoV1::getRefund($claim_detail->id, 'patient_paid_amt');
                            ?>
                            <td class="font600 text-right" style="padding-right: 20px;"> 
                                {{($credit_balance['wallet_balance'] == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat($credit_balance['wallet_balance'])}}
                            </td> 
                        </tr>
                        <tr>
                            <td class="font600">First Submission</td>
                            <?php if (!empty($claim_detail->submited_date) && $claim_detail->submited_date != "0000-00-00" && $claim_detail->submited_date != "1970-01-01") { ?>
                                <td><span>{{App\Http\Helpers\Helpers::dateFormat($claim_detail->submited_date)}}</span></td>
                            <?php } else { ?>
                                <td><span>- Nil -</span></td>
                            <?php } ?>
                            <td class="font600">Rendering Provider</td>
                            <td>{{@$claim_detail->rendering_provider->provider_name.' '.@$claim_detail->rendering_provider->degrees->degree_name}} </td>
                            <td class="font600">Ins Overpayment</td>
                            <td class="text-right" style="padding-right: 20px;">{!!App\Http\Helpers\Helpers::priceFormat($ins_overpayment)!!}</td>
                        </tr>
                        <tr>
                            <td class="font600">Last Submission</td>
                            <?php if (!empty($claim_detail->last_submited_date) && $claim_detail->last_submited_date != "0000-00-00") { ?>
                                <td><span>{{App\Http\Helpers\Helpers::dateFormat($claim_detail->last_submited_date)}}</span></td>
                            <?php } else { ?>
                                <td><span>- Nil -</span></td>
                            <?php } ?>
                            <td class="font600">Billing Provider</td>
                            <td>{{@$claim_detail->billing_provider->provider_name.' '.@$claim_detail->billing_provider->degrees->degree_name}} </td>
                            <td class="font600">Insurance Refund</td>
                            <td class="text-right" style="padding-right: 20px;">{{($insurance_refund == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat($insurance_refund)}}</td>
                        </tr>
                        <tr>
                            <td class="font600">Claim Type</td>
                            <td>{{ App\Models\Payments\ClaimInfoV1::getPayerIdbilledToInsurance(@$claim_detail->insurance_id)}}</td>
                            <td class="font600">Facility</td>
                            <td>{{@$claim_detail->facility_detail->facility_name}} </td>
                            <td class="font600">Patient Refund</td>
                            <td class="text-right" style="padding-right: 20px;">{{($patient_refund == 0)?"0.00":App\Http\Helpers\Helpers::priceFormat($patient_refund)}}</td>
                        </tr>
                    </tbody>                        
                </table>
            </div>
            <div>
                <table>
                    <tr>
                        <td><h3>Claim Transaction</h3></td>
                    </tr>
                </table>
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th>Trans Date</th>
                            <th>Responsibility</th>
                            <th>Description</th>
                            <th>Payment Type</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">Payments($)</th>
                            <th class="text-right">Adj($)</th>
                            <th class="text-right">Pat Bal($)</th>
                            <th class="text-right">Ins Bal($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($claim_transaction))
                        @foreach($claim_transaction as $key => $txn)
                        <tr>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($txn->txn_date,'date')}}</td>
                            <td>
                                {!! $txn->responsibility !!}
                                @if(isset($txn->resp_category) && $txn->resp_category != '')
                                    {{ substr(@$txn->resp_category, 0, 1) }}
                                @endif
                            </td>
                            <td>{!! nl2br($txn->description) !!}</td>
                            <td>{!! $txn->payment_type!!}</td>
                            <td class="text-right">{!! $txn->charges!!}</td>
                            <td class="text-right"> {!! $txn->payments!!}</td>
                            <td class="text-right">{!! $txn->adjustment!!}</td>
                            <td class="text-right">{!! $txn->pat_balance!!}</td>
                            <td class="text-right">{!! $txn->ins_balance!!}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->total_charge)!!}</th>
                            <th class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->total_paid)!!}</th>
                            <th class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->totalAdjustment)!!}</th>
                            <th class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->patient_due)!!}</th>
                            <th class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$claim_detail->insurance_due)!!}</th> 
                        </tr>
                        @else
                        <tr><td colspan="9"><p class="text-center">No payment has been done</p></td></tr>
                        @endif                        
                    </tbody>
                </table>
            </div>
            <div>
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <?php $patient_paid_amt = App\Http\Helpers\Helpers::getClaimPatPaidAmt($claim_detail->id);?>
                    <tr>
                        <td class="@if($patient_paid_amt == 0) p-b-10 @endif"><h3>CPT Transaction</h3></td>
                    </tr>                    
                    @if($patient_paid_amt != 0)
                    <tr>
                        <td><p><span class="font600">Patient Paid : </span> <span>{{$patient_paid_amt}}</span></p></td>
                    </tr>
                    @endif
                    <thead>
                        <tr>
                            <th>CPT</th>
                            <th>Trans Date</th>
                            <th>Responsibility</th>
                            <th>Description</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">Payments($)</th>
                            <th class="text-right">Adj($)</th>
                            <th class="text-right">Pat Bal($)</th>
                            <th class="text-right">Ins Bal($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $payment = 1; ?>
                        @foreach($cpt_transaction as $cptKey => $cpttx)
                        <?php
                            $cpt_code = $cpttx;                            
                            $i = 0;
                            $lTxn = end($cpttx);
                            $fTxn = isset($cpttx[0]) ? $cpttx[0] : [];
                        ?>                        
                        @foreach($cpttx as $ctxnIndex => $ctxn)
                        <?php
                            $cpt_transaction_count = count($cpttx);
                            $payment= 0;
                        ?>
                        <tr>
                            <td>@if($i == 0){{@$ctxn->cpt_code}}@endif</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($ctxn->txn_date,'date')}}</td>
                            <td>
                                {!! $ctxn->responsibility !!}
                                @if(isset($ctxn->resp_category) && $ctxn->resp_category != '')
                                    {{ substr(@$ctxn->resp_category, 0, 1) }}
                                @endif
                            </td>
                            <td>{!! nl2br($ctxn->description) !!}</td>
                            <td class="text-right">{!! $ctxn->charges !!}</td>
                            <td class="text-right">{!! $ctxn->payments !!}</td>
                            <td class="text-right">{!! $ctxn->adjustment!!}</td>
                            <td class="text-right">{!! $ctxn->pat_balance!!}</td>
                            <td class="text-right">{!! $ctxn->ins_balance!!}</td>
                        </tr>
                        <?php $i++;?>
                        @endforeach
                        @endforeach
                        @if($payment)
                            <tr><td colspan="9"><p class="text-center">No payment has been done</p></td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>