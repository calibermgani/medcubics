<!DOCTYPE html>
<html lang="en">
    <head>
        <title>AR Management List</title>
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
                <td colspan="13" style="text-align:center;">AR Management List</td>
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
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed To</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Charges($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>								
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat AR($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Ins AR($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Due($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Sub Status</th>
                </tr>
            </thead>   
            <tbody>
                <?php             
                    $insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();
                ?>
                @foreach($claims_lists as $key => $claim)
                <?php
                    //$facility = $claim->facility_detail;
                    //$provider = $claim->rendering_provider;

                    $insurance_name = "";
                    if (empty($claim->insurance_details) || $claim->insurance_details->id == '' || $claim->insurance_details->id == '0') {
                            $insurance_name = "Self";
                    } else {
                            $insurance_name = !empty($insurances[$claim->insurance_details->id]) ? $insurances[$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
                    }
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name, @$claim->patient->first_name, @$claim->patient->middle_name);                   
                ?>
                <tr>
                    <td style="text-align: left;">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$claim->date_of_service)}}</td> 
                    <td style="text-align: left;">{{@$claim->claim_number}}</td>  
                    <td style="text-align: left;">{{ @$claim->patient->account_no }}</td>                      
                    <td style="text-align: left;">{{ $patient_name }}</td>
                    <td style="text-align: left;">@if(isset($claim->rendering_provider->provider_name) && @$claim->rendering_provider->provider_name !=''){{ @$claim->rendering_provider->short_name }} - {{ @$claim->rendering_provider->provider_name }} @else -Nil- @endif</td>
                    <td style="text-align: left;">@if(isset($claim->facility_detail->facility_name) && @$claim->facility_detail->facility_name !=''){{ @$claim->facility_detail->short_name }} - {{ @$claim->facility_detail->facility_name }} @else -Nil- @endif</td>
                    <td style="text-align: left;">{{ $insurance_name }}</td>
                    <td style="text-align: right; @if((@$claim->total_charge)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$claim->total_charge !!}</td>
                    <td style="text-align: right; @if((@$claim->total_paid)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$claim->total_paid !!}</td>
                    <td style="text-align: right; @if((@$claim->pmt_claim_fin_data->patient_due)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$claim->pmt_claim_fin_data->patient_due !!}</td>
                    <td style="text-align: right; @if((@$claim->pmt_claim_fin_data->insurance_due)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$claim->pmt_claim_fin_data->insurance_due !!}</td>
                    <td style="text-align: right; @if((@$claim->balance_amt)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$claim->balance_amt !!}</td>
                    <td style="text-align: left;">{{ @$claim->status}}</td>
                    <td style="text-align: left;">
                        @if(isset($claim->claim_sub_status->sub_status_desc))
                            {{ $claim->claim_sub_status->sub_status_desc }}
                        @else
                            -Nil-
                        @endif		
                    </td>
                </tr>
                @endforeach
            </tbody>   
        </table>

        <table> <tr><td colspan="13">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td></tr></table>
    </body>
</html>