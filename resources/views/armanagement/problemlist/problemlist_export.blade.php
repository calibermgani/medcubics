<?php 
	@$last_addin_problemlist = $result['last_addin_problemlist'];
	@$heading = $result['heading'];
	$heading_name = App\Models\Practice::getPracticeDetails();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{$heading}} Workbench</title>
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
        <table>
            <tr>
                <td colspan="14" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="14" style="text-align:center;">{{$heading}} Workbench</td>
            </tr>
            <tr>
                <td colspan="14" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Provider</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed To</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>                        
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Due($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Sub Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Followup Dt</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Assigned To</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Priority</th>                   
                </tr>
            </thead>    
            <tbody>
                <?php 
                    $last_addin_problemlist = isset($last_addin_problemlist->problem_list_data)?$last_addin_problemlist->problem_list_data:$last_addin_problemlist;
                ?>
                @if(count((array)$last_addin_problemlist)>0 )
                @foreach(@$last_addin_problemlist as $keys=>$last_addin_problemlist)
                <?php
                    $patient = $last_addin_problemlist->patient;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
                ?>
                <tr>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$last_addin_problemlist->claim->date_of_service,'claimdate') }}</td>
                    <td style="text-align: left;">{{@$last_addin_problemlist->claim->claim_number}}</td>
                    <td>{{ @$patient_name }}</td>
                    <td>@if(isset($last_addin_problemlist->claim->rendering_provider->id) && $last_addin_problemlist->claim->rendering_provider->id !=''){{ @$last_addin_problemlist->claim->rendering_provider->short_name }} - {{ @$last_addin_problemlist->claim->rendering_provider->provider_name }} @else -Nil- @endif</td>
                    <td>@if(isset($last_addin_problemlist->claim->facility_detail->id) && $last_addin_problemlist->claim->facility_detail->id !=''){{ @$last_addin_problemlist->claim->facility_detail->short_name }} - {{ @$last_addin_problemlist->claim->facility_detail->facility_name }} @else -Nil- @endif</td>
                    <td>
                        @if(@$last_addin_problemlist->claim->insurance_details)
                            {!! App\Http\Helpers\Helpers::getInsuranceFullName(@$last_addin_problemlist->claim->insurance_details->id) !!}
                        @else
                            Self
                        @endif
                    </td>
                    <td style="text-align: right; @if((@$last_addin_problemlist->claim->total_charge)<0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$last_addin_problemlist->claim->total_charge!!}</td>
                    <td style="text-align: right; @if((@$last_addin_problemlist->claim->total_paid)<0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$last_addin_problemlist->claim->total_paid!!}</td>
                    <td style="text-align: right; @if((@$last_addin_problemlist->claim->balance_amt)<0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$last_addin_problemlist->claim->balance_amt!!}</td>
                    <td>{{ @$last_addin_problemlist->status }}</td>
                    <td>
                        @if(isset($last_addin_problemlist->claim->claim_sub_status->sub_status_desc))
                            {{ @$last_addin_problemlist->claim->claim_sub_status->sub_status_desc }}
                        @else 
                            -Nil-
                        @endif
                    </td>
                    <td>
                        <?php $fllowup_date = date("m/d/y", strtotime($last_addin_problemlist->fllowup_date)); ?>
                        @if(date("m/d/y") == $fllowup_date)
                            <span class="">{{$fllowup_date}}</span>
                        @elseif(date("m/d/y") >= $fllowup_date)
                            <span class="">{{$fllowup_date}}</span>
                        @else
                            <span class="">{{$fllowup_date}}</span>
                        @endif
                    </td>
                    <td>{{ App\Http\Helpers\Helpers::getUserFullName($last_addin_problemlist->assign_user_id) }}</td>
                    <td>{{@$last_addin_problemlist->priority}}</td>                    
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="14">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>