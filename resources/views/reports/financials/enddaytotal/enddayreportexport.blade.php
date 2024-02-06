<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports-End of the Day Totals</title>
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
			@$data = $result['response'];
			@$start_date = $result['start_date'];
			@$end_date = $result['end_date'];
			@$createdBy = $result['createdBy'];
			@$practice_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($result['practice_id'],'decode');
			@$search_by = $result['search_by'];
			$heading_name = App\Models\Practice::getPracticeDetails(); 
        ?>
        <table>
            <tr>                   
                <td colspan="10" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>                  
            </tr>
            <tr>
                <td colspan="10" style="text-align:center;">End of the Day Totals</td>
            </tr>
            <tr>
                <td colspan="10" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="10" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key => $val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count((array)$data) > 0)
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th rowspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Date-Day</th>
                    <th rowspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>
                    <th rowspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claims</th>
                    <th rowspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Write-off($)</th>
                    <th colspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adjustments($)</th>
                    <th colspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Refund($)</th>
                    <th colspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payments($)</th>
                    <th rowspan="2" valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Payments($)</th>
                </tr>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Insurance</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;border-top: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient</th>
                    <th></th>
                </tr>
            </thead>   
            <tbody>
                @if(count((array)$data) > 0)  
                <?php
					$total_adj = 0;
					$patient_total = 0;
					$insurance_total = 0;
				?>
                @foreach($data as  $key=>$dates)
                <?php
                    $total_charge = isset($dates->total_charge) ? $dates->total_charge : 0;
                    $writeoff_total = isset($dates->writeoff_total) ? $dates->writeoff_total : 0;
                    $insurance_adjustment = isset($dates->insurance_adjustment) ? $dates->insurance_adjustment : 0;
                    $patient_adjustment = isset($dates->patient_adjustment) ? $dates->patient_adjustment : 0;                
                    $insurance_refund = isset($dates->insurance_refund) ? $dates->insurance_refund : 0;
                    $patient_refund = isset($dates->patient_refund) ? $dates->patient_refund : 0;
                    $insurance_payment = isset($dates->insurance_payment) ? $dates->insurance_payment : 0;
                    $patient_payment = isset($dates->patient_payment) ? $dates->patient_payment : 0;
                    $total_payment = isset($dates->total_payment) ? $dates->total_payment : 0;
                ?>
                <tr>
                    <td>{{$key.'-'.date('D', strtotime($key))}}</td>
                    <td class="text-right <?php echo(@$total_charge)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$total_charge !!} </td>
                    <td class="text-right">@if(@$dates->claims_count != ''){{ @$dates->claims_count }} @else 0 @endif </td>
                    <td class="text-right <?php echo(@$writeoff_total)<0?'med-red':'' ?>" style="text-align:right; @if(@$writeoff_total <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$writeoff_total !!} </td>
                    <td class="text-right <?php echo(@$insurance_adjustment)<0?'med-red':'' ?>" style="text-align:right; @if(@$insurance_adjustment <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$insurance_adjustment  !!}  </td>
                    <td class="text-right <?php echo(@$patient_adjustment)<0?'med-red':'' ?>" style="text-align:right; @if(@$patient_adjustment <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$patient_adjustment !!}  </td>
                    <td class="text-right <?php echo(@$insurance_refund)<0?'med-red':'' ?>" style="text-align:right; @if(@$insurance_refund <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$insurance_refund !!}</td>
                    <td class="text-right <?php echo(@$patient_refund)<0?'med-red':'' ?>" style="text-align:right; @if(@$patient_refund <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$patient_refund !!}</td>
                    <td class="text-right <?php echo(@$insurance_payment)<0?'med-red':'' ?>" style="text-align:right; @if(@$insurance_payment <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$insurance_payment !!} </td>
                    <td class="text-right <?php echo(@$patient_payment)<0?'med-red':'' ?>" style="text-align:right; @if(@$patient_payment <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$patient_payment !!} </td>
                    <td class="text-right <?php echo(@$total_payment)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_payment <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$total_payment !!} </td> 	
                </tr>
                @endforeach
                @endif
            </tbody>   
        </table>
        @endif
        <div colspan="10"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>