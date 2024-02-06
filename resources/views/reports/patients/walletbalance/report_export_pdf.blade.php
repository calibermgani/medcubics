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
            $patient = $result['patient'];
            $header = $result['header'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">            
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td class="text-center" style="line-height:8px;"><h3>{{$heading_name}} - <i>Wallet Balance</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 1; ?>
                            @if(isset($header) && !empty($header))
                            @foreach($header as $header_name => $header_val)
                            <span>
                            <?php $hn = $header_name; ?>
                            {{ @$header_name }}</span> : {{str_replace('-','/', @$header_val)}}@if($i<count((array)$header)) | @endif
                            <?php $i++; ?>
                            @endforeach
                            @endif
                        </p>
                    </td>
                </tr>
            </table>            
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="4" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">         
            @if(isset($patient) && !empty($patient))
            <div>   
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>DOB</th>
                            <th>Statements</th>
                            <th>Statement Sent</th>
                            <th>Last Statement Date</th>
                            <th>Wallet Balance($)</th>
                            <th>Pat AR($)</th>
                        </tr>
                    </thead>  
                        <tbody>
                            @foreach($patient as $r)
                                <tr>
                                    <td>{!! $r->account_no !!}</td>
                                    <td>{!! $r->last_name.', '.$r->first_name.' '.$r->middle_name !!}</td>
                                    <td>{!! $r->dob !!}</td>
                                    <td>{!! $r->statements !!}</td>
                                    <td class="text-left">{!! $r->statements_sent !!}</td>
                                    <td>{!! $r->last_statement !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->wallet_balance) !!}</td>
                                    <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($r->patient_balance) !!}</td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>      
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
    </body>
</html>