<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - CPT-HCPCS Summary</title><?php // CPT/HCPCS here / slash not working ?>
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
        @$cpts_count_wallet = $result['cpts_count_wallet'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$cpts_count_summary = $result['cpts_count_summary'];
        @$cpts = $result['cpts'];
        @$patient = $result['patient'];
        @$summary_det = $result['summary_det'];
        @$cptDesc = $result['cptDesc'];
        @$search_by = $result['search_by'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="9" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;">CPT/HCPCS Summary</td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="9" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key => $val)
                    @if($i > 0){{' | '}}@endif
                    <span>{{ $key }} : </span>{{ $val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count((array)$cpts) > 0)
        <table>
            <thead >
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT Code</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Description</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Paid($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Paid($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">AR Due($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cpts as $list)
                <?php
                    $cpt_code = @$list->cpt_code;
                    $total_charge = isset($list->total_charge) ? $list->total_charge : 0;
                    $desc = isset($cptDesc->$cpt_code) ? $cptDesc->$cpt_code : '';
                    $patient_adj = isset($list->pat_adj)?$list->pat_adj:0;
                    $insurance_adj = isset($list->ins_adj)?$list->ins_adj:0;
                    $adjustment = isset($list->tot_adj)?$list->tot_adj:0;
                    $pat_pmt = isset($patient->$cpt_code)?$patient->$cpt_code:0;
                    $ins_pmt = isset($insurance->$cpt_code)?$insurance->$cpt_code:0;
                    $pat_bal = isset($list->patient_bal)?$list->patient_bal:0;
                    $ins_bal = isset($list->insurance_bal)?$list->insurance_bal:0;
                ?>
                <tr>
                    <td style="text-align: left;">{{ @$cpt_code }}</td>
                    <td style="text-align: left;">{{ @$list->description }}</td>
                    <td style="text-align: left;">{{ $list->unit }}</td>
                    <td style=" @if($total_charge <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $total_charge !!}</td>
                    <td style=" @if(@$list->patient_paid <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! @$list->patient_paid !!}</td>
                    <td style=" @if(@$list->insurance_paid <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! @$list->insurance_paid !!}</td>
                    <td style=" @if($patient_adj <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $patient_adj !!} </td>
                    <td style=" @if($insurance_adj <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $insurance_adj !!} </td>
                    <td style=" @if($list->total_ar_due <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $list->total_ar_due !!}</td>
                </tr>
                @endforeach 
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="3" style="color: #00877f;font-weight: bold;font-size:13.5px;"><h3>Summary</h3></td>
            </tr>
            <thead>
                <tr>
                    <th colspan="2" style="font-weight:600;">Title</th>
                    <th style="font-weight:600;text-align:right;">Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $wallet = isset($patient->wallet)?$patient->wallet:0;
                    if($wallet<0)
                        $wallet = 0;
                ?>
                <tr>
                    <td colspan="2">Wallet Balance</td>
                    <td style=" @if($wallet <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{{ $wallet }}</td>
                </tr>
                <tr>
                    <td colspan="2">Total Units</td>
                    <td style="font-weight:600;text-align:right;">{{ $summary_det['units'] }}</td>
                </tr>
                <tr>
                    <td colspan="2">Total Charges</td>
                    <td style=" @if($summary_det['charges'] <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! $summary_det['charges'] !!}</td>
                </tr>
                <tr>
                    <td colspan="2">Total Adjustments</td>
                    <td style=" @if($summary_det['adj'] <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! $summary_det['adj'] !!}</td>
                </tr>                                       
                <tr>
                    <td colspan="2">Total Payments</td>
                    <td style=" @if($summary_det['pmt'] <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! $summary_det['pmt'] !!}</td>
                </tr>
                <tr>
                    <td colspan="2">Total Balance</td>
                    <td style=" @if($summary_det['bal'] <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! $summary_det['bal'] !!}</td>
                </tr>
            </tbody>
        </table>
        @endif
        <td colspan='9'>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>