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
                <td colspan="24" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="24" style="text-align:center;">Payment Analysis {{html_entity_decode('-')}} Detailed Report</td>
            </tr>
            <tr>
                <td colspan="24" style="text-align:center;"><span>User :</span> <span>@if(isset($createdBy)){{ $createdBy }}@endif</span> | <span>Created :</span> <span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="24" style="text-align:center;">
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
        @if(isset($payments) && count((array)$payments)>0)
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Transaction Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    @if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">DOS</th>                    
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>                    
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    @endif
                    @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Insurance Type</th>
                    @endif
                    @if($header->Payer=="Insurance Only")
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Payment Date</th>
                    @endif
                    @if(@$column->payment_type =='1')
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Payment Type</th>
                    @endif
                    <th valign="center"  style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Check/EFT/CC<?php if($header->Payer!="Insurance Only"){echo'/MO'; }?> No</th>
                    <th valign="center"  style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Check/EFT/CC<?php if($header->Payer!="Insurance Only"){echo'/MO'; }?> Date</th>
                    @if($header->Payer=="Insurance Only")
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Billed($)</th>
                    @endif
                    @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Allowed($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">W/O($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Ded($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Co-Pay($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Co-Ins($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Other Adjustment($)</th>
                    @endif
                    @if($header->Payer!="Patient Payments – Detailed Transaction")
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>
                    @else
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Applied($)</th>
                    @endif
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">Reference</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000 !important;font-weight: 800;font-size:12px;width:15px;">User</th>
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
                    $patient_name = $set_title."". App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name);
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
                <tr>
                    <td style="font-size: 9px;">{{ App\Http\Helpers\Helpers::timezone(@$payments_list->created_at, 'm/d/y') }}</td>
                    <td style="font-size: 9px;text-align: left;">@if(@$patient->account_no != ''){{ @$patient->account_no }} @else -Nil- @endif</td>
                    <td style="font-size: 9px;">@if($patient_name !=''){{ $patient_name }}@else -Nil- @endif</td>
                    @if($header->Payer=="Insurance Only" || $header->Payer=="Patient Payments – Detailed Transaction")
                    <td style="font-size: 9px;">{{ (isset($claim->date_of_service))?App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service,'claimdate'):'-Nil-' }}</td>                    
                    <td style="font-size: 9px;text-align: left;">{{ (isset($claim->claim_number))?@$claim->claim_number:'-Nil-' }}</td> 
                        @if($header->Payer=="Insurance Only")
                            <td style="font-size: 9px;">@if(isset($payments_list->billing_full_name) && $payments_list->billing_full_name != ''){{ @$payments_list->billing_short_name }} - {{ @$payments_list->billing_full_name }} @else -Nil- @endif</td>
                            <td style="font-size: 9px;">@if(isset($payments_list->rendering_full_name) && $payments_list->rendering_full_name != ''){{ @$payments_list->rendering_short_name }} - {{ @$payments_list->rendering_full_name }} @else -Nil- @endif</td>
                            <td style="font-size: 9px;">@if(isset($payments_list->facility_name) && $payments_list->facility_name != ''){{ @$payments_list->facility_short_name }} - {{ @$payments_list->facility_name }} @else -Nil- @endif</td>
                        @else                   
                            <td style="font-size: 9px;">@if(isset($payments_list->claim->billing_provider->provider_name) && $payments_list->claim->billing_provider->provider_name != ''){{ @$payments_list->claim->billing_provider->short_name }} - {{ @$payments_list->claim->billing_provider->provider_name }} @else -Nil- @endif</td>
                            <td style="font-size: 9px;">@if(isset($payments_list->claim->rendering_provider->provider_name) && $payments_list->claim->rendering_provider->provider_name != ''){{ @$payments_list->claim->rendering_provider->short_name }} - {{ @$payments_list->claim->rendering_provider->provider_name }} @else -Nil- @endif</td>
                            <td style="font-size: 9px;">@if(isset($payments_list->claim->facility_detail->facility_name) && $payments_list->claim->facility_detail->facility_name != ''){{ @$payments_list->claim->facility_detail->short_name }} - {{ @$payments_list->claim->facility_detail->facility_name }} @else -Nil- @endif</td>
                        @endif
                    @endif
                    @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                    <td style="font-size: 9px;">
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
                    <td style="font-size: 9px;">{{ App\Http\Helpers\Helpers::dateFormat($payments_list->posting_date) }}</td>
                    @endif
                    @if(@$column->payment_type =='1')
                    <td style="font-size: 9px;">{{ isset($payment_info->pmt_mode)?@$payment_info->pmt_mode:'-Nil-' }}</td>
                    @endif
                    @if(@$payment_info->pmt_mode =='Check')
                    <td style="font-size: 9px;text-align: left;" >
                        @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                        <?php echo "#".(($check_details->check_no));  ?>
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='EFT')
                    <td style="font-size: 9px;text-align: left;" >
                        @if(!empty(@$eft_details->eft_no) || @$eft_details->eft_no==0)
                        <?php echo "#".(($eft_details->eft_no));  ?> 
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='Money Order')
                    <td style="font-size: 9px;text-align: left;" >
                        @if(!empty(@$check_details->check_no) || @$check_details->check_no==0)
                            <?php $exp=explode("MO-", @$check_details->check_no); echo "#".$exp[1];?>
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='Credit')
                    <td style="font-size: 9px;text-align: left;" >
                        @if(@$creditCardDetails->card_last_4 != '')
							<?php echo "#".((@$creditCardDetails->card_last_4));  ?> 
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='Cash')
                    <td style="font-size: 9px;text-align: left;"> -Nil- </td>
                    @else
                    <td style="font-size: 9px;text-align: left;"> -Nil- </td>
                    @endif
                    @if(@$payment_info->pmt_mode =='Check')
                    <td style="font-size: 9px;">
                        @if(!empty(@$check_details->check_date))
                        {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }}
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='EFT')
                    <td style="font-size: 9px;">
                        @if(!empty(@$eft_details->eft_date))
                        {{ App\Http\Helpers\Helpers::dateFormat($eft_details->eft_date) }}
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='Money Order')
                    <td style="font-size: 9px;">
                        @if(!empty(@$check_details->check_date))
                        {{ App\Http\Helpers\Helpers::dateFormat($check_details->check_date) }}
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='Credit')
                    <td style="font-size: 9px;">
                        @if(@$creditCardDetails->created_at != '')
                        {{ App\Http\Helpers\Helpers::dateFormat($creditCardDetails->created_at) }}
                        @endif
                    </td>
                    @elseif(@$payment_info->pmt_mode =='Cash')                                    
                    <td style="font-size: 9px;"> -Nil- </td>
                    @else
                    <td style="font-size: 9px;"> -Nil- </td>
                    @endif
                    @if($header->Payer=="Insurance Only")
                    <td style="font-size: 9px;text-align: right; @if(@$claim->total_charge <0) color:#ff0000; @endif" class="text-right <?php echo(@$claim->total_charge)<0?'med-red':'' ?>" data-format="#,##0.00">{{ (isset($claim->total_charge))?@$claim->total_charge:'-Nil-' }}</td>
                    @endif
                    @if(@$column->ins_pat =='1' || @$column->insurance =='1')
                    <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_allowed <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_allowed)<0?'med-red':'' ?>" data-format="#,##0.00">{{ @$payments_list->total_allowed }}</td>
                    <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_writeoff <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_writeoff)<0?'med-red':'' ?>" data-format="#,##0.00">{{ @$payments_list->total_writeoff }}</td>
                    <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_deduction <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_deduction)<0?'med-red':'' ?>" data-format="#,##0.00">{{ @$payments_list->total_deduction }}</td>
                    <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_copay <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_copay)<0?'med-red':'' ?>" data-format="#,##0.00">{{ @$payments_list->total_copay }}</td>
                    <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_coins <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_coins)<0?'med-red':'' ?>" data-format="#,##0.00">{{ @$payments_list->total_coins }}</td>
                    <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_withheld <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_withheld)<0?'med-red':'' ?>" data-format="#,##0.00">{{ @$payments_list->total_withheld }}</td>
                    @endif
                    @if($header->Payer=="Insurance Only")
                        <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_paid <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_paid)<0?'med-red':'' ?>" data-format="#,##0.00"> {!! @$payments_list->total_paid !!}</td>
                    @elseif($header->Payer=="Patient Payments")
                        @if($payment_info->pmt_type =='Credit Balance')
                        <td style="font-size: 9px;text-align: right; @if(@$payments_list->pmt_amt <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->pmt_amt)<0?'med-red':'' ?>" data-format="#,##0.00"> {!! @$payments_list->pmt_amt !!}</td>
                        @else
                        <td style="font-size: 9px;text-align: right; @if(@$payments_list->pmt_amt <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->pmt_amt)<0?'med-red':'' ?>" data-format="#,##0.00"> {!! @$payments_list->pmt_amt !!}</td>
                        @endif
                    @else
                        @if($payments_list->pmt_type =='Credit Balance' && $payments_list->source_id==0)
                        <td style="font-size: 9px;text-align: right; @if( (@$payments_list->total_paid*(-1)) <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_paid*(-1))<0?'med-red':'' ?>" data-format="#,##0.00">{!! (isset($payments_list->total_paid))?(@$payments_list->total_paid*(-1)):'-Nil-' !!}</td>
                        @else
                        <td style="font-size: 9px;text-align: right; @if(@$payments_list->total_paid <0) color:#ff0000; @endif" class="text-right <?php echo(@$payments_list->total_paid)<0?'med-red':'' ?>" data-format="#,##0.00">@if($payments_list->used!=''){!! (@$payments_list->used) !!}@else{!! isset($payments_list->total_paid)?(@$payments_list->total_paid):'-Nil-' !!}@endif</td>
                        @endif
                    @endif
                    <td style="font-size: 9px;text-align:left;">@if(empty($payment_info)) -Nil- @elseif(@$payment_info->reference=='' ) -Nil- @else {{ @$payment_info->reference }} @endif</td>
                    <td style="font-size: 9px;">@if($payments_list->created_by!=''){{ App\Http\Helpers\Helpers::shortname($payments_list->created_by) }} - {{ App\Http\Helpers\Helpers::getUserFullName($payments_list->created_by) }} @else -Nil- @endif</td>
                    <?php
                        // @todo check and replace new pmt flow
                        $trans_cpt_details = [];

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
        @if($header->Payer=="Patient Payments – Detailed Transaction")
        <?php /*<p><b>Note: Amount in red are moved to wallet</b></p>*/?>        
        @endif
        @if($header->Payer!="Patient Payments – Detailed Transaction")
        <table>
            <tr>
                <td colspan="3" style="font-size:13.5;font-weight:600;color:#00877f;">Summary</td>
            </tr>
            <tbody>
                @if($header !='' && count((array)$header)>0)
                @foreach($header as $header_name => $header_val)
                @if($header_name=='Transaction Date')
                <tr class="font600">
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Transaction Date</td>
                    <td class="text-right"  style="text-align:right;font-size: 9px;">{{ @$header_val }}</td>
                </tr>
                @endif
                @if($header_name=='DOS Date')
                <tr class="font600">
                    <td colspan="2" style="font-weight:600;font-size: 9px;">DOS</td>
                    <td class="text-right" style="text-align:right;font-size: 9px;">{{ @$header_val }}</td>
                </tr>
                @endif
                @endforeach
                @endif
                @if($header->Payer!="Insurance Only")
                <tr class="font600">
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Patient Payments</td>
                    <td class="text-right <?php echo(@$dataArr->patPmt)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->patPmt <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->patPmt !!}</td>
                </tr>
                @endif
                @if($header->Payer=="Insurance Only")
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Insurance Payments</td>
                    <td class="text-right <?php echo(@$dataArr->insPmt)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->insPmt <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->insPmt !!}</td>
                </tr>
                @endif
                @if($header->Payer!="Patient Payments")
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Write-Off</td>
                    <td class="text-right <?php echo(@$dataArr->wrtOff)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->wrtOff <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->wrtOff !!}</td>
                </tr>
                <tr class="font600">
                    <td colspan="2" style="font-weight:600;font-size: 9px;">Total Other Adjustments</td>
                    <td class="text-right <?php echo(@$dataArr->other)<0?'med-red':'' ?>"style="font-size: 9px;text-align:right;@if(@@$dataArr->other <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->other !!}</td>
                </tr>   
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Deductible</td>
                    <td class="text-right <?php echo(@$dataArr->deduction)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->deduction <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->deduction !!}</td>
                </tr>
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Co-Pay</td>
                    <td class="text-right <?php echo(@$dataArr->copay)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->copay <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->copay !!}</td>
                </tr>
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Co-Insurance</td>
                    <td class="text-right <?php echo(@$dataArr->coins)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->coins <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->coins !!}</td>
                </tr>
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total EFT Payments</td>
                    <td class="text-right <?php echo(@$dataArr->eft )<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->eft  <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->eft !!}</td>
                </tr>
                @endif
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Check Payments</td>
                    <td class="text-right <?php echo(@$dataArr->check)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->check <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->check !!}</td>
                </tr>
                @if($header->Payer!="Insurance Only")
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total Cash Payments</td>
                    <td class="text-right <?php echo(@$dataArr->cash)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->cash <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->cash !!}</td>
                </tr>
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total MO Payments</td>
                    <td class="text-right <?php echo(@$dataArr->mo)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->mo <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->mo !!}</td>
                </tr>
                @endif
                <tr class="font600">
                    <td colspan="2"  style="font-weight:600;font-size: 9px;">Total CC Payments</td>
                    <td class="text-right <?php echo(@$dataArr->cc)<0?'med-red':'' ?>" style="font-size: 9px;text-align:right;@if(@$dataArr->cc <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! @$dataArr->cc !!}</td>
                </tr>
            </tbody>
        </table>
        @endif
        @endif            
        <td colspan="24" style="">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>