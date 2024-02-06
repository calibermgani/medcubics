<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient and Insurance Payment</title>
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
            $payment_c_summary = @$result['payment_c_summary'];
            $payment = (array)@$result['payment'];
            $user_names = (array)@$result['user_names'];
            $createdBy = @$result['createdBy'];
            $practice_id = @$result['practice_id'];
            $header = (array)@$result['header'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>                   
                <td colspan="12" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">Patient and Insurance Payment</td>
            </tr>
            <tr>
                <td valign="center" colspan="12" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td valign="center" colspan="12" style="text-align:center;">
                    <?php $i=1; ?>
                    @if(isset($header) && !empty($header))
                    @foreach($header as $header_name => $header_val)
                    <span>
                    <?php $hn = $header_name; ?>
                    {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i < count((array)$header)) | @endif 
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Transaction Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    @if($header['Payer']!="Patient Payments")
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    @endif
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payment Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Check/EFT/CC/MO No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Check/EFT/CC/MO Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Paid($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Reference</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">User</th>
                </tr>
            </thead>    
            <tbody>
                @if(count($payment['payment'])>0)
                    @foreach($payment['payment'] as $r)
                        <tr>
                            <?php $title = !empty($r->title)?$r->title.'. ':''; ?>
                            <?php /* from stored procedure  */
                                if(isset($r->patient_name) && $r->patient_name != ''){
                            ?>
                            <td>{!! $r->transaction_date !!}</td>
                            <td style="text-align:left;">{!! $r->account_no !!}</td>
                            <td>{!! $r->patient_name !!}</td>
                            <?php 
                                } else {
                            ?>
                            <td>{{ App\Http\Helpers\Helpers::timezone($r->transaction_date, 'm/d/y') }}</td>
                            <td style="text-align:left;">{!! $r->account_no !!}</td>
                            <td>{!! $title.@$r->last_name.', '.@$r->first_name.' '.@$r->middle_name !!}</td>
                            <?php } ?>
                            @if($header['Payer']!="Patient Payments")
                            <td>@if( (isset($r->payer) && $r->payer=="Patient") && $header['Payer']!="All Payments")-Nil- @else {!! ($r->dos=='')?'-Nil-':$r->dos !!} @endif</td>
                            <td style="text-align:left;">@if( (isset($r->payer) && $r->payer=="Patient") && $header['Payer']!="All Payments")-Nil- @else {!! ($r->claim_number=='')?'-Nil-':$r->claim_number !!} @endif</td>
                            @endif
                            <td>{!! $r->payer_name !!}</td>
                            <td>{!! $r->pmt_mode !!}</td>
                            <td style="text-align:left;">{!! ($r->pmt_mode_no=='')?'-Nil-':$r->pmt_mode_no !!}</td>
                            <td>{!! ($r->pmt_mode_date=='')?'-Nil-':$r->pmt_mode_date !!}</td>
                            @if(isset($r->payer) && $r->payer=="Patient")
                            <td style="text-align:right; <?php echo($r->total_paid)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->total_paid !!}</td>
                            @else
                            <td style="text-align:right; <?php echo($r->total_paid)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->total_paid !!}</td>
                            @endif
                            <td>{!! (!empty($r->reference))?$r->reference:"-Nil-" !!}</td>
                            <td>
                                @if($r->created_by != 0 )
                                    {!! \App\Http\Helpers\Helpers::user_names($r->created_by) !!} - {!! \App\Http\Helpers\Helpers::getUserFullName($r->created_by) !!}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>   
        </table>
        <table>
            <tr>
                <td colspan="2" style="font-size:13.5px;color:#00877f;font-weight:bold;"><h3>Summary</h3></td>
            </tr>
            <tbody>
                @if(isset($header))
                <tr >
                    <td style="font-weight:bold;">Transaction Date</td>
                    <td style="text-align:right;">{{$header['Transaction Date']}}</td>
                </tr>
                @endif
                @if(isset($header) && $header['Payer']=="Patient Payments" || $header['Payer']=="All Payments")
                <tr>
                    <td style="font-weight:bold;">Total Patient Payments</td>
                    <td style="text-align:right; <?php echo($payment['patient_total'])<0?'color:#ff0000':'' ?>" data-format='"$"#,##0.00_-'>{!! $payment['patient_total'] !!}</td>
                </tr>
                @endif
                @if(isset($header) && $header['Payer']=="Insurance Payments" || $header['Payer']=="All Payments")
                <tr>
                    <td style="font-weight:bold;">Total Insurance Payments</td>
                    <td style="text-align:right; <?php echo($payment['insurance_total'])<0?'color:#ff0000':'' ?>" data-format='"$"#,##0.00_-'>{!! $payment['insurance_total'] !!}</td>
                </tr>
                @endif
                @if(isset($header) && $header['Payer']=="All Payments")
                <tr>
                    <td style="font-weight:bold;">Total Payments</td>
                    <td style="text-align:right; <?php echo($payment['patient_total']+$payment['insurance_total'])<0?'color:#ff0000':'' ?>" data-format='"$"#,##0.00_-'>{!! $payment['patient_total']+$payment['insurance_total'] !!}</td>
                </tr>
                @endif
            </tbody>
        </table>
        <td colspan="12">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>