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
                border-top: 1px solid #ccc;
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
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Patient Payment Wallet</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="4" style="border:none;text-align: right !important"><span>User :</span> <span class="">{{ Auth::user()->short_name }}</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">            
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Check No/Mode</th>
                            <th>Check Date</th>
                            <th class="text-center">Check Amt($)</th>
                            <th class="text-center">Posted($)</th>
                            <th class="text-center">Un Posted($)</th>
                            <th>Posted Date</th>
                            <th>Posted By</th>
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
                            <td>{{$payment_detail->pmt_no}}</td>
                            <td>{{$check_no}}</td>
                            <td>{{$check_date}}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payment_detail->pmt_amt) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$payment_detail->amt_used) !!}</td>
                            <td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$bal_amt)!!}</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($payment_detail->created_at)}}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($payment_detail->created_by) }}</td>		
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>