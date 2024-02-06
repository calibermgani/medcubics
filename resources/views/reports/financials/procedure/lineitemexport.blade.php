<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Procedure Collection Report</title>
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
        $cptreport_list = $result['cptreport_list'];
        $createdBy = $result['createdBy'];
        $practice_id = $result['practice_id'];
        $export = $result['export'];
        $search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>
                <td colspan="13" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="13" style="text-align:center;">Procedure Collection Report - Insurance Only</td>
            </tr>
            <tr>
                <td colspan="13" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="13" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($cptreport_list) > 0)
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Amount($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payment Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Allowed Amount($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payment Amount($)</th>                                                        
                </tr>             
            </thead>   
            <tbody>
                <?php   $total_amt_charge = 0;$total_amt_payment=0; ?>    
                @foreach($cptreport_list as  $result)
                    <?php                              
                        //   $insurance_payment[] = $dates->insurance_payment;
                         //  $patient_payment[] = $dates->patient_payment; 
                       @$last_name = $result->patient_details->last_name;
                       @$first_name = $result->patient_details->first_name;
                       @$middle_name = $result->patient_details->middle_name;
                       @$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                    ?>
                    <tr>
                        <?php /* SP  */
                            if(isset($result->account_no) && $result->account_no != ''){
                        ?>
                        <td style="text-align:left;">{!! @$result->cpt_code !!}</td> 
                        <td>{!! @$result->date_of_service !!}</td>
                        <td style="text-align:left;">{!! @$result->account_no !!}</td>
                        <td style="text-align:left;">{!! @$result->claim_number !!}</td>
                        <td>{!! @$result->patient_name  !!}</td>
                        <td>{!! @$result->rendering_short_name !!} - {!! @$result->rendering_name !!}</td>
                        <td>{!! @$result->type_name !!}</td>
                        <td>{!! @$result->insurance_short_name !!} - {!! @$result->insurance_name !!}</td>
                        <td>{!! @$result->charge_date !!}</td> 
                        <td data-format='#,##0.00' style="<?php echo($result->charge)<0?'color:#ff0000;':'' ?> text-align: right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td> 
                        <td>{!! @$result->payment_date !!}</td> 
                        <td data-format='#,##0.00' style="<?php echo($result->allowed)<0?'color:#ff0000;':'' ?> text-align: right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->allowed) !!}</td> 
                        <td data-format='#,##0.00' style="<?php echo($result->Payment_amount)<0?'color:#ff0000;':'' ?> text-align: right;">{!! App\Http\Helpers\Helpers::priceFormat(@$result->Payment_amount) !!}</td>
                        <?php 
                            } else {
                        ?>                       
                        <td style="text-align:left;">{{ @$result->cpt_code}}</td>
                        <td>{!! date('m/d/Y',strtotime(@$result->date_of_service)) !!}</td>
                        <td style="text-align:left;">{{ @$result->patient_details->account_no}}</td> 
                        <td style="text-align:left;">{{ @$result->claim_details->claim_number}}</td> 
                        <td>{!! @$patient_name !!}</td>
                        <td>{{ @$result->claim_details->rend_providers->short_name}} - {{ @$result->claim_details->rend_providers->provider_name}}</td>
                        <td>{{ @$result->type_name}}</td>
                            <?php $ins = App\Models\Insurance::where('id', @$result->payer_insurance_id)->value("insurance_name"); ?>
                        <td>{{ $ins }}</td>
                        <td>{{ App\Http\Helpers\Helpers::dateFormat(@$result->charge_date, 'date') }}</td> 
                        <td data-format='#,##0.00' style="<?php echo($result->charge)<0?'color:#ff0000;':'' ?> text-align: right;">{!! @$result->charge !!}</td> 
                        <td>{{ App\Http\Helpers\Helpers::dateFormat(@$result->payment_date, 'date') }}</td> 
                        <td data-format='#,##0.00' style="<?php echo($result->allowed)<0?'color:#ff0000;':'' ?> text-align: right;">{!! @$result->allowed !!}</td> 
                        <td data-format='#,##0.00' style="<?php echo($result->Payment_amount)<0?'color:#ff0000;':'' ?> text-align: right;">{!! @$result->Payment_amount !!}</td>
                        <?php } ?>
                        <?php 
                            $total_amt_payment += @$result->Payment_amount;
                            $total_amt_charge += @$result->charge;
                        ?>
                   </tr>
                @endforeach
                  <tr>
                      <th colspan="12" style="font-weight:bold;text-align:right;">Totals</th>
                      <th style="text-align:right;font-weight:bold;<?php echo($total_amt_payment)<0?'color:#ff0000;':'' ?>" data-format='"$"#,##0.00_-'>{!! $total_amt_payment !!}</th>
                  </tr>
            </tbody>   
        </table>
        @endif
        <td colspan="13">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>
