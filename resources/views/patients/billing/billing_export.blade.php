<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient Claims List</title>
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
            .color-status-Hold { color:#c0c0c0;}
            .color-status-Paid { color:#02b424;}
            .color-status-Rejection { color:#f07d08;}
            .color-status-Denied { color:#d93800;}
            .color-status-Patient { color:#e626d6;}
            .color-status-Pending { color:#313e50;}
            .color-status-Ready { color:#5d87ff;}
            .color-status-Submitted { color:#009ec6;}
        </style>
    </head>	
    <body>
        <?php 
            @$claims_lists = $result['claims_lists'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>                   
                <td colspan="12" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">Patient Claims List</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billing</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed To</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Unbilled($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed($)</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>                 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Sub Status</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($claims_lists as $claims)
                <?php
                    // When billed amount comes unbilled amount should not come
                    $charge_amt = App\Http\Helpers\Helpers::BilledUnbilled($claims);
                    $billed = isset($charge_amt['billed'])?$charge_amt['billed']:0.00;
                    $unbilled = isset($charge_amt['unbilled'])?$charge_amt['unbilled']:0.00;
                    $total_paid = isset($claims->total_paid)?$claims->total_paid:0.00;
                    $ar_balance = isset($claims->balance_amt)?$claims->balance_amt:0.00;
                ?>
                <tr> 
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$claims->date_of_service,'claimdate') }}</td>
                    <td style="text-align:left;">{{@$claims->claim_number}}</td>
                    <td>@if(isset($claims->rendering_full_name) && $claims->rendering_full_name!=''){{@$claims->rendering_short_name}} - {{@$claims->rendering_full_name}} @else -Nil- @endif</td>
                    <td>@if(isset($claims->billing_full_name) && $claims->billing_full_name!=''){{@$claims->billing_short_name}} - {{@$claims->billing_full_name}} @else -Nil- @endif</td>
                    <td>@if(isset($claims->facility_name) && $claims->facility_name!=''){{@$claims->facility_short_name}} - {{@$claims->facility_name}} @else -Nil- @endif</td>
                    <td><?php echo(@$claims->self_pay)=="Yes"?"Self": App\Http\Helpers\Helpers::getInsuranceName(@$claims->insurance_id) ; ?></td>
                    <td style="text-align:right; @if(@$unbilled < 0) color:#ff0000; @endif" data-format="#,##0.00">{{@$unbilled}}</td>
                    <td style="text-align:right; @if(@$billed < 0) color:#ff0000; @endif" data-format="#,##0.00">{{@$billed}}</td>
                    <td style="text-align:right; @if(@$total_paid < 0) color:#ff0000; @endif" data-format="#,##0.00">{{$total_paid}}</td>
                    <td style="text-align:right; @if(@$ar_balance < 0) color:#ff0000; @endif" data-format="#,##0.00">{{$ar_balance}}</td>
                    <td>{{@$claims->status}}</td>
                    <td>
                        @if(isset($claims->sub_status_desc))
                            {{ $claims->sub_status_desc }}
                        @else 
                            -Nil-
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="12">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>