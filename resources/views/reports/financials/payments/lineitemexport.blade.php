<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Payments Analysis</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>
    <body>
        <?php
            $header = $result['header'];
            $column = $result['column'];
            $payments = $result['payments'];
            $patient_wallet_balance = $result['patient_wallet_balance'];
            $dataArr = $result['dataArr'];
            $page = $result['page'];
            $createdBy = $result['createdBy'];
            $practice_id = $result['practice_id'];
            $export = $result['export'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="23" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="23" style="text-align:center;">Payment Analysis {{html_entity_decode('-')}} Detailed Report</td>
            </tr>
            <tr>
                <td valign="center" colspan="23" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',$practice_id) }}</span></td>
            </tr>
            <tr>
                <td valign="center" colspan="23" style="text-align:center;">
                    <?php $i = 1; ?>
                    @if($header !='' && count((array)$header)>0)
                    @foreach($header as $header_name => $header_val)
                    <span>{{ @$header_name }}</span> :  {{ @$header_val }}
                    @if ($i < count((array)$header)) | @endif
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        @if(count((array)$payments)>0)
        <?php $count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; $claim_bal_total = $total_claim = $total_cpt =  0; ?>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Transaction Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Payment Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Check / EFT / CC Number</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Check / EFT / CC Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">CPT</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Billed Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Allowed Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">W/O($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Ded($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Co-Pay($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Co-Ins($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Other Adjustment($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Reference</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">User</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payments_list)
                @if($payments_list->total_paid > 0 && $payments_list->pmt_method=='Patient' || $payments_list->pmt_method=='Insurance')
                <?php
                    if ($header->Payer == "Insurance Only") {
                        $claim = @$payments_list;
                        $patient = @$payments_list;
                        $check_details = @$payments_list;
                        $eft_details = @$payments_list;
                        $creditCardDetails = @$payments_list;
                        $payment_info = $payments_list;
                        $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                        $patient_name = $set_title . "" . App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                    }else{
                        $patient = @$payments_list->claim_patient_det;
                        $claim = $payments_list->claim;
                        $check_details = $payments_list->pmt_info->check_details;
                        $eft_details = $payments_list->pmt_info->eft_details;
                        $creditCardDetails = $payments_list->pmt_info->credit_card_details;
                        $set_title = (@$patient->title) ? @$patient->title . ". " : '';
                        $patient_name = $set_title . App\Http\Helpers\Helpers::getNameformat(@$patient->last_name, @$patient->first_name, @$patient->middle_name);
                    }
                    $cpt_fin = \DB::table('pmt_claim_cpt_tx_v1')->selectRaw('pmt_claim_cpt_tx_v1.allowed, pmt_claim_cpt_tx_v1.writeoff, pmt_claim_cpt_tx_v1.deduction, pmt_claim_cpt_tx_v1.copay, pmt_claim_cpt_tx_v1.coins, pmt_claim_cpt_tx_v1.withheld, pmt_claim_cpt_tx_v1.paid, claim_cpt_info_v1.cpt_code')->leftJoin('claim_cpt_info_v1','claim_cpt_info_v1.claim_id','pmt_claim_cpt_tx_v1.claim_id')->where('pmt_claim_cpt_tx_v1.pmt_claim_tx_id','=',$payments_list->id)->groupBy('pmt_claim_cpt_tx_v1.id')->get();
                ?>
                @foreach($cpt_fin as $trans_cpt_details)
                <tr>
                    <td style="text-align:left;font-size:9px;">
                        @if(@$payments_list->created_at != "0000-00-00" && $payments_list->created_at != "1970-01-01" )
                        {{ App\Http\Helpers\Helpers::timezone($payments_list->created_at, 'm/d/y') }}
                        @endif
                    </td>
                    <td style="text-align:left;font-size:9px;">{{ $claim->claim_number }}</td>
                    <td style="text-align:left;font-size:9px;">{{ @$patient->account_no }}</td>
                    <td style="text-align:left;font-size:9px;">{{ $patient_name }}</td>
                    <td style="font-size: 9px;">
                    @if ($header->Payer == "Insurance Only")
                                {{ isset($claim->billing_short_name)?$claim->billing_short_name:'-Nil-' }}
                    @else
                        @if(isset($claim->billing_provider->provider_name) && $claim->billing_provider->provider_name != ''){{ @$claim->billing_provider->short_name }} - {{ @$claim->billing_provider->provider_name }} @else -Nil- @endif
                    @endif
                    </td>
                    <td style="font-size: 9px;">
                        @if ($header->Payer == "Insurance Only")
                            {{ isset($claim->rendering_short_name)?$claim->rendering_short_name:'-Nil-' }}
                        @else
                            @if(isset($claim->rendering_provider->provider_name) && $claim->rendering_provider->provider_name != ''){{ @$claim->rendering_provider->short_name }} - {{ @$claim->rendering_provider->provider_name }} @else -Nil- @endif
                        @endif
                    </td>
                    <td style="font-size: 9px;">
                        @if ($header->Payer == "Insurance Only")
                            {{ isset($claim->facility_short_name)?$claim->facility_short_name:'-Nil-' }}
                        @else
                            @if(isset($claim->facility_detail->facility_name) && $claim->facility_detail->facility_name != ''){{ @$claim->facility_detail->short_name }} - {{ @$claim->facility_detail->facility_name }} @else -Nil- @endif
                        @endif
                    </td>
                    @if($payments_list->payer_insurance_id==0)
                        <td style="font-size:9px;">Self</td>
                    @else
                        <?php $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName($payments_list->payer_insurance_id); ?>
                        <td style="font-size:9px;">{{ @$insurance_name }}</td>
                    @endif
                    <td class="text-left" style="text-align:left;font-size:9px;">{{ @$payments_list->pmt_mode }}</td>
                    @if($payments_list->pmt_mode =='Check')
                        <td class="text-left" style="text-align:left;font-size:9px;" data-format="0">{{ @$check_details->check_no }}</td>
                        <td class="text-left" style="text-align:left;font-size:9px;">@if(empty($check_details->check_date)) -Nil- @else {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }}  @endif</td>
                    @elseif($payments_list->pmt_mode =='Money Order')
                        <td class="text-left" style="text-align:left;font-size:9px;" data-format="0">{{ @$check_details->check_no }}</td>
                        <td class="text-left" style="text-align:left;font-size:9px;">@if(empty($check_details->check_date)) -Nil- @else {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }}  @endif</td>                    
                    @elseif($payments_list->pmt_mode =='EFT')
                        <td class="text-left" style="text-align:left;font-size:9px;" data-format="0">@if(empty($eft_details->eft_no)) -Nil- @else {{ $eft_details->eft_no }} @endif</td>
                        <td class="text-left" style="text-align:left;font-size:9px;">@if(empty($eft_details->eft_date)) -Nil- @else {{ App\Http\Helpers\Helpers::dateFormat($eft_details->eft_date)  }} @endif </td>                    
                    @elseif($payments_list->pmt_mode =='Credit')                                
                        <td class="text-left" style="text-align:left;font-size:9px;" data-format="0">@if(empty($creditCardDetails->card_last_4)) -Nil- @else {{ @$creditCardDetails->card_last_4 }} @endif</td>
                        <td class="text-left" style="text-align:left;font-size:9px;">@if(empty($creditCardDetails->card_last_4)) -Nil- @else {{ @$creditCardDetails->card_last_4 }}  @endif</td>
                    @endif                                        
                    <td>{{ date('m/d/Y',strtotime(@$claim->date_of_service)) }}</td>
                    <td style="font-size:9px;text-align: left;" class="text-left">{{ @$trans_cpt_details->claimcpt->cpt_code }}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$claim->total_charge)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$claim->total_charge)<0?'med-red':'' ?>">{!! @$claim->total_charge !!}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->allowed)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$trans_cpt_details->allowed)<0?'med-red':'' ?>">{!! @$trans_cpt_details->allowed !!}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->writeoff)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$trans_cpt_details->writeoff)<0?'med-red':'' ?>">{!! @$trans_cpt_details->writeoff !!}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->deduction)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$trans_cpt_details->deduction)<0?'med-red':'' ?>">{!! @$trans_cpt_details->deduction !!}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->copay)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$trans_cpt_details->copay)<0?'med-red':'' ?>">{!! @$trans_cpt_details->copay !!}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->coins)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$trans_cpt_details->coins)<0?'med-red':'' ?>">{!! @$trans_cpt_details->coins !!}</td>
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->withheld)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$trans_cpt_details->withheld)<0?'med-red':'' ?>">{!! @$trans_cpt_details->withheld !!}</td>
                    <?php
                        $adj = @$trans_cpt_details->adjustment+@$trans_cpt_details->with_held;
                        $trans_amt = @$trans_cpt_details->co_pay+@$trans_cpt_details->co_ins+@$trans_cpt_details->deductable;
                        $claim_paid_total += @$trans_cpt_details->patient_paid+@$trans_cpt_details->insurance_paid;
                        $bal = @$payments_list->fin[0]->patient_due+@$payments_list->fin[0]->insurance_due;
                    ?>		
                    <td data-format="#,##0.00" style="font-size:9px;text-align: right; <?php echo(@$trans_cpt_details->paid)<0?'color:#ff0000;':'' ?>"  class="text-right <?php echo(@$trans_cpt_details->paid)<0?'med-red':'' ?>">{!! @$trans_cpt_details->paid !!}</td>
                    <td style="text-align:left;font-size:9px;" class="text-left">@if(@$payments_list->reference =='') -Nil- @else {{ @$payments_list->reference }} @endif</td>
                    <td style="text-align:left;font-size:9px;" class="text-left">{{ App\Http\Helpers\Helpers::shortname($payments_list->created_by) }} - </td>
                    <?php
                        $total_amt_bal += @$bal;
                        $claim_bal_total += @$bal;
                        $count_cpt += 1;
                    ?>
                </tr>   
                @endforeach
                <?php $count++;   ?> 
                @endif
                @endforeach
            </tbody>
        </table>

        <table>
            <tr>
                <td colspan="3" style="color: #00877f;font-weight:600;">Summary</td>
            </tr>
            <tbody>
                @if($header !='' && count((array)$header)>0)
                @foreach($header as $header_name => $header_val)
                @if($header_name=='Transaction Date')
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Transaction Date</td>
                    <td style="text-align:right;font-size:9px;" class="text-right">{{ @$header_val }}</td>
                </tr>
                @endif
                @if($header_name=='DOS Date')
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">DOS</td>
                    <td style="text-align:right;font-size:9px;" class="text-right">{{ @$header_val }}</td>
                </tr>
                @endif
                @endforeach
                @endif                    
                @if($header->Payer!="Insurance Only") 
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Patient Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->patPmt)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->patPmt)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->patPmt !!}</td>
                </tr>
                @endif
                @if($header->Payer=="Insurance Only")
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Insurance Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->insPmt)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->insPmt)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->insPmt !!}</td>
                </tr>
                @endif                        
                @if($header->Payer!="Patient Payments")
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Write-Off</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->wrtOff)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->wrtOff)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->wrtOff !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Other Adjustments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->other)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->other)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->other !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Deductible</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->deduction)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->deduction)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->deduction !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Co-Pay</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->copay)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->copay)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->copay !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Co-Insurance</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->coins)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->coins)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->coins !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total EFT Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->eft)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->eft)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->eft !!}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Check Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->check)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->check)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->check !!}</td>
                </tr>
                @if($header->Payer!="Insurance Only")
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Cash Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->cash)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->cash)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->cash !!}</td>
                </tr>
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total MO Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->mo)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->mo)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->mo !!}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total CC Payments</td>
                    <td style="text-align:right;font-size:9px;<?php echo(@$dataArr->cc)<0?'color:#ff0000;':'' ?>" class="text-right <?php echo(@$dataArr->cc)<0?'med-red':'' ?>" data-format='"$"#,##0.00_-'>{!! @$dataArr->cc !!}</td>
                </tr>
            </tbody>
        </table>
        @endif 
        <td colspan="23" style="">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>