<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patients Bulk Statement</title>
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
            $patients_arr = $result['patients_arr'];
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="6" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">Patients Bulk Statement List</td>
            </tr>
            <tr>
                <td valign="center" colspan="6" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Statements</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Last Payment Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Last Payment Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat Balance($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients_arr as $keys=>$patient_value)
                <?php
                    $patPmtDate = isset($patient_value->patient_last_pmt->created_at) ? $patient_value->patient_last_pmt->created_at : '';
                    $patPmtAmt = isset($patient_value->patient_last_pmt->total_paid) ? $patient_value->patient_last_pmt->total_paid : 0;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat("$patient_value->last_name","$patient_value->first_name","$patient_value->middle_name");
                ?>
                <tr>
                    <td>@if(@$patient_value->title){{ @$patient_value->title }}. @endif{{ str_limit($patient_name,25,'...') }}</td>
                    <td style="text-align:left;">{{ @$patient_value->account_no }}</td>
                    <td style="text-align:left;">{{ @$patient_value->statements_sent }}</td>
                    <td style="text-align:left;">{{ App\Http\Helpers\Helpers::timezone(@$patPmtDate, 'm/d/y') }}</td>
                    <td style="text-align:right; @if((@$patPmtAmt) < 0 ) color:#ff0000; @endif" data-format="#,##0.00">{!! @$patPmtAmt !!}</td>
                    <td style="text-align:right; @if((@$patient_value->patient_due) < 0 ) color:#ff0000; @endif" data-format="#,##0.00">{!! @$patient_value->patient_due !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <td colspan="6">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>