<?php
    @$claims = $result['claims'];
    @$type = $result['type'];
    @$header = $result['header'];
    @$practice_details = App\Models\Practice::getPracticeDetails();
    @$heading_name = $practice_details['practice_name'];
    if ($type == 'rejected'){
        $colspan = 10;
    }elseif($type == 'error') {
        $colspan = 13;
    }else{
        $colspan = 12;
    }
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{$header}}</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr  td span ul{
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
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>	
    <body>
        <table>
            <tr>                   
                <td colspan="<?php echo $colspan; ?>" style="text-align:center;color: #00877f;font-weight:600;">@if(isset($heading_name) && !empty($heading_name)){{$heading_name}} @else <span style='color:#fff;'> - </span> @endif</td>
            </tr>
            <tr>
                <td colspan="<?php echo $colspan; ?>" style="text-align:center;">{{$header}}</td>
            </tr>
            <tr>
                <td valign="center" colspan="<?php echo $colspan; ?>" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        
        <table>
            <thead>
                @if($type == 'rejected')
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">ACC No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Charge Amt($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Submitted Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rejected Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rejection reason</th>
                </tr>
                @else
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billed To</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Category</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Billing</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>                               
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Charge Amt($)</th>                                
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Bal($)</th>                                
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created Date</th>                                
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">@if($type == 'submitted') Submitted @else Filed @endif Date</th>
                    @if($type == 'error') <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th> @endif
                </tr>
                @endif
            </thead>    
            <tbody>
                <?php
                    $count = 1;   
                    $insurances = App\Http\Helpers\Helpers::getInsuranceFullNameLists(); 
                    $patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();  
                    $payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('payment');  
                    $billed_amounts_list = App\Models\Payments\ClaimCPTInfoV1::getAllBilledAmountByActiveLineItem();
                ?>
                @if(isset($claims))    
                @foreach($claims as $key => $claim)
                <?php 
                    $facility = @$claim->facility_detail;
                    $provider = @$claim->rendering_provider;
                    $patient = @$claim->patient;
                    $patient_name = App\Http\Helpers\Helpers::getNameformat(@$claim->patient->last_name,@$claim->patient->first_name,@$claim->patient->middle_name);
                    if (@$claim->insurance_details->payerid != '' && @$claim->self_pay == 'No')
                    $class_name = 'cls-electronic';
                    else if (@$claim->status == 'Patient' || $claim->self_pay == 'Yes')
                    $class_name = 'cls-patient';
                    else
                    $class_name = 'cls-paper';
                    $claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id);
                    $billed_amount = (!empty($billed_amounts_list[$claim->id])) ? $billed_amounts_list[$claim->id] : 0;
                    $insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;
                    
                    $insurance_name = "";
                    if ($claim->self_pay == 'Yes')
                    $insurance_name = "Self";
                    else
                    $insurance_name = !empty($insurances[@$claim->insurance_details->id]) ? $insurances[@$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceFullName(@$claim->insurance_details->id);
                    $detArr = ['patient_id' => @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
                    $edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);
                    
                    $patient_ins_name = '';
                    if (isset($patient_insurances['all'][@$claim->patient->id])) {
                        $patient_ins_name = $patient_insurances['all'][@$claim->patient->id];
                    }
                    if($type == 'submitted' || $type == 'rejected'){
                        $filled_date = $claim->submited_date;
                    }else{
                        $filled_date = $claim->filed_date;
                    }
                ?>
                @if($type == 'rejected')
                <tr>
                    <td style="text-align: left;">{{$claim->claim_number}}</td>
                    <td>{{App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate')}}</td>
                    <td style="text-align: left;">{!! $claim->patient->account_no !!}</td>
                    <td>{{$patient_name}}</td>
                    <td style="text-align: right; @if($billed_amount < 0) color:#ff000; @endif" data-format="#,##0.00">{!! $billed_amount !!}</td>
                    <td>{{ $insurance_name }}</td>
                    <td style="text-align: left;">@if($claim->self_pay == 'No'){{@$claim->insurance_details->payerid}}@endif</td>                            
                    <td>{{App\Http\Helpers\Helpers::dateFormat($claim->submited_date)}}</td>                            
                    <td>{{App\Http\Helpers\Helpers::dateFormat($claim->claimediinfo->rejected_date)}}</td>                            
                    <?php 
                        $fileType = explode('.',$claim->claimediinfo->response_file_path); 
                        ?>
                    @if($fileType[1] != '277')
                    <td style='text-align: justify;'>
                        {!! preg_replace("/<br\\s*?\\/?>\\s*$/", '', str_replace("</li>", "<br> <br>", str_replace("<li>", "",$claim->claimediinfo->denial_codes))) !!}
                    </td>
                    @else
                        <td></td>
                    @endif
                </tr>
                @else
                <tr>
                    <td style="text-align: left;">{{$claim->claim_number}}</td>
                    <td>{{App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate')}}</td>
                    <td>{{$patient_name}}</td>
                    <td>{{ $insurance_name }}</td>
                    <td>{{ $claim->insurance_category }}</td>
                    <td style="text-align: left;">@if($claim->self_pay == 'No'){{@$claim->insurance_details->payerid}}@endif</td>
                    <td>@if(isset($claim->rendering_provider) && $claim->rendering_provider->provider_name !=''){{@$claim->rendering_provider->short_name}} - {{@$claim->rendering_provider->provider_name}} @else -Nil- @endif</td>
                    <td>@if(isset($claim->billing_provider) && $claim->billing_provider->provider_name !=''){{ @$claim->billing_provider->short_name }} - {{ @$claim->billing_provider->provider_name }} @else -Nil- @endif</td>
                    <td>@if($claim->facility_detail->facility_name !=''){{ @$claim->facility_detail->short_name}} - {{ @$claim->facility_detail->facility_name}} @else -Nil- @endif</td>
                    <td style="text-align: right;@if($billed_amount < 0) color:#ff000; @endif" data-format="#,##0.00">{!! $billed_amount !!}</td>
                    <td style="text-align: right;@if($billed_amount < 0) color:#ff000; @endif" data-format="#,##0.00">{{$claim->arbal}}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$claim->created_at, 'date') }}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$filled_date,'date')}}</td>
                    @if($type == 'error')
                    <td>
                        @if($claim->no_of_issues > 0 && $claim->status == 'Ready') Error @else {{ $claim->status }} @endif
                    </td>
                    @endif
                </tr>
                @endif
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="<?php echo $colspan; ?>">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>