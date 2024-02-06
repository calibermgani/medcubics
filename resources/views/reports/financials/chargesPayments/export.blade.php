<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Charges Payments</title>
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
            .hide {display: none !important;}
        </style>
    </head>
    <body>
        <?php 
            $billingprov_count_b = $result['billingprov_count_b'];
            $header = $result['header'];
            $column = $result['column'];
            $billingprov = $result['billingprov'];
            $charges = $result['charges'];
            $payments = $result['payments'];
            $pmt_adj = $result['pmt_adj'];
            $createdBy = $result['createdBy'];
            $practice_id = $result['practice_id'];
            $payerType = $result['payerType'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>                   
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>                    
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Charges & Payments Summary</td>
            </tr>
            <tr>
                <td valign="center" colspan="8" style="text-align:center;">User : @if(isset($createdBy)){{ $createdBy }}@endif | Created : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</td>
            </tr>
            <tr>
                <td valign="center" colspan="8" style="text-align:center;">
                    @if($header !='' && count($header)>0)
                    <?php $i=1; ?>
                    @foreach($header as $header_name => $header_val)
                    
                    <?php $hn = $header_name; ?>
                    {{ @$header_name }} : {{str_replace('-','/', @$header_val)}}
                    @if($i < count((array)$header)) | @endif
                    <?php $i++; ?>
                    @endforeach
                    <?php
                        $date_cal = json_decode(json_encode($header), true);
                        $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                        $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                    ?>
                    @endif
                </td>
            </tr>
        </table>
        @if((isset($charges) && count($charges)>0) || (isset($payments) && count($payments)>0) || (isset($pmt_adj) && count($pmt_adj)>0))
                <?php $patient_total_payment = $insurance_total_payment = $total_billed = $patient_total_adj = $insurance_total_adj = 0; ?>
                @if(isset($billingprov) && !empty($billingprov))
                    <table>
                        <thead>
                            <tr>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billing</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Total Charges($)</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Adjustments($)</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance Adjustments($)</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Total Adjustments($)</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Payments($)</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance Payments($)</th>
                                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Total Payments($)</th>
                            </tr>
                        </thead>
                        <tbody>
                    @foreach($billingprov as $val)
                        <?php
                            $short_name = str_replace(',','',$val->short_name);
                            $short_name_key = str_replace(' ','_',($short_name));
                            $provider_name = str_replace(',','',$val->provider_name);
                            $key = str_replace(' ','_',($provider_name));
                            $billed = isset($charges[$key])?$charges[$key]:0;
                            $pat_adj = isset($pmt_adj[$key]['Patient'])?$pmt_adj[$key]['Patient']:0;
							if($payerType == 'insurance')
								$pat_adj = 0;
                            $ins_adj = isset($pmt_adj[$key]['Insurance'])?$pmt_adj[$key]['Insurance']:0;
							if($payerType == 'self')
								$ins_adj = 0;
                            $pat_pmt = isset($payments[$key]['Patient'])?$payments[$key]['Patient']:0;
							if($payerType == 'insurance')
								$pat_pmt = 0;
                            $ins_pmt = isset($payments[$key]['Insurance'])?$payments[$key]['Insurance']:0;
							if($payerType == 'self')
								$ins_pmt = 0;
                            $tot_adj = $pat_adj+$ins_adj;
                            $tot_pmt = $pat_pmt+$ins_pmt;
                            $total_billed += $billed;
                            $patient_total_payment += $pat_pmt;
                            $insurance_total_payment += $ins_pmt;
                            $patient_total_adj += $pat_adj;
                            $insurance_total_adj += $ins_adj;
                        ?>
                        @if($billed || $pat_adj || $ins_adj || $pat_pmt || $ins_pmt!=0)
                        <tr>
                            <td>{{str_replace('_', ' ', $short_name_key)}} - {{str_replace('_', ' ', $key)}}</td>
                            <td class="text-right <?php echo($billed)<0?'med-red':'' ?>" style="text-align:right; @if($billed <0) color:#ff0000; @endif" data-format="#,##0.00">{{$billed}}</td>
                            <td class="text-right <?php echo($pat_adj)<0?'med-red':'' ?>" style="text-align:right; @if($pat_adj <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $pat_adj !!}</td>
                            <td class="text-right <?php echo($ins_adj)<0?'med-red':'' ?>" style="text-align:right; @if($ins_adj <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $ins_adj !!}</td>
                            <td class="text-right <?php echo($tot_adj)<0?'med-red':'' ?>" style="text-align:right; @if($tot_adj <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $tot_adj !!}</td>
                            <td class="text-right <?php echo($pat_pmt)<0?'med-red':'' ?>" style="text-align:right; @if($pat_pmt <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $pat_pmt !!}</td>
                            <td class="text-right <?php echo($ins_pmt)<0?'med-red':'' ?>" style="text-align:right; @if($ins_pmt <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $ins_pmt !!}</td>
                            <td class="text-right <?php echo($tot_pmt)<0?'med-red':'' ?>" style="text-align:right; @if($tot_pmt <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $tot_pmt !!}</td>
                        </tr>
                        @endif
                    @endforeach
                        <tr style="height: 10px"><td colspan="8"></td></tr>
                        <tr>
                            <td>Totals</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{{ array_sum((array)$charges) }}</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{!! $patient_total_adj !!}</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{!! $insurance_total_adj !!}</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{!! $patient_total_adj+$insurance_total_adj !!}</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{!! $patient_total_payment !!}</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{!! $insurance_total_payment !!}</td>
                            <td style="text-align:right;" data-format='"$"#,##0.00_-'>{!! $patient_total_payment+$insurance_total_payment !!}</td>
                        </tr>
                    </tbody>
                    </table>
                    @endif
                    <?php
                        $wallet = isset($payments['wallet'])?$payments['wallet']:0;
                        if($wallet<0)
                            $wallet = 0;?>
                    <div style="margin: 0 15px;">
                        <table>
                            <td>Wallet Balance</td>
                            <td style="@if($tot_pmt <0) color:#ff0000; @endif font-weight:600;" data-format='"$"#,##0.00_-'>{{ $wallet }}</td>
                        </table>
                    </div>
           <?php /* <table style="display: none;">
                <tbody>
                <tr>
                    <td class="font600" colspan="6"><h3>Summary</h3></td>
                </tr>
                <tr>
                    <td style="border-bottom: 5px solid #000 !important;font-size:10px !important"></td>
                    <td style="text-align:right;border-bottom: 5px solid #000 !important;font-size:10px !important">Value($)</td>
                </tr>
                <tr>
                    <td>Total Charges</td>
                    <td data-format='0.00' class="text-right font600">${{App\Http\Helpers\Helpers::priceFormat($total_billed)}}</td>
                </tr>
                <?php
                $wallet = isset($payments['wallet']) ? $payments['wallet'] : 0;
                if ($wallet < 0)
                    $wallet = 0;
                $tot_adj = $patient_total_adj + $insurance_total_adj;
                $tot_pmt = $patient_total_payment + $insurance_total_payment + $wallet;
                ?>
                <tr>
                    <td>Wallet Balance</td>
                    <td data-format='0.00' class="text-right font600">${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
                </tr>
                <tr> 
                    <td>Patient Adjustments</td>
                    <td data-format='0.00' class="text-right font600">${!!App\Http\Helpers\Helpers::priceFormat($patient_total_adj)!!}</td>
                </tr>
                <tr>
                    <td>Insurance Adjustments</td>
                    <td data-format='0.00' class="text-right font600">${!!App\Http\Helpers\Helpers::priceFormat($insurance_total_adj)!!}</td>
                </tr>
                <tr>
                    <td>Total Adjustments</td>
                    <td data-format='0.00' class="text-right font600">${!!App\Http\Helpers\Helpers::priceFormat($tot_adj)!!}</td>
                </tr>
                <tr> 
                    <td>Patient Payments</td>
                    <td data-format='0.00' class="text-right font600">${!!App\Http\Helpers\Helpers::priceFormat($patient_total_payment+$wallet)!!}</td>
                </tr>
                <tr>
                    <td>Insurance Payments</td>
                    <td data-format='0.00' class="text-right font600">${!!App\Http\Helpers\Helpers::priceFormat($insurance_total_payment)!!}</td>
                </tr>
                <tr>
                    <td>Total Payments</td>
                    <td data-format='0.00' class="text-right font600">${!!App\Http\Helpers\Helpers::priceFormat($tot_pmt)!!}</td>
                </tr>
            </tbody>
        </table>*/ ?>
        @endif
        <div style="margin: 0 15px;">
            <table><tr></tr><tr><td colspan="8">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td></tr></table>
        </div>
    </body>
</html>