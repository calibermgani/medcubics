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
                text-align:center !important;
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
            $claims = $result['claims'];
            $search_by  = $result['search_by']; 
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Year End Financials</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                            <span>{!! $key !!} : </span>{{ @$val }}                           
                            <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="7" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="8" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">            
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th rowspan="2" colspan="1" style="border-left: 1px solid #ccc;border-right: 1px solid #ccc;">Month</th>
                            <th rowspan="2" colspan="1" style="border-right: 1px solid #ccc;">Claims</th>
                            <th rowspan="2" colspan="1" style="border-right: 1px solid #ccc;">Charges($)</th>
                            <th colspan="3" style="border-right: 1px solid #ccc;">Adjustment($)</th>
                            <th colspan="3" style="border-right: 1px solid #ccc;">Refunds($)</th>
                            <th colspan="3" style="border-right: 1px solid #ccc;">Payment($)</th>
                            <th colspan="3" style="border-right: 1px solid #ccc;">AR Bal($)</th>
                        </tr>
                        <tr>
                            <th style="border-right: 1px solid #ccc;">Patient</th>
                            <th style="border-right: 1px solid #ccc;">Insurance</th>
                            <th style="border-right: 1px solid #ccc;">Total</th>
                            <th style="border-right: 1px solid #ccc;">Patient</th>
                            <th style="border-right: 1px solid #ccc;">Insurance</th>
                            <th style="border-right: 1px solid #ccc;">Total</th>
                            <th style="border-right: 1px solid #ccc;">Patient</th>
                            <th style="border-right: 1px solid #ccc;">Insurance</th>
                            <th style="border-right: 1px solid #ccc;">Total</th>
                            <th style="border-right: 1px solid #ccc;">Patient</th>
                            <th style="border-right: 1px solid #ccc;">Insurance</th>
                            <th style="border-right: 1px solid #ccc;">Total</th>
                        </tr>
                    </thead>    
                    <tbody>
                        <?php
                            $count = 1;
                            $last_visit = [];
                            $charges = $total_adjustments = $patient_payments = $insurance_payments = $patient_ar_due = $insurance_ar_due = $total_patient_adj = $total_ins_adj = $total_ref_patient = $total_ref_ins = $claims_count = 0;
                        ?> 
                        @if(!empty($claims))
                        @foreach($claims as $key=>$claim_list) 							
                        <?php
                            $ins_adj =$claim_list->insurance_adj;
                            $claims_count += $claim_list->claims_visits;
                        ?>
                        <tr>
                            <td class="text-left" style="border-left: 1px solid #ccc;padding-left:5px;"> {{ $key }}-{{$claim_list->year_key}}</td>
                            <td class="text-left" style="padding-left:5px;">{!! $claim_list->claims_visits !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->value) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->patient_adjusted) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$ins_adj) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_list->total_adjusted) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_list->patient_refund) !!}</td> 
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(-($claim_list->ins_refund)) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat((-($claim_list->ins_refund)) + (@$claim_list->patient_refund)) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->patient_payment) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->insurance_payment) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(($claim_list->insurance_payment)+($claim_list->patient_payment)) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->patient_due) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->insurance_due) !!}</td>
                            <td class="text-right" style="border-right: 1px solid #ccc;">{!! App\Http\Helpers\Helpers::priceFormat($claim_list->insurance_due + $claim_list->patient_due) !!}</td>
                        </tr>
                        <?php
                            $count++;
                            $charges += $claim_list->value;
                            $total_adjustments += $claim_list->total_adjusted;
                            $total_patient_adj += $claim_list->patient_adjusted;
                            $total_ins_adj += $ins_adj;
                            $patient_payments += $claim_list->patient_payment;
                            $total_ref_patient += $claim_list->patient_refund;
                            $total_ref_ins += $claim_list->ins_refund;
                            $insurance_payments += $claim_list->insurance_payment;
                            $patient_ar_due += $claim_list->patient_due;
                            $insurance_ar_due += $claim_list->insurance_due;
                        ?> 
                        @endforeach
                        @endif
                        <tr>
                            <th style="border-left: 1px solid #ccc;">Total</th>
                            <th style="text-align:left !important;padding-left:5px;">{!! $claims_count !!}</th>
                            <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($charges) !!}</th>
                            <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($total_patient_adj) !!}</th>
                            <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($total_ins_adj) !!}</th>
                            <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($total_adjustments) !!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat($total_ref_patient)!!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat(-($total_ref_ins))!!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat((-($total_ref_ins)) + (($total_ref_patient)))!!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat($patient_payments)!!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat($insurance_payments)!!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat($insurance_payments+$patient_payments) !!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat($patient_ar_due)!!}</th>
                            <th class="text-right">${!!App\Http\Helpers\Helpers::priceFormat($insurance_ar_due)!!}</th>
                            <th class="text-right" style="border-right: 1px solid #ccc;">${!!App\Http\Helpers\Helpers::priceFormat(($insurance_ar_due+$patient_ar_due))!!}</th>
                        </tr>
                    </tbody>  
                </table>
            </div>
        </div>
    </body>
</html>