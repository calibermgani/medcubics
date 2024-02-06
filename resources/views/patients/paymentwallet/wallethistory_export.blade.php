<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient - Payment Wallet</title>
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
            @$payments = $result['payments'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Patient Payment Wallet</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payment ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check No/Mode</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Check Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Posted($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Un Posted($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Posted Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Posted By</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($payments as $payment_detail)
                <?php
                    $type = ($payment_detail->source != 'refundwallet') ? $payment_detail->pmt_type : "Refund";
                    $check_date = '';
                    $check_no   = '';
                    if ($payment_detail->pmt_mode == "Check" ) {
                        $check_no = @$payment_detail->check_details->check_no;
                        $check_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_detail->check_details->check_date);                            
                    } else if ($payment_detail->pmt_mode == "EFT") {
                        $check_no = @$payment_detail->eft_details->eft_no;
                        $check_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_detail->eft_details->eft_date);
                    } else if ($payment_detail->pmt_mode == "Cash") {
                        $check_no = "Cash";
                    } else if ($payment_detail->pmt_mode == "Credit Balance") {
                        $check_no = "Credit Balance";                            
                    } elseif($payment_detail->pmt_mode == "Credit" ){
                        if(isset($payment_detail->credit_card_details))
                            $check_no = (isset($payment_detail->credit_card_details->card_last_4) ? @$payment_detail->credit_card_details->card_last_4." - " : '')."Credit ";
                        else
                            $check_no = "Credit ";                            
                    } elseif($payment_detail->pmt_mode == "Money Order"){
                        $check_no = (isset($payment_detail->check_details->check_no) ? str_replace("MO-", "",@$payment_detail->check_details->check_no)." - " : '')."Money Order";
                        $check_date = App\Http\Helpers\Helpers::checkAndDisplayDateInInput($payment_detail->check_details->check_date);
                    } else {
                        $check_no = "-Nil-";
                    }
                    if ($payment_detail->pmt_type == "Refund") {
                        $check_no = $check_no." - Refund";
                    }
                    $check_date = ($check_date != '') ? $check_date : "-Nil-";
                    $payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_detail->id, 'encode');
                    $bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;
                ?>
                <tr>
                    <td style="text-align:left;">{{$payment_detail->pmt_no}}</td>
                    <td style="text-align:left;">{{$check_no}}</td>
                    <td>{{$check_date}}</td>
                    <td style="text-align:right; @if(@$payment_detail->pmt_amt <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$payment_detail->pmt_amt !!}</td>
                    <td style="text-align:right; @if(@$payment_detail->amt_used <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$payment_detail->amt_used !!}</td>
                    <td style="text-align:right; @if(@$bal_amt <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$bal_amt !!}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat($payment_detail->created_at) }}</td>
                    <td>{{ App\Http\Helpers\Helpers::getUserFullName($payment_detail->created_by) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <td colspan="8">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>