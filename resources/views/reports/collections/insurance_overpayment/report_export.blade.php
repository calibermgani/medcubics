<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Insurance Over Payment</title>
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
        $overpayment = $result['overpayment'];
        $createdBy = $result['createdBy'];
        $practice_id = $result['practice_id'];
        $header = $result['header'];
        $heading_name = App\Models\Practice::getPracticeDetails();  ?>
        <table>
            <tr>                   
                <td colspan="11" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>            
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;">Insurance Over Payment</td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;">
                    <?php $i = 1; ?> 
                    @if(isset($header) && !empty($header))
                    @foreach($header as $header_name => $header_val)
                    <span>
                    <?php $hn = $header_name; ?>
                    {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}
                    @if($i < count((array)$header)) | @endif
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Transaction Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adjustments($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payments($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">AR Due($)</th>
                </tr>
            </thead>    
            <tbody> 
                @if(count($overpayment)>0)
                    @foreach($overpayment as $r)
                        <tr>
                            <?php // from stored procedure
                                if(isset($r->patient_name) && $r->patient_name != ''){ 
                            ?>
                            <td style="text-align: left;">{!! $r->claim_number !!}</td>
                            <td>{!! $r->dos !!}</td>
                            <td>{!! $r->account_no !!}</td>
                            <td>{!! $r->patient_name !!}</td>
                            <td>{!! $r->provider_short_name !!} - {!! $r->provider_name !!}</td>
                            <td>{!! $r->facility_short_name !!} - {!! $r->facility_name !!}</td>
                            <td>{!! $r->transaction_date !!}</td>
                            <td style="text-align: right; <?php echo($r->total_charge)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->total_charge !!}</td>
                            <td style="text-align: right; <?php echo($r->adjustment)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->adjustment !!}</td>
                            <td style="text-align: right; <?php echo($r->insurance_paid)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->insurance_paid !!}</td>
                            <td style="text-align: right; <?php echo($r->ar_due)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->ar_due !!}</td>
                            <?php 
                                } else {
                            ?>
                            <td style="text-align: left;">{!! $r->claim_number !!}</td>
                            <td>{!! $r->dos !!}</td>
                            <td>{!! $r->account_no !!}</td>
                            <td>{!! $r->last_name.', '.$r->first_name.' '.$r->middle_name !!}</td>
                            <td>{!! $r->provider_short_name !!} - {!! $r->provider_name !!}</td>
                            <td>{!! $r->facility_short_name !!} - {!! $r->facility_name !!}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat($r->date, 'date') }}</td>
                            <td style="text-align: right; <?php echo($r->total_charge)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->total_charge !!}</td>
                            <td style="text-align: right; <?php echo($r->adjustment)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->adjustment !!}</td>
                            <td style="text-align: right; <?php echo($r->insurance_paid)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->insurance_paid !!}</td>
                            <td style="text-align: right; <?php echo($r->ar_due)<0?'color:#ff0000':'' ?>"  data-format="#,##0.00">{!! $r->ar_due !!}</td>
                            <?php } ?>
                        </tr>
                    @endforeach
                @endif
            </tbody>   
        </table>
        <td colspan="11">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>