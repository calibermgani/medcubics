<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Year End Financials</title>
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
        </style>
    </head>	
    <body>
        <?php 
        $claims_count_co = $result['claims_count_co'] ;
        $claims = $result['claims'] ;
        $createdBy = $result['createdBy'];
        $practice_id = $result['practice_id'] ;
        $export = $result['export'];
        $search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="15" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="15" style="text-align:center;">Year End Financials</td>
            </tr>
            <tr>
                <td colspan="15" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="15" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        <table>
            <thead style="font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th rowspan="2" colspan="1" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Month</th>
                    <th rowspan="2" colspan="1" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claims</th>
                    <th rowspan="2" colspan="1" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>
                    <th colspan="3" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adjustment($)</th>
                    <th colspan="3" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Refunds($)</th>
                    <th colspan="3" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payment($)</th>
                    <th colspan="3" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">AR Bal($)</th>
                </tr>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total</th>
                </tr>
            </thead>    
            <tbody>
                <?php
                    $count = 1;
                    $last_visit = [];
                    $charges = $total_adjustments = $patient_payments = $insurance_payments = $patient_ar_due = $insurance_ar_due = $total_patient_adj = $total_ins_adj = $total_ref_patient = $total_ref_ins = $claims_count = 0;
                ?> 
                @if(count((array)$claims)>0)

                @foreach($claims as $key=>$claim_list) 							
                <?php
								$ins_adj = $claim_list->insurance_adj;
                                $claims_count += $claim_list->claims_visits;
							?>
                <tr style="text-align:right;">
                    <td class="text-left"> {{ $key }}-{{$claim_list->year_key}}</td>
                    <td class="text-left" style="text-align:left;"> {{$claim_list->claims_visits}}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->value)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->value)<0?'med-red':'' ?> text-right">{!! empty($claim_list->value)?0:$claim_list->value !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->patient_adjusted)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->patient_adjusted)<0?'med-red':'' ?> text-right">{!! $claim_list->patient_adjusted !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo(@$ins_adj)<0?'color:#ff0000;':'' ?>" class="<?php echo(@$ins_adj)<0?'med-red':'' ?> text-right">{!! @$ins_adj !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo(@$claim_list->total_adjusted)<0?'color:#ff0000;':'' ?>" class="<?php echo(@$claim_list->total_adjusted)<0?'med-red':'' ?> text-right">{!! @$claim_list->total_adjusted !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo(@$claim_list->patient_refund)<0?'color:#ff0000;':'' ?>" class="<?php echo(@$claim_list->patient_refund)<0?'med-red':'' ?> text-right">{!! @$claim_list->patient_refund !!}</td> 
                    <td data-format="#,##0.00" style="text-align:right;<?php echo((-$claim_list->ins_refund))<0?'color:#ff0000;':'' ?>" class="<?php echo((-$claim_list->ins_refund))<0?'med-red':'' ?> text-right">{!! (-$claim_list->ins_refund) !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo(((-$claim_list->ins_refund)) + (@$claim_list->patient_refund))<0?'color:#ff0000;':'' ?>" class="<?php echo(((-$claim_list->ins_refund)) + (@$claim_list->patient_refund))<0?'med-red':'' ?> text-right">{!! (-($claim_list->ins_refund)) + (@$claim_list->patient_refund) !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->patient_payment)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->patient_payment)<0?'med-red':'' ?> text-right">{!! $claim_list->patient_payment !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->insurance_payment)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->insurance_payment)<0?'med-red':'' ?> text-right">{!! $claim_list->insurance_payment !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo(($claim_list->insurance_payment)+($claim_list->patient_payment))<0?'color:#ff0000;':'' ?>" class="<?php echo(($claim_list->insurance_payment)+($claim_list->patient_payment))<0?'med-red':'' ?> text-right">{!! ($claim_list->insurance_payment)+($claim_list->patient_payment) !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->patient_due)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->patient_due)<0?'med-red':'' ?> text-right">{!! isset($claim_list->patient_due)?$claim_list->patient_due:0 !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->insurance_due)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->insurance_due)<0?'med-red':'' ?> text-right">{!! isset($claim_list->insurance_due)?$claim_list->insurance_due:0 !!}</td>
                    <td data-format="#,##0.00" style="text-align:right;<?php echo($claim_list->insurance_due + $claim_list->patient_due)<0?'color:#ff0000;':'' ?>" class="<?php echo($claim_list->insurance_due + $claim_list->patient_due)<0?'med-red':'' ?> text-right">{!! $claim_list->insurance_due + $claim_list->patient_due !!}</td>
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
                    <th>Total</th>
                    <th style="text-align:left;">{!! $claims_count !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($charges)<0?'med-red':'' ?>" style="text-align:right;<?php echo($charges)<0?'color:#ff0000;':'' ?>">{!! $charges !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($total_patient_adj)<0?'med-red':'' ?>" style="text-align:right;<?php echo($total_patient_adj)<0?'color:#ff0000;':'' ?>">{!! $total_patient_adj !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($total_ins_adj)<0?'med-red':'' ?>" style="text-align:right;<?php echo($total_ins_adj)<0?'color:#ff0000;':'' ?>">{!! $total_ins_adj !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($total_adjustments)<0?'med-red':'' ?>" style="text-align:right;<?php echo($total_adjustments)<0?'color:#ff0000;':'' ?>">{!! $total_adjustments !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($total_ref_patient)<0?'med-red':'' ?>" style="text-align:right;<?php echo($total_ref_patient)<0?'color:#ff0000;':'' ?>">{!! $total_ref_patient!!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo(-($total_ref_ins))<0?'med-red':'' ?>" style="text-align:right;<?php echo(-($total_ref_ins))<0?'color:#ff0000;':'' ?>">{!! -($total_ref_ins) !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo((-($total_ref_ins)) + (($total_ref_patient)))<0?'med-red':'' ?>" style="text-align:right;<?php echo((-($total_ref_ins)) + (($total_ref_patient)))<0?'color:#ff0000;':'' ?>">{!! (-($total_ref_ins)) + (($total_ref_patient)) !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($patient_payments)<0?'med-red':'' ?>" style="text-align:right;<?php echo($patient_payments)<0?'color:#ff0000;':'' ?>">{!! $patient_payments !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($insurance_payments)<0?'med-red':'' ?>" style="text-align:right;<?php echo($insurance_payments)<0?'color:#ff0000;':'' ?>">{!! $insurance_payments !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($insurance_payments+$patient_payments)<0?'med-red':'' ?>" style="text-align:right;<?php echo($insurance_payments+$patient_payments)<0?'color:#ff0000;':'' ?>">{!! $insurance_payments+$patient_payments !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($patient_ar_due)<0?'med-red':'' ?>" style="text-align:right;<?php echo($patient_ar_due)<0?'color:#ff0000;':'' ?>">{!! $patient_ar_due !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo($insurance_ar_due)<0?'med-red':'' ?>" style="text-align:right;<?php echo($insurance_ar_due)<0?'color:#ff0000;':'' ?>">{!! $insurance_ar_due !!}</th>
                    <th data-format='"$"#,##0.00_-' class="<?php echo(($insurance_ar_due+$patient_ar_due))<0?'med-red':'' ?>"style="text-align:right;<?php echo(($insurance_ar_due+$patient_ar_due))<0?'color:#ff0000;':'' ?>">{!! ($insurance_ar_due+$patient_ar_due) !!}</th>
                </tr>
            </tbody>   
        </table>
        <table>
            <tr>
                <td colspan="15">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>