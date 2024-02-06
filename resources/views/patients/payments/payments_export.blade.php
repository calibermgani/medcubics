<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patient Payments List</title>
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
                <td colspan="13" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="13" style="text-align:center;">Patient Payments List</td>
            </tr>
            <tr>
                <td colspan="13" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
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
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Charge Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>                 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Adjustments($)</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Ins Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                </tr>
            </thead>    
            <tbody>
                @if(!empty($claims_lists))
                @foreach($claims_lists as $claims_list)
                <tr>
                    <td>{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claims_list->date_of_service) }}</td>
                    <td>{{@$claims_list->claim_number}}</td>
                    <td>@if(isset($claims_list->rendering_full_name) && $claims_list->rendering_full_name!=''){{@$claims_list->rendering_short_name}} - {{@$claims_list->rendering_full_name}} @else -Nil- @endif</td>
                    <td>@if(isset($claims_list->billing_full_name) && $claims_list->billing_full_name!=''){{@$claims_list->billing_short_name}} - {{@$claims_list->billing_full_name}} @else -Nil- @endif</td>
                    <td>@if(isset($claims_list->facility_name) && $claims_list->facility_name!=''){{@$claims_list->facility_short_name}} - {{@$claims_list->facility_name}} @else -Nil- @endif</td>
                    @if(empty($claims_list->insurance_details))
                    <td>Self</td>  
                    @else                                          
                    <td>{!!App\Http\Helpers\Helpers::getInsuranceFullName(@$claims_list->insurance_id)!!}</td>
                    @endif
                    <td style="text-align:right; @if(@$claims_list->total_charge <0) color:#ff0000; @endif" data-format="#,##0.00" >{!!@$claims_list->total_charge!!}</td>
                    <td style="text-align:right; @if(@$claims_list->total_paid <0) color:#ff0000; @endif" data-format="#,##0.00" >{!!@$claims_list->total_paid!!}</td>
                    <td style="text-align:right; @if(@$claims_list->totalAdjustment <0) color:#ff0000; @endif" data-format="#,##0.00" >{!! $claims_list->totalAdjustment!!}</td>
                    <td style="text-align:right; @if(@$claims_list->patient_due <0) color:#ff0000; @endif" data-format="#,##0.00" >{!!@$claims_list->patient_due!!}</td>
                    <td style="text-align:right; @if(@$claims_list->insurance_due <0) color:#ff0000; @endif" data-format="#,##0.00" >{!!@$claims_list->insurance_due!!}</td>
                    <td style="text-align:right; @if(@$claims_list->balance_amt <0) color:#ff0000; @endif" data-format="#,##0.00" >{!!@$claims_list->balance_amt!!}</td>
                    <td>{{@$claims_list->status}}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <td colspan="13">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>