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
        <div class="header">
            <?php $heading_name = App\Models\Practice::getPracticeName($practice_id); ?>
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center" >{{$heading_name}}</h3> </td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Denial & Pending Claims Summary</i></p> </td>
                </tr>
            </table>
            
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;float:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px; width: 98%">         
            <div>   
                <table class="popup-table-border  table-separate table m-b-m-1"><tr><td colspan="16" class="med-orange font600" style="font-size: 16px;"> Denied/Pending Claims - Status Summary</td></tr></table>
                @if(!empty($denials['denials_billing']))
                    <table class="popup-table-border  table-separate table m-b-m-1" style="width: 98%">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f;">Reasons Identified</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims</th>
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Value </th>                                            
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Contracted per Fee Schedule</th>                                           
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Approx. Time line to Expect Payment</th>
                        </tr>
                        @foreach($denials['denials_billing'] as $billings)
                        @foreach($billings as $billing)
                        <tr>
                            <td class="" style="line-height: 24px;">{{ $billing['status']}}{{(!empty($billing['description']))?' - '.Str::words($billing['description'],2):'' }} </td>
                            <td class="text-center">{{array_sum($billing['claims'])}}</td>
                            <td class="text-right">${{$billing['value']}}</td>
                            <td class="text-right">${{$billing['fee_schedule']}}</td>
                            <td class="text-right">21</td>   
                        </tr>
                        @endforeach
                        @endforeach
                    </table>
                @endif
                <table class="popup-table-border  table-separate table m-b-m-1">
                    <tr>
                        <td class="med-orange font600" colspan="16" style="font-size: 16px;">Coding Denial - Status Summary</td>
                    </tr>
                </table>
                 @if(!empty($denials['denials_coding']))
                    <table class="popup-table-border  table-separate table m-b-m-1" style="width: 98%">
                        <tr>
                            <th class="font600" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff;text-align: left !important; color:#00877f; text-align: left !important;">Coding Denials</th>
                            <th class="font600 text-center" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;"># of Claims</th>
                            <th class="font600 text-right" style="border-bottom: 2px solid #00877f;line-height: 24px;border-top:1px solid #fff;background: #fff; color:#00877f;">Value </th>                                                                                        
                        </tr>
                        @foreach($denials['denials_coding'] as $coding)
                        <tr>
                            <td class="" style="line-height: 24px;">{{ $coding['status']}}{{(!empty($coding['description']))?' - '.Str::words($coding['description'],2):'' }}</td>
                            <td class="text-center">{{array_sum($coding['claims'])}}</td>
                            <td class="text-right">${{$coding['value']}}</td>                                              
                        </tr>
                        @endforeach
                    </table>
                @endif
            </div>      
        </div>
    </body>
</html>