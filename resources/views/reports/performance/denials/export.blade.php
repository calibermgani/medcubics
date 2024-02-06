<!DOCTYPE html>
<html lang="en">
    <head>
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
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>	
    <body>
        <?php @$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="11" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>               
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;">Denial & Pending Claims Summary</td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table class="popup-table-border  table-separate table m-b-m-1"><tr><td colspan="16"> Denied/Pending Claims - Status Summary</td></tr></table>
            @if(!empty($denials['denials_billing']))
                <table class="popup-table-border  table-separate table m-b-m-1">
                    <tr>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Reasons Identified</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;"># of Claims</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Value </th>                                            
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Contracted per Fee Schedule</th>                                           
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Approx. Time line to Expect Payment</th>
                    </tr>
                    @foreach($denials['denials_billing'] as $billings)
                    @foreach($billings as $billing)
                    <tr>
                        <td class="text-left">{{ $billing['status']}}{{(!empty($billing['description']))?' - '.Str::words($billing['description'],2):'' }} </td>
                        <td class="text-left">{{array_sum($billing['claims'])}}</td>
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
                <table class="popup-table-border  table-separate table m-b-m-1">
                    <tr>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Coding Denials</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;"># of Claims</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Value </th>                                                                                        
                    </tr>
                    @foreach($denials['denials_coding'] as $coding)
                    <tr>
                        <td class="text-left">{{ $coding['status']}}{{(!empty($coding['description']))?' - '.Str::words($coding['description'],2):'' }}</td>
                        <td class="text-left">{{array_sum($coding['claims'])}}</td>
                        <td class="text-right">${{$coding['value']}}</td>                                              
                    </tr>
                    @endforeach
                </table>
            @endif
        <div colspan="5"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>