<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Patients</title>
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
            @$patients = $result['patients'];
            @$insurance_list = $result['insurance_list'];
            $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>                   
                <td colspan="12" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">Patients List</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Cell Phone</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Gender</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">DOB</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">SSN</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat Due($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Ins Due($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">AR Due($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Created On</th>       
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">%</th>
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
                    <td style="text-align:left;">{{@$patient->account_no}}</td>
                    <td>{{@$patient_name}}</td>
                    <td>@if($patient->mobile != '') {!! @$patient->mobile !!} @else -Nil- @endif</td>
                    <td>{{@$patient->gender}}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient->dob,'claimdate') }}</td>
                    <td style="text-align:left;width:30px;">@if($patient->ssn != '') {!! @$patient->ssn !!} @else -Nil- @endif</td>
                    <td>@if($insurance_name != '') {!! @$insurance_name !!} @else -Nil- @endif</td>
                    <td style="text-align: right;@if(strip_tags(@$patient_due) < 0) color:#ff0000; @endif" data-format="#,##0.00">{{ strip_tags(@$patient_due) }}</td>
                    <td style="text-align: right;@if(strip_tags(@$ins_due) < 0) color:#ff0000; @endif" data-format="#,##0.00">{{ strip_tags(@$ins_due) }}</td>
                    <td style="text-align: right;@if(strip_tags(@$ar_due) < 0) color:#ff0000; @endif" data-format="#,##0.00">{{ strip_tags(@$ar_due) }}</td>
                    <td>{{ App\Http\Helpers\Helpers::timezone(@$patient->created_at, 'm/d/y') }}</td> 
                    <td style="text-align:left;">
                        <div class="@if(@$patient->percentage =='100') patient-100 @elseif(@$patient->percentage =='40') patient-40 @else patient-60 @endif" style=""><span>{{@$patient->percentage}}</span></div>
                    </td>
                </tr>
                @endif
                @endforeach
                @endif
            </tbody>
        </table>
        <td colspan="12">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>