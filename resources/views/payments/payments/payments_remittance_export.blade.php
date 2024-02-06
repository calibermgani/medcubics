<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Payments E-Remittance</title>
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
            @$e_remittance = $result['e_remittance'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="7" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;">Payments E-Remittance</td>
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->short_name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Received Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check No</th>                               
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check Amount($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Posted($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Un Posted($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($e_remittance as $list)
                <?php
                if(isset($list->check_details->pmt_details))  
                    $posted = $list->check_details->pmt_details->amt_used;
                elseif(isset($list->eft_details->pmt_details))
                    $posted = $list->eft_details->pmt_details->amt_used;
                else
                    $posted = 0.00;
                if(isset($list->check_details->pmt_details))
                    $un_posted = $list->check_details->pmt_details->pmt_amt - $list->check_details->pmt_details->amt_used;
                elseif(isset($list->eft_details->pmt_details))
                    $un_posted = $list->eft_details->pmt_details->pmt_amt - $list->eft_details->pmt_details->amt_used;
                else
                    $un_posted = $list->check_paid_amount;//dd($list->insurance_details);
                ?>
                <tr>
                    <td>{!! App\Http\Helpers\Helpers::dateFormat($list->receive_date) !!}</td>
                    <td style="text-align:left;">@if(!empty($list->insurance_details)){!! @$list->insurance_details->short_name !!} - {!! @$list->insurance_details->insurance_name !!} @else -Nil- @endif</td>
                    <td style="text-align:left;">{!! $list->check_no !!}</td>
                    <td>{!! App\Http\Helpers\Helpers::dateFormat($list->check_date) !!}</td>
                    <td style="text-align:right; @if(@$list->check_paid_amount < 0)  color:#ff0000; @endif " data-format="#,##0.00">{!! $list->check_paid_amount !!}</td>
                    <td style="text-align:right; @if(@$posted < 0)  color:#ff0000; @endif " data-format="#,##0.00">{!! $posted !!}</td>
                    <td style="text-align:right; @if(@$un_posted < 0)  color:#ff0000; @endif " data-format="#,##0.00">{!! $un_posted !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <td colspan="7">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>