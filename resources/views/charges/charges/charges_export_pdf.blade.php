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
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right !important;padding-right:5px;}
            .text-left{text-align: left !important;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .c-paid, .Paid{color:#02b424;}
            .c-denied, .Denied{color:#d93800;}
            .m-ppaid, .c-ppaid{color:#2698F8}
            .ready-to-submit, .Ready{color:#5d87ff;}
            .Rejection{color: #f07d08;}
            .Hold{color:#110010;}
            .claim-paid{background: #defcda; color:#2baa1d !important;}
            .claim-denied{ color:#d93800 !important;}
            .claim-submitted{background: #caf4f3; color:#41a7a5 !important}
            .claim-ppaid{background: #dbe7fe; color:#2f5dba !important;}
            .Patient{color:#e626d6;}
            .Submitted{color:#009ec6;}
            .Pending{color:#313e50;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
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
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Charges List</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="7" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="8" style="border:none;text-align: right !important"><span>User :</span> <span class="">{{ Auth::user()->short_name }}</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">            
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th>Claim No</th>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>DOS</th>
                            <th>Facility</th>
                            <th>Rendering</th>
                            <th>Billing</th>            
                            <th>Payer</th>                      
                            <th style="text-align:center !important;">Unbilled($)</th>
                            <th style="text-align:center !important;">Billed($)</th>
                            <th style="text-align:center !important;">Paid($)</th>
                            <th style="text-align:center !important;">Pat Bal($)</th>
                            <th style="text-align:center !important;">Ins Bal($)</th>
                            <th style="text-align:center !important;">AR Bal($)</th>
                            <th>Status</th>
                        </tr>
                    </thead>    
                    <tbody>
                        @if(!empty($charges))
                        <?php 
							$count = 1;   
                            $insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();
                            $patient_insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();
                            // Patient copay payment included
                            $payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetails('patient');
                        ?>
                        @foreach($charges as $charge)                    
                        <?php
                            $facility = @$charge->facility_detail;
                            $provider = @$charge->rendering_provider;
                            $patient = @$charge->patient;
                            $insurance_payment_count = (!empty($payment_claimed_det[$charge->id])) ? $payment_claimed_det[$charge->id] : 0;
                            $patient_name = App\Http\Helpers\Helpers::getNameformat(@$charge->patient->last_name, @$charge->patient->first_name, @$charge->patient->middle_name);
                            $detArr = ['patient_id' => @$charge->patient->id, 'status' => @$charge->status, 'charge_add_type' => @$charge->charge_add_type, 'claim_submit_count' => @$charge->claim_submit_count];
                            $edit_link = App\Http\Helpers\Helpers::getChargeEditLinkByDetails(@$charge->id, @$insurance_payment_count, "Charge", $detArr);

                            $insurance_name = "";
                            if(empty($charge->insurance_details)) {
                                $insurance_name = "Self";
                            }else {
                                $insurance_name = !empty($insurances[$charge->insurance_details->id]) ? $insurances[$charge->insurance_details->id] : App\Http\Helpers\Helpers::getInsuranceName(@$charge->insurance_details->id);
                            }
                            $patient_ins_name = '';
                            if(isset($patient_insurances['all'][@$patient->id])) {
                                $patient_ins_name = $patient_insurances['all'][@$patient->id];
                            }
                            $provider = $charge->billing_provider;
                            $charge_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($charge->id, 'encode');
                            $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id, 'encode');
                            // When billed amount comes unbilled amount should not come
                            if((!empty($charge->insurance_details) && $charge->claim_submit_count == 0)  || ($charge->status=='Hold' && !empty($charge->insurance_details))) {
                                $unbilled = $charge->total_charge;
                                $billed = 0;
                            }else {
                                $unbilled = 0;
                                $billed = $charge->total_charge;
                            }
                        ?>
                        @if(isset($charge->patient) &&!empty($charge->patient))
                        <?php $dos = App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$charge->date_of_service, '','-')  ?>
                        <tr>
                            <td class="text-left">{{@$charge->claim_number}}</td>
                            <td>{{@$charge->patient->account_no}}</td>
                            <td>{{@$patient_name}}</td>
                            <td>{{ ($dos!='')?@$dos:'Nil' }}</td>
                            <td>{{($charge->facility_detail->short_name!='')?@$charge->facility_detail->short_name:'Nil'}}</td>
                            <td>{{($charge->rendering_provider->short_name!='')?@$charge->rendering_provider->short_name:'Nil'}}</td>
                            <td>{{($charge->billing_provider->short_name!='')?@$charge->billing_provider->short_name:'Nil'}}</td>
                            <td>{{ ($insurance_name!='')?@$insurance_name:'Nil' }}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$unbilled) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$billed) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$charge->total_paid) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$charge->patient_due) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$charge->insurance_due) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($charge->balance_amt) !!}</td>
                            <td class="color-status">{{ ($charge->status!='') ? $charge->status : 'Nil'}}</td>
                        </tr>
                        @endif
                        <?php $count++;   ?> 
                        @endforeach
                        @endif
                    </tbody>  
                </table>
            </div>
        </div>
    </body>
</html>