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
            $payment = $result['payment'];
            $header = $result['header'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            $user_names = $result['user_names'];
            foreach ($payment['payment'] as $key => $value) {
                    $abb_user[] = @$value->created_user->short_name." - ".@$value->created_user->name;
                }
                $abb_user = array_unique($abb_user);
                foreach (array_keys($abb_user, ' - ') as $key) {
                    unset($abb_user[$key]);
                }
                $user_imp = implode(':', $abb_user);
         ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center" >{{$heading_name}} - <i>Patient and Insurance Payment</i></h3> </td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i=1; ?>
                            @if(isset($header) && !empty($header))
                            @foreach($header as $header_name => $header_val)
                            <span>
                            <?php $hn = $header_name; ?>
                            {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i<count((array)$header)) | @endif 
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </p>
                    </td>
                </tr>
            </table>            
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(isset($payment['payment']) && !empty($payment['payment']))
            <div>   
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>Transaction Date</th>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>DOS</th>
                            <th>Claim No</th>
                            <th>Payer</th>
                            <th>Payment Type</th>
                            <th>Check/EFT/CC/MO No</th>
                            <th>Check/EFT/CC/MO Date</th>
                            <th>Paid($)</th>
                            <th>Reference</th>
                            <th>User</th>
                        </tr>
                    </thead>  
                        <tbody>
                            @foreach($payment['payment'] as $r)
                                <tr>
                                    <?php $title = !empty($r->title)?$r->title.'. ':''; ?>
                                    <td>{{ App\Http\Helpers\Helpers::timezone($r->transaction_date, 'm/d/y') }}</td>
                                    <td>{!! $r->account_no !!}</td>
                                    <td>{!! $title.$r->last_name.', '.$r->first_name.' '.$r->middle_name !!}</td>
                                    <td>{!! ($r->dos=='')?'-Nil-':$r->dos !!}</td>
                                    <td>{!! ($r->claim_number=='')?'-Nil-':$r->claim_number !!}</td>
                                    <td>{!! $r->payer !!}</td>
                                    <td>{!! $r->pmt_mode !!}</td>
                                    <td>{!! ($r->pmt_mode_no=='')?'-Nil-':$r->pmt_mode_no !!}</td>
                                    <td>{!! $r->pmt_mode_date !!}</td>
                                    @if(isset($r->payer) && $r->payer=="Patient")
                                    <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($r->total_paid) !!}</td>
                                    @else
                                    <td class="text-right" data-format='0.00'>{!! App\Http\Helpers\Helpers::priceFormat($r->total_paid) !!}</td>
                                    @endif
                                    <td>{!! (!empty($r->reference))?$r->reference:"-Nil-" !!}</td>
                                    <td>
                                        @if($r->created_by != 0 && isset($user_names[@$r->created_by]) )
                                            {!! $user_names[@$r->created_by] !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>
            <div style="page-break-inside: avoid;" >
                <h3 style="margin-left:10px">Summary</h3>
                <table class="table-summary" style="width: 40%; border:1px solid #ccc; margin-left: 10px;margin-top:10px;">
                    @if(isset($payment['header']['Transaction Date']))
                    <tr>
                        <td>Transaction Date</td>
                        <td class="text-right">{{$payment['header']['Transaction Date']}}</td>
                    </tr>
                    @endif
                    @if(isset($payment['header']['Payer']) && $payment['header']['Payer']=="Patient Payments" || $payment['header']['Payer']=="All Payments")
                    <tr>
                        <td>Total Patient Payments</td>
                        <td class="text-right" style="@if($payment['patient_total']<0)color: #ff0000;@endif">${!! App\Http\Helpers\Helpers::priceFormat($payment['patient_total']) !!}</td>
                    </tr>
                    @endif
                    @if(isset($payment['header']['Payer']) && $payment['header']['Payer']=="Insurance Payments" || $payment['header']['Payer']=="All Payments")
                    <tr>
                        <td>Total Insurance Payments</td>
                        <td class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($payment['insurance_total']) !!}</td>
                    </tr>
                    @endif
                    @if(isset($payment['header']['Payer']) && $payment['header']['Payer']=="All Payments")
                    <tr>
                        <td>Total Payments</td>
                        <td class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($payment['patient_total']+$payment['insurance_total']) !!}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
            <ul style="line-height:20px;">
                <li>{{$user_imp}}</li>
            </ul>
        </div>
    </body>
</html>