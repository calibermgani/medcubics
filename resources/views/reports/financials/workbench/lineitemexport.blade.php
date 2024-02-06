<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AR Workbench Report</title>
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
        @$workbench_list = $result['workbench_list'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="19" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="19" style="text-align:center;">AR Workbench Report</td>
            </tr>
            <tr>
                <td colspan="19" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="19" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!} :  </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($workbench_list) > 0)
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Responsibility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Category</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Paid($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat AR($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins AR($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">AR Due($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Age</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Status</th>
					<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Sub Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Workbench Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Followup Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Assigned To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workbench_list as  $result)
                <?php // from stored procedure
                    if(isset($result->claim_number) && $result->claim_number != ''){
                ?>
                <tr>
                    <td style="text-align: left;">{{ @$result->claim_number}}</td>
                    <td>{{ @$result->dos }}</td>
                    <td>{{ @$result->patient_name }}</td>
                    <td>{{ @$result->rendering_provider_short_name }} - {{ @$result->rendering_provider_name }}</td>
                    <td>{{ @$result->billing_provider_short_name }} - {{ @$result->billing_provider_name }}</td>
                    <td>{{ @$result->facility_short_name }} - {{ @$result->facility_name }}</td>
                    <td>{{ @$result->insurance_name }}</td>
                    <td>{{ @$result->insurance_category }}</td>
                    <td data-format="#,##0.00" style="@if(@$result->total_charge <0) color:#ff0000; @endif text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_charge) !!}</td>
                    <td data-format="#,##0.00" style="@if(@$result->tot_paid <0) color:#ff0000; @endif text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->tot_paid) !!}</td>
                    <td data-format="#,##0.00" style="@if(@$result->tot_adj <0) color:#ff0000; @endif text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->tot_adj) !!}</td>
                    <td data-format="#,##0.00" style="@if(@$result->pat_due <0) color:#ff0000; @endif text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->pat_due) !!}</td>
                    <td data-format="#,##0.00" style="@if(@$result->ins_due  <0) color:#ff0000; @endif text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->ins_due) !!}</td>
                    <td data-format="#,##0.00" style="@if(@$result->ar_due <0) color:#ff0000; @endif text-align:right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->ar_due) !!}</td>
                    <td  style="text-align:left;">{{ @$result->claim_age_days }}</td>
                    <td>{{ @$result->claim_status }}</td>
					<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                    <td>{{ @$result->workbench_status }}</td>
                    @if(date("m/d/y") == $result->fllowup_date)
                        <td style="color:#f07d08;">{{@$result->fllowup_date}}</td>
                    @elseif(date("m/d/y") >= $result->fllowup_date)
                        <td style="color:#ff0000;">{{@$result->fllowup_date}}</td>
                    @else
                        <td style="color:#bbc2d3;">{{@$result->fllowup_date}}</td>
                    @endif
                    <td>{{ App\Http\Helpers\Helpers::getUserFullName($result->assign_user_id) }}</td>
                </tr>
                <?php
                    } else {
                ?>
                <?php
                    $last_name = @$result->patient->last_name;
                    $first_name = @$result->patient->first_name;
                    $middle_name = @$result->patient->middle_name;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                    $fin_details = @$result->claim->pmt_claim_fin_data;
                    $pat_due = ($result->claim->insurance_id == 0)?@$fin_details->total_charge-($fin_details->patient_paid+$fin_details->patient_adj+$fin_details->insurance_paid+$fin_details->insurance_adj+$fin_details->withheld):0;
                    $ins_due = ($result->claim->insurance_id != 0) ?@$fin_details->total_charge-($fin_details->patient_paid+$fin_details->patient_adj+$fin_details->insurance_paid+$fin_details->insurance_adj+$fin_details->withheld):0;
                    $tot_adj = @$fin_details->patient_adj + @$fin_details->insurance_adj+ @$fin_details->withheld;
                    $tot_paid = @$fin_details->patient_paid + @$fin_details->insurance_paid;
                    $ar_due = @$fin_details->total_charge-($fin_details->patient_paid+$fin_details->patient_adj+$fin_details->insurance_paid+$fin_details->insurance_adj+$fin_details->withheld);
                    $fllowup_date = date("m/d/y", strtotime($result->fllowup_date));
                    $responsibility = 'Patient';
                    $ins_category = 'Patient';
                    if($result->claim->insurance_details){
                        $responsibility = App\Http\Helpers\Helpers::getInsuranceFullName(@$result->claim->insurance_details->id);
                        $ins_category= @$result->insurance_category;
                    }
                ?>
                <tr>
                    <td>{{@$result->claim->claim_number}}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$result->claim->date_of_service,'claimdate') }}</td>
                    <td>{{ $patient_name }}</td>
                    <td>
                        @if(@$result->claim->rendering_provider->provider_name != '')
                        {{ @$result->claim->rendering_provider->short_name }} - {{ str_limit(@$result->claim->rendering_provider->provider_name,25,'...') }}
                        @else
                        -Nil-
                        @endif
                    </td>
                    <td>
                        @if(@$result->claim->billing_provider->provider_name != '')
                        {{ @$result->claim->billing_provider->short_name }} - {{ str_limit(@$result->claim->billing_provider->provider_name,25,'...') }}
                        @else
                        -Nil-
                        @endif
                    </td>
                    <td>
                        @if(@$result->claim->facility_detail->facility_name != '')
                        {{ @$result->claim->facility_detail->short_name }} - {{ str_limit(@$result->claim->facility_detail->facility_name,25,'...') }} 
                        @else
                        -Nil-
                        @endif
                    </td>
                    <td>{{ $responsibility }}</td>
                    <td>{{ $ins_category }}</td>
                    <td data-format="#,##0.00" style="text-align:right;@if(@$result->claim->total_charge <0) color:#ff0000; @endif">{!! @$result->claim->total_charge !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;@if(@$tot_paid <0) color:#ff0000; @endif">{!! @$tot_paid !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;@if(@$tot_adj <0) color:#ff0000; @endif">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_adj) !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;@if(@$pat_due <0) color:#ff0000; @endif">{!! @$pat_due !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;@if(@$ins_due  <0) color:#ff0000; @endif">{!! @$ins_due !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;@if(@$ar_due <0) color:#ff0000; @endif">{!! @$ar_due !!}</td>
                    <td  class="text-left" style="text-align:left;">{{ @$result->claim->claim_age_days }}</td>
                    <td>{{ @$result->claim->status }}</td>
					<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                    <td>{{ @$result->status }}</td>
                        @if(date("m/d/y") == $fllowup_date)
                            <td style="color:#f07d08;">{{$fllowup_date}}</td>
                        @elseif(date("m/d/y") >= $fllowup_date)
                            <td style="color:#ff0000;">{{$fllowup_date}}</td>
                        @else
                            <td style="color:#bbc2d3;">{{$fllowup_date}}</td>
                        @endif
                    <td>{{ App\Http\Helpers\Helpers::getUserFullName($result->assign_user_id) }}</td>
                </tr>
                <?php } ?>
                @endforeach
            </tbody>
        </table>
        @endif
        <td colspan="20">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>
