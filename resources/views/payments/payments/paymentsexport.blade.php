<html>
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important;
            } 
            .print-table table  tbody tr{ line-height: 26px !important; }
            .print-table table  tbody tr:nth-of-type(even) {
                background: #f3fffe !important;   
            }
            table tbody tr:nth-of-type(even) td{border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;}
            th {
                text-align:left;
                font-size:13px;  
                font-weight: 100 !important;
                border-radius: 0px !important;
            }
            tr, tr span, th, th span{line-height: 20px;}
            @page { margin: 110px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:13px; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-left{text-align: left;}
            .text-right{text-align: right;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #00877f;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .c-paid, .Paid{color:#02b424;}
            .c-denied, .Denied{color:#d93800;}
            .m-ppaid, .c-ppaid{color:#2698F8}
            .ready-to-submit, .Ready{color:#5d87ff;}
            .Rejection{color: #f07d08;}
            .Hold{color:#110010;}
            .claim-paid{background: #defcda; color:#2baa1d !important;}
            .claim-denied{ color:#d93800 !important;}
            .claim-submitted{background: #caf4f3; color:#41a7a5 !important}
            .claim-ppaid{background: #dbe7fe; color:#2f5dba !important;}
            .Patient{color:#e626d6;}
            .Submitted{color:#009ec6;}
            .Pending{color:#313e50;}
            .text-center{text-align: center;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>
    <body>
        <div class="header">
            <table style="background:#f0f0f0; padding: 0px 0px; margin-top: -20px;">
                <tr style="background:#f0f0f0;">
                    @if($export == "pdf")
                    <td colspan="5"><h3 class="text-left" style="margin-left:10px;" >Payments List</h3></td>
                    <td colspan="6" class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
                    @else
                    <td colspan="11"><h3 class="text-center">Payments List</h3></td>
                    @endif
                </tr>
            </table>
            <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
                <tr>
                    <th colspan="5" style=""><span>Created:</span> <span class="med-green">{{ \App\Http\Helpers\Helpers::timezone(date('m/d/Y H:i:s'),'m/d/y - H:i A') }}</span></th>
                    <th colspan="6" style=""><span>User :</span> <span class="med-green">{{ Auth::user()->short_name }}</span></th>
                </tr>              
            </table>
        </div>
        @if($export == "pdf")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
        <div style="padding-top:10px;">
            <div class="print-table" style="">	
                <table class="table-bordered" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; margin-left: 10px;  ">
                    <thead>
                        <tr style="background: #00837C; color: #fff;">
                            <th style="width:20px;">Payment ID</th>                                
                            <th>Payer</th>
                            <th>Check/EFT No</th>
                            <th>Mode</th>
                            <th>Check Date</th>
                            <th>Check Amt($)</th>
                            <th>Posted($)</th>
                            <th>Un Posted($)</th>
                            <th>Created On</th>
                            <th>User</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody> 
                        @if(!empty($payment_details))                        
                        @foreach($payment_details as $payment_detail)
                        <?php						
                        $type = $payment_detail->pmt_type;
                        if ($payment_detail->pmt_method == "Patient") {
                            $insurance = $payment_detail->pmt_method;
                        } else {
                            if (isset($payment_detail->insurancedetail)) {								
                                $insurance = !empty($payment_detail->insurancedetail->short_name) ? $payment_detail->insurancedetail->short_name : "";
                            } else {
                                $insurance = "";
                            }
                        }						
                        $check_mode = $payment_detail->pmt_mode;
                        $check_date = '';
                        if ($check_mode == "Check") {
                            $check_no = $payment_detail->check_details->check_no;
                            $check_date = $payment_detail->check_details->check_date;
                        } elseif ($check_mode == "EFT") {
                            $check_no = $payment_detail->eft_details->eft_no;
                            $check_date = $payment_detail->eft_details->eft_date;
                        } elseif ($check_mode == "Cash") {
                            $check_no = "-";
                        } elseif ($check_mode == "Credit") {
                            $check_no = isset($payment_detail->credit_card_details->card_last_4) ? @$payment_detail->credit_card_details->card_last_4 : '';
                        } else {
                            $check_no = ''; 
                        }
                        $check_date = (!empty($check_date) ) ? App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$check_date, '', '-') : "-";
						$bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;						
                        ?>
                        <tr>
                            <td class="text-left">{{$payment_detail->pmt_no}}</td>
                            <td class="text-left">{!!$insurance!!}</td>
                            <td class="text-left">{{$check_no}}</td>                       
                            <td class="text-left">{{$check_mode}}</td>
                            <td>{{$check_date}}</td>
                            <td class="text-right <?php echo(@$payment_detail->pmt_amt)<0?'med-red':''; ?>">{!!@$payment_detail->pmt_amt!!}</td>
                            <td class="text-right <?php echo(@$payment_detail->amt_used)<0?'med-red':''; ?>">{!!@$payment_detail->amt_used!!}</td>
                            <td class="text-right <?php echo(@$bal_amt)<0?'med-red':''; ?>">{!!@$bal_amt!!}</td>
                             <?php /*
                            <td>{{App\Http\Helpers\Helpers::dateFormat($payment_detail->created_at)}}</td>
                            */ ?>
                            <td>{{ App\Http\Helpers\Helpers::timezone($payment_detail->created_at, 'm/d/y') }}</td>
                            <td>{{ @$payment_detail->created_user->short_name }}</td>
                            <td>E</td>							
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="11"> No Records Found.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
        @if($export == "xlsx" || $export == "csv")
        <div colspan = "11" class="footer med-green text-center"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
    </body>
</html>