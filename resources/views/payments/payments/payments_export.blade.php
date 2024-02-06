<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Payments</title>
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
            @$payment_details = $result['payment_details'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="11" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;">Payments List</td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payment ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:20px;">Check/EFT No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Mode</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Posted($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Un Posted($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created On</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">User</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;"></th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($payment_details))
                @foreach($payment_details as $payment_detail)
                <?php
                    $type = @$payment_detail->pmt_type;
                    if ($payment_detail->pmt_method == "Patient") {
                        $insurance = $payment_detail->pmt_method;
                    } else {
                        if($payment_detail->insurance_name!='') {
                            $insurance = !empty($payment_detail->insurance_name) ? $payment_detail->insurance_name : "";
                        } else {
                            $insurance = "-Nil-";
                        }
                    }
                    $check_mode = $payment_detail->pmt_mode;
                    $check_date = '';
                    if ($check_mode == "Check" && $payment_detail->check_no!='') {
                        $check_no = $payment_detail->check_no;
                        $check_date = $payment_detail->check_date;
                    } elseif ($check_mode == "EFT" && $payment_detail->eft_no!='') {
                        $check_no = $payment_detail->eft_no;
                        $check_date = $payment_detail->eft_date;
                    } elseif ($check_mode == "Cash") {
                        $check_no = "-Nil-";
                    } elseif($check_mode == "Credit" && $payment_detail->card_last_4!='') {
                        $check_no = isset($payment_detail->card_last_4) ? @$payment_detail->card_last_4 : '';
						$check_date = isset($payment_detail->expiry_date) ? @$payment_detail->expiry_date : '';
                    } elseif($check_mode == "Money Order"){
                        $check_no = isset($payment_detail->check_no) ? str_replace("MO-", "",@$payment_detail->check_no) : '';
                    } else {
                        $check_no = '-Nil-';
                    }
                    if ($payment_detail->pmt_type == "Refund") {
                        $check_no = $check_no." - Refund";
                    }
                    $check_date = (!empty($check_date) && $check_date != '1970-01-01' && $check_date != '0000-00-00') ? App\Http\Helpers\Helpers::dateFormat($check_date) : "-Nil-";
                    $payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_detail->id, 'encode');
                    $bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;
                ?>
                <tr>
                    <td style="text-align: left;">{{$payment_detail->pmt_no}}</td>
                    <td style="text-align: left;">{!!$insurance!!}</td>
                    <td style="text-align: left;" data-format="0">{{$check_no}}</td>
                    <td style="text-align: left;">{{$check_mode}}</td>
                    <td style="text-align: left;">{{$check_date}}</td>
                    <td style="text-align: right; @if((@$payment_detail->pmt_amt) < 0 || ($payment_detail->pmt_type == 'Refund')) color:#ff0000; @endif" data-format="#,##0.00">{!!@$payment_detail->pmt_amt!!}</td>
                    <td style="text-align: right; @if((@$payment_detail->amt_used) < 0 || ($payment_detail->pmt_type == 'Refund')) color:#ff0000; @endif" data-format="#,##0.00">{!!@$payment_detail->amt_used!!}</td>
                    <td style="text-align: right; @if((@$bal_amt) < 0 || ($payment_detail->pmt_type == 'Refund')) color:#ff0000; @endif" data-format="#,##0.00">{!!@$bal_amt!!}</td>
                    <td style="text-align: left;">{{ App\Http\Helpers\Helpers::timezone($payment_detail->created_date, 'm/d/y') }}</td>
                    <td style="text-align: left;">{{ @$payment_detail->user_name }}</td>
                    <td>E</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="11">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>