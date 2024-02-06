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
        </style>
    </head>	
    <body>
        <div class="header">
            <table  style="background:#f0f0f0; padding: 0px 0px; margin-top: -20px;">
                <tr style="background:#f0f0f0;">
                    <td colspan="3"><h3 class="text-left" style="margin-left:10px;" >Payer Analysis</h3> </td>
                    <td colspan="2" class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
                </tr>
            </table>
            <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
                <tr>
                    <th colspan="1" style=""><span>Created:</span> <span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></th>
                    <th colspan="2" style=""><span>User :</span> <span class="med-green">{{ Auth::user()->name }}</span></th>
                    <th colspan="2" style=""><span>Transaction Date :</span>  <span class="med-green">{!! @$start_date !!}   To {!! @$end_date !!}</span></th>
                </tr>
            </table>
        </div>
        @if($export == "pdf")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
        <div style="">			
            @if(count($claim_details) > 0)
            <div class="print-table" style="">	
                <table class="table-bordered" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; margin-left: 10px;  ">
                    <thead>
                        <tr style="background: #00837C; color: #fff;">
                            <th>Payer</th>
                            <th style="text-align:right;">Billed($)</th>
                            <th style="text-align:right;">Paid($)</th>
                            <th style="text-align:right;">Adj($)</th>
                            <th style="text-align:right;">Balance($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
							$total_billed = 0;
							$total_paids  = 0;
							$total_adjusted = 0;
							$total_balanced = 0;
							@endphp
                        @foreach($claim_details as $list)
                        @php
							$total_billed = $total_billed + $list->Billed;
							if($list->insurance_details != NULL)
								$total_paids = $total_paids + $list->insurancepaid;
							else
								$total_paids = $total_paids + $list->patientpaid;
							$total_adjusted = $total_adjusted + $list->adjusted;
							$total_balanced = $total_balanced + $list->balanced;
						@endphp
                        <tr >
                            @if($list->insurance_details != NUll)
                            <td>{!! $list->insurance_details->insurance_name !!}</td>
                            <td style="text-align:right;">{!! $list->Billed !!}</td>
                            <td style="text-align:right;">{!! $list->insurancepaid !!}</td>
                            <td style="text-align:right;">{!! $list->adjusted !!}</td>
                            <td style="text-align:right;">{!! $list->balanced !!}</td>
                            @else
                            <td>Patient</td>
                            <td style="text-align:right;">{!! $list->Billed !!}</td>
                            <td style="text-align:right;">{!! $list->patientpaid !!}</td>
                            <td style="text-align:right;">{!! $list->adjusted !!}</td>
                            <td style="text-align:right;">{!! $list->balanced !!}</td>
                            @endif   	
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding-left:10px;page-break-after: auto; page-break-inside: avoid;border-top: 1px solid #f0f0f0;">
                <h3 style="color:#f07d08 !important;">Summary</h3>
                <table style="border: 1px solid; border-radius:4px; width:40%; border-spacing:0px;font-weight:normal;">
                    <thead>
                        <tr style="background:#00877f;color:#fff;">
                            <th style="">Title</th>
                            <th style="text-align:right;">Value($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Billed</td>
                            <td style="text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat($total_billed) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Paid</td>
                            <td style="text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat($total_paids) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Adjustments</td>
                            <td style="text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat($total_adjusted) !!}</td>
                        </tr>
                        <tr>
                            <td>Total Balance</td>
                            <td style="text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat($total_balanced) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div style=""><h5>No Records Found !!</h5></div>
            @endif
        </div>
        @if($export == "xlsx" || $export == "csv")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
    </body>
</html>