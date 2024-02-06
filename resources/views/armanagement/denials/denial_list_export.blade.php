<?php 
    $practice_details = App\Models\Practice::getPracticeDetails();
    $heading_name = $practice_details['practice_name'];
?>
<table>
    <tbody>
        <tr>                   
            <td colspan="15" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
        </tr>
        <tr>
            <td colspan="15" style="text-align:center;">AR Denial List</td>
        </tr>
        <tr>
            <td colspan="15" style="text-align:center;">User: {{ Auth::user()->short_name }} | Created: {{ date("m/d/y") }}</td>
        </tr>
        <?php 
        /*<tr>
            <td colspan="15" style="text-align:center;">
                @if(isset($result['header']) && $result['header'] !='')
                    <?php $i = 0; ?>
                    @foreach($result['header'] as $header_name => $header_val)
                        {{ @$header_name }}: {{implode(',', @$header_val)}}
                        @if($i < count((array)$result['header'])) | @endif 
                            <?php $i++; ?>
                    @endforeach
                @endif
            </td>
        </tr>
        */
        ?>
    </tbody>
</table>
@if(count((array)$result['denial_cpt_list']) > 0)
<table>
    <thead>
        <tr>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Category</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Provider</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>								
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Denied CPT</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Denied Date</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Sub Status</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim Age</th>
            @if(isset($result['workbench_status']) && $result['workbench_status'] == 'Include')
                <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Workbench Status</th>
            @endif
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Charge Amt($)</th>
            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Outstanding AR($)</th>
        </tr>            
    </thead>   
    <tbody>              
        @foreach($result['denial_cpt_list'] as  $res)
            <?php
                $last_name = @$res->claim->patient->last_name;
                $first_name = @$res->claim->patient->first_name;
                $middle_name = @$res->claim->patient->middle_name;
                $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                $ar_due = @$res->total_ar_due;
                $tot_charge = isset($res->total_charge) ? $res->total_charge : @$res->claimcpt->charge;
                if(isset($res->lastcptdenialdesc->pmtinfo)) {
                    if($res->lastcptdenialdesc->pmtinfo->pmt_mode == 'EFT')
                        $denial_date = @$res->lastcptdenialdesc->pmtinfo->eft_details->eft_date;
                    elseif($res->lastcptdenialdesc->pmtinfo->pmt_mode == 'Credit')
                        $denial_date = @$res->lastcptdenialdesc->pmtinfo->credit_card_details->expiry_date ;
                    else 
                        $denial_date = @$res->lastcptdenialdesc->pmtinfo->check_details->check_date ;
                }else{
                    $denial_date = @$res->denied_date;
                }
                $denial_date = App\Http\Helpers\Helpers::dateFormat(@$denial_date);
                $responsibility = 'Patient';
                $ins_category = 'Patient';
                if(isset($res->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id)) {
                    $responsibility = App\Http\Helpers\Helpers::getInsuranceName($res->lastcptdenialdesc->claimcpt_txn->claimtxdetails->payer_insurance_id);
                    $ins_category = @$res->lastcptdenialdesc->claimcpt_txn->claimtxdetails->ins_category;
                }else{
                    $responsibility = App\Http\Helpers\Helpers::getInsuranceName($res->rec_claim_txn->payer_insurance_id);
                    $ins_category = @$res->rec_claim_txn->ins_category;
                }
                $cpt_info_id = $res->claim_cpt_info_id;
                if(isset($res->cpt_codes)){		
                    $cpt_arr = array_unique(explode(",", $res->cpt_codes));
                    $cpt_codes = implode(",", $cpt_arr);
                } else {
                    $cpt_codes = @$res->claimcpt->cpt_code;
                }
            ?>
            <tr>
                <td style="text-align: left;">{{ @$res->claim->claim_number }}</td>
                <td style="text-align: left;">{{ App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$res->claimcpt->dos_from,'','Nil') }}</td>
                <td style="text-align: left;">{{ @$res->claim->patient->account_no }}</td>
                <td style="text-align: left;">{{ $patient_name }}</td>
                <td style="text-align: left;">{{ $responsibility }}</td>
                <td style="text-align: left;">{{ $ins_category }}</td>
                <td style="text-align: left;">@if($res->claim->rend_providers->provider_name !=''){{ @$res->claim->rend_providers->provider_short }} - {{ @$res->claim->rend_providers->provider_name }} @else -Nil- @endif</td>
                <td style="text-align: left;">@if($res->claim->facility->facility_name !=''){{ @$res->claim->facility->facility_short }} - {{ @$res->claim->facility->facility_name }} @else -Nil- @endif</td>
                <td style="text-align: left;">{{ @$cpt_codes }}</td>
                <td style="text-align: left;">{{ $denial_date }}</td>
                <td style="text-align: left;">
                    @if(isset($res->claim->claim_sub_status->sub_status_desc) && $res->claim->claim_sub_status->sub_status_desc != '')
                        {{ @$res->claim->claim_sub_status->sub_status_desc }}
                    @else
                        -Nil-
                    @endif	
                </td>
                <td style="text-align: left;">{{ @$res->claim->claim_age_days }}</td>
                @if(isset($result['workbench_status']) && $result['workbench_status'] == 'Include')
                <td style="text-align: left;">
                    @if(isset($result->last_workbench))
                        {{ $res->last_workbench->status }}
                    @else
                        N/A
                    @endif
                </td>
                @endif
                <td style="text-align: right; @if((@$tot_charge)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$tot_charge !!}</td>
                <td style="text-align: right; @if((@$ar_due)<0) color:#ff0000; @endif" data-format='0.00'>{!! @$ar_due !!}</td>
            </tr>
        @endforeach                   
    </tbody>   
</table>
@endif
<table> <tr><td colspan="15">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td></tr></table>
