<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Charges</title>
    </head>
    <body>
        <?php 
            @$charges = $result['charges'];
            @$facilities = $result['facilities'];
            @$rendering_providers = $result['rendering_providers'];
            @$billing_providers = $result['billing_providers'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="16" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="16" style="text-align:center;">Charges List</td>
            </tr>
            <tr>
                <td colspan="16" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Unbilled($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Paid($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Ins Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Bal($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Sub Status</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty((array)$charges))
                <?php
                    $count = 1;   
                    $insurances = App\Http\Helpers\Helpers::getInsuranceFullNameLists();
                    $patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();
                ?>
                @foreach($charges as $charge)
                <?php
                    $facility = @$charge;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$charge->last_name, @$charge->first_name, @$charge->middle_name);

                    $insurance_name = "";
                    if($charge->insurance_id==0) {
                        $insurance_name = "Self";
                    }else {
                        $insurance_name = !empty($insurances[$charge->insurance_id]) ? $insurances[$charge->insurance_id] : App\Http\Helpers\Helpers::getInsuranceFullName(@$charge->insurance_id);
                    }
                    $patient_ins_name = '';
                    if(isset($patient_insurances['all'][@$charge->patient_id])) {
                        $patient_ins_name = $patient_insurances['all'][@$charge->patient_id];
                    }                    
                    // When billed amount comes unbilled amount should not come
                    $charge_amt = App\Http\Helpers\Helpers::BilledUnbilled($charge);
                    $billed = isset($charge_amt['billed'])?$charge_amt['billed']:0.00;
                    $unbilled = isset($charge_amt['unbilled'])?$charge_amt['unbilled']:0.00;
                ?>
                @if(isset($charge) &&!empty($charge))
                <?php $dos = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$charge->date_of_service, '','-')  ?>
                <tr> 
                    <td style="text-align:left;">{{@$charge->claim_number}}</td>
                    <td style="text-align:left;">{{@$charge->account_no}}</td>
                    <td>{{@$patient_name}}</td>
                    <td>{{ ($dos!='')?@$dos:'-Nil-' }}</td>
                    <td>@if(isset($charge->facility_name) && $charge->facility_name !=''){!! @$charge->facility_short_name !!} - {!! @$charge->facility_name !!} @else -Nil- @endif</td>
                    <td>@if(isset($charge->rendering_full_name) && $charge->rendering_full_name!=''){!! @$charge->rendering_short_name !!} - {!! $charge->rendering_full_name !!} @else -Nil- @endif</td>
                    <td>@if(isset($charge->billing_full_name) && $charge->billing_full_name!=''){!! @$charge->billing_short_name !!} - {!! $charge->billing_full_name !!} @else -Nil- @endif</td>
                    <td>{{isset($insurance_name)?@$insurance_name:'-Nil-' }}</td>
                    <td style="text-align: right; @if(@$unbilled < 0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$unbilled!!}</td>
                    <td style="text-align: right; @if(@$billed < 0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$billed!!}</td>
                    <td style="text-align: right; @if(@$charge->total_paid < 0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$charge->total_paid!!}</td>
                    <td style="text-align: right; @if(@$charge->patient_due < 0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$charge->patient_due!!}</td>
                    <td style="text-align: right; @if(@$charge->insurance_due < 0) color:#ff0000; @endif" data-format="#,##0.00">{!!@$charge->insurance_due!!}</td>
                    <td style="text-align: right; @if( (@$charge->balance_amt) < 0 ) color:#ff0000; @endif" data-format="#,##0.00">{!! @$charge->balance_amt !!}</td>
                    <td class="color-status">{{ ($charge->status!='') ? $charge->status : '-Nil-'}}</td>
                    <td class="color-status"> 
                        @if(isset($charge->sub_status_desc))
                            {{ $charge->sub_status_desc }}
                        @else 
                            -Nil-
                        @endif
                    </td>
                </tr>
                @endif
                <?php $count++;   ?> 
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="16">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>