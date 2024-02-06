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
                    <td colspan="3"><h3 class="text-left" style="margin-left:10px;" >Provider Reimbursement Analysis</h3> </td>
                    <td class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
                </tr>
            </table>
            <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
                <tr>
                    <th style=""><span>Created:</span> <span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></th>
                    <th colspan="2" style=""><span>User :</span> <span class="med-green">{{ Auth::user()->name }}</span></th>
                    <th colspan="1" style=""><span>Transaction Date :</span>  <span class="med-green">{!! @$start_date !!}   To {!! @$end_date !!}</span></th>
                </tr>
            </table>
        </div>
        @if($export == "pdf")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
        <div style="">			
            <input type="hidden" name="rendering_provider" value="{!! $rendering_provider !!}">
            <input type="hidden" name="billing_provider" value="{!! $billing_provider !!}">
            @if(count($report_details) > 0)
            @foreach($report_details as $key=>$list)					
            @foreach($list as $sublist)
            <div class="print-table" style="page-break-after: auto; page-break-inside: avoid;">
                <p style="margin-left:10px;"><span>{!! $sublist->{key((array)$sublist)}->rendering !!}</span> - <span> {!! $key !!} </span></p>
                <table class="table-bordered" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; margin-left: 10px; ">
                    <tr style="background: #00837C; color: #fff;">
                        <th>Facility Name</th>
                        <th style="text-align:right;">Charges($)</th>
                        <th style="text-align:right;">Payments($)</th> 
                        <th style="text-align:right;">Balance($)</th>
                    </tr>							
                    @foreach($sublist as $subsublist)
                    <tr>
                        <td>{!! @$subsublist->facility_name !!}</td>
                        <td style="text-align:right;">{!! @$subsublist->chrg !!}</td>
                        <td style="text-align:right;">{!! @$subsublist->ptms !!}</td>
                        <td style="text-align:right;">{!! @$subsublist->bal !!}</td>
                    </tr>
                    @endforeach
                </table>							
            </div>
            @endforeach 
            @endforeach
            @else
            <div style=""><h5>No Records Found !!</h5></div>
            @endif
        </div>
        @if($export == "xlsx" || $export == "csv")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
    </body>
</html>