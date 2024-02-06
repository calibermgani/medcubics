<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;
            }
            th {
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-top: 1px solid #ccc;
                border-bottom: 1px solid #ccc;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;} 
            .table-summary tbody tr td{font-size:11px !important;}
            .table-row-border tbody tr td{border-bottom: 1px solid #ccc;}
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right !important;padding-right:5px;}
            .text-left{text-align: left !important;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px}
            .padding-0-4{padding: 0px 4px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>	
    <body>
        <?php 
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>{{$header}}</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="6" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="6" style="border:none;text-align: right !important"><span>User :</span> <span class="">{{ Auth::user()->short_name }}</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">            
            <div>	
                <table class="@if($type == 'rejected') table-row-border @endif" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        @if($type == 'rejected')
                        <tr>
                            <th>Claim No</th>
                            <th>DOS</th>
                            <th>ACC No</th>
                            <th>Patient Name</th>
                            <th class="text-center">Charge Amt($)</th>
                            <th>Payer</th>
                            <th>Payer ID</th>
                            <th>Submitted Date</th>
                            <th>Rejected Date</th>
                            <th>Rejection reason</th>
                        </tr>
                        @else
                        <tr>
                            <th>Claim No</th>
                            <th>DOS</th>
                            <th>Patient Name</th>
                            <th>Billed To</th>
                            <th>Category</th>
                            <th>Payer ID</th>
                            <th>Rendering</th>
                            <th>Billing</th>
                            <th>Facility</th>                               
                            <th class="text-center">Charge Amt($)</th>                                
                            <th class="text-center">AR Bal($)</th>                                
                            <th>Created Date</th>                                
                            <th>Filed Date</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        <?php 
							$count = 1;    
                            $insurances = App\Http\Helpers\Helpers::getInsuranceNameLists(); 
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
                            $billed_amount = App\Http\Helpers\Helpers::priceFormat($billed_amount);
                            $insurance_payment_count = (!empty($payment_claimed_det[$claim->id])) ? $payment_claimed_det[$claim->id] : 0;

                            $insurance_name = "";
                            if ($claim->self_pay == 'Yes')
                                $insurance_name = "Self";
                            else
                                $insurance_name = !empty($insurances[@$claim->insurance_details->id]) ? $insurances[@$claim->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
                            // Edit link new function call included
                            $detArr = ['patient_id' => @$claim->patient->id, 'status' => @$claim->status, 'charge_add_type' => @$claim->charge_add_type, 'claim_submit_count' => @$claim->claim_submit_count];
                            $edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$claim->id, @$insurance_payment_count, "Charge", $detArr);

                            $patient_ins_name = '';
                            if (isset($patient_insurances['all'][@$claim->patient->id])) {
                                $patient_ins_name = $patient_insurances['all'][@$claim->patient->id];
                            }
                        ?>
                        @if($type == 'rejected')
                        <tr>
                            <td class="text-left">{{$claim->claim_number}}</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate')}}</td>
                            <td>{!! $claim->patient->account_no !!}</td>
                            <td>{{$patient_name}}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($billed_amount) !!}</td>
                            <td>{{ $insurance_name }}</td>
                            <td class="text-left">@if($claim->self_pay == 'No'){{@$claim->insurance_details->payerid}}@endif</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($claim->submited_date)}}</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($claim->claimediinfo->rejected_date)}}</td>
                            <?php
                                $fileType = explode('.',$claim->claimediinfo->response_file_path);
                                //$rejection_reason = (isset($claim->claimediinfo->denial_codes)) ? $claim->claimediinfo->denial_codes : '';
                                //echo $claim->claim_number.'<pre>'.$claim->claimediinfo->denial_codes;
                            ?>
                            @if($fileType[1] != '277')
                            <td style='width:30%;text-align: justify;'>
                                {!! str_replace("</li>", "<br><br>", str_replace("<li>", "",$claim->claimediinfo->denial_codes)) !!}
                            </td>
                            @else
                                <td></td>
                            @endif
                        </tr>
                        @else
                        <tr>
                            <td class="text-left">{{$claim->claim_number}}</td>
                            <td>{{App\Http\Helpers\Helpers::dateFormat($claim->date_of_service,'claimdate')}}</td>
                            <td>{{$patient_name}}</td>
                            <td>{{ $insurance_name }}</td>
                            <td>{{ $claim->insurance_category }}</td>
                            <td class="text-left">@if($claim->self_pay == 'No'){{@$claim->insurance_details->payerid}}@endif</td>
                            <td>{{@$claim->rendering_provider->short_name}}</td>
                            <td>{{ @$claim->billing_provider->short_name }}</td>
                            <td>{{ @$claim->facility_detail->short_name}}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($billed_amount) !!}</td>
							<td style="text-align: right;" data-format="#,##0.00">{{$claim->arbal}}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$claim->created_at, 'date') }}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$claim->filed_date,'date')}}</td>
                        </tr>
                        @endif
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>