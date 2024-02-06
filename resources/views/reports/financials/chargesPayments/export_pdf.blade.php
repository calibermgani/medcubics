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
                text-align:right !important;
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
            .text-center{text-align: center;}
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
            $billingprov = $result['billingprov'];
            $charges = $result['charges'];
            $pmt_adj = $result['pmt_adj'];
            $payments = $result['payments'];
            $practice_id = $result['practice_id'];
            $header = $result['header'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Charges & Payments Summary</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;">
                        <p class="text-center" style="font-size:11px !important;">
                            @if($header !='' && !empty($header))
                            <?php $i=1; ?>
                            @foreach($header as $header_name => $header_val)
                            <span>
                            <?php $hn = $header_name; ?>
                            {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}
                            @if($i<count((array)$header)) | @endif
                            <?php $i++; ?>
                            @endforeach
                            <?php
                                $date_cal = json_decode(json_encode($header), true);
                                $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                                $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                            ?>
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="4" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>        
        <div style="padding-top:10px;">
            @if((isset($charges) && !empty($charges)) || (isset($payments) && !empty($payments)) || (isset($pmt_adj) && !empty($pmt_adj)))
            <?php $patient_total_payment = $insurance_total_payment = $total_billed = $patient_total_adj = $insurance_total_adj = 0; ?>
            @if(isset($billingprov) && !empty($billingprov))
            <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                <thead>
                    <tr>
                        <th class="text-left">Billing</th>
                        <th>Total Charges($)</th>
                        <th>Patient Adjustments($)</th>
                        <th>Insurance Adjustments($)</th>
                        <th>Total Adjustments($)</th>
                        <th>Patient Payments($)</th>
                        <th>Insurance Payments($)</th>
                        <th>Total Payments($)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billingprov as $val)
                    <?php
                    $provider_name = str_replace(',','',$val->provider_name);
					$key = str_replace(' ','_',($provider_name));
                    $billed = isset($charges[$key]) ? $charges[$key] : 0;
                    $pat_adj = isset($pmt_adj[$key]['Patient']) ? $pmt_adj[$key]['Patient'] : 0;
                    $ins_adj = isset($pmt_adj[$key]['Insurance']) ? $pmt_adj[$key]['Insurance'] : 0;
                    $pat_pmt = isset($payments[$key]['Patient']) ? $payments[$key]['Patient'] : 0;
                    $ins_pmt = isset($payments[$key]['Insurance']) ? $payments[$key]['Insurance'] : 0;
                    $tot_adj = $pat_adj + $ins_adj;
                    $tot_pmt = $pat_pmt + $ins_pmt;
                    $total_billed += $billed;
                    $patient_total_payment += $pat_pmt;
                    $insurance_total_payment += $ins_pmt;
                    $patient_total_adj += $pat_adj;
                    $insurance_total_adj += $ins_adj;
                    ?>
                    @if($billed || $pat_adj || $ins_adj || $pat_pmt || $ins_pmt!=0)
                    <tr>
                        <td>{{str_replace('_', ' ', $key)}}</td>
                        <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat($billed)}}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($pat_adj)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($ins_adj)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($tot_adj)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($pat_pmt)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($ins_pmt)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($tot_pmt)!!}</td>
                    </tr>
                    @endif
                    @endforeach
                    <tr style="height: 10px"><td colspan="8"></td></tr>
                    <tr>
                        <td>Totals</td>
                        <td class="text-right">{{App\Http\Helpers\Helpers::priceFormat(array_sum((array)$charges))}}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_adj)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($insurance_total_adj)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_adj+$insurance_total_adj)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_payment)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($insurance_total_payment)!!}</td>
                        <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat($patient_total_payment+$insurance_total_payment)!!}</td>
                    </tr>
                </tbody>
            </table>
            @endif
            <?php
            $wallet = isset($payments['wallet']) ? $payments['wallet'] : 0;
            if ($wallet < 0)
                $wallet = 0;
            ?>
            <div style="margin-top: 15px;">
                <table width="25%">
                    <tr>
                        <td>Wallet Balance</td>
                        <td class='text-right med-green font600' style="padding-right:40px !important;">${{App\Http\Helpers\Helpers::priceFormat($wallet)}}</td>
                    </tr>
                </table>
            </div>
            @endif	
        </div>
    </body>
</html>