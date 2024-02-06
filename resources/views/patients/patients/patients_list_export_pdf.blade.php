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
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
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
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Patients List</i></p></td>
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
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Cell Phone</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>SSN</th>
                            <th>Payer</th>
                            <th class="text-center">Pat Due($)</th>
                            <th class="text-center">Ins Due($)</th>
                            <th class="text-center">AR Due($)</th>
                            <th>Created On</th>       
                            <th>%</th>
                        </tr>
                    </thead>    
                    <tbody>
                        @if(!empty($patients))
                        <?php $insurances = json_decode(json_encode($insurance_list), TRUE); ?>
                        @foreach($patients as $patient)
                        @if($patient !='')
                        <?php
                            $getReachEndday = '';
                            $patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id);
                            $plan_end_date = '';
                            if ($plan_end_date == '0000-00-00' || $plan_end_date == '') {
                                $getReachEndday = 0;
                            } else {
                                $now = strtotime(date('Y-m-d')); // or your date as well
                                $your_date = strtotime($plan_end_date);
                                $datediff = $now - $your_date;
                                $getReachEndday = floor($datediff / (60 * 60 * 24));
                            }
                            $insurance_name = "";
                            if ($patient->is_self_pay == 'Yes') {
                                $insurance_name = "Self Pay";
                            } else {
                                $insurance_name = '';
                                if (isset($insurances['primary'][@$patient->id]))
                                    $insurance_name = $insurances['primary'][@$patient->id];
                                elseif (isset($insurances['secondary'][@$patient->id]))
                                    $insurance_name = $insurances['secondary'][@$patient->id];
                                elseif (isset($insurances['others'][@$patient->id]))
                                    $insurance_name = $insurances['others'][@$patient->id];
                            }
                            $patient_ins_name = $insurance_name;
                            $open_new_window = 0; // open patient view in same page.
                            $fin_details = @$patient->patient_claim_fin[0];
                            $patient_due = (!empty($patient->total_pat_due)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_pat_due) : '0.00';
                            $ins_due = (!empty($patient->total_ins_due)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_ins_due) : '0.00';
                            $ar_due = (!empty($patient->total_ar)) ? App\Http\Helpers\Helpers::priceFormat(@$patient->total_ar) : '0.00';
                            $patient_name = App\Http\Helpers\Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
                        ?>
                        <tr>
                            <td>{{@$patient->account_no}}</td>
                            <td>{{@$patient_name}}</td>
                            <td>{{@$patient->mobile}}</td>
                            <td>{{@$patient->gender}}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient->dob,'claimdate') }}</td>
                            <td>{{@$patient->ssn}}</td>
                            <td>{{$insurance_name}}</td>
                            <td class="text-right">{{ strip_tags(@$patient_due) }}</td>
                            <td class="text-right">{{ strip_tags(@$ins_due) }}</td>
                            <td class="text-right">{{ strip_tags(@$ar_due) }}</td>
                            <td>{{ App\Http\Helpers\Helpers::timezone(@$patient->created_at, 'm/d/y') }}</td>
                            <td>
                                <div class="@if(@$patient->percentage =='100') patient-100 @elseif(@$patient->percentage =='40') patient-40 @else patient-60 @endif" style=""><span>{{@$patient->percentage}}</span></div>
                            </td>
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