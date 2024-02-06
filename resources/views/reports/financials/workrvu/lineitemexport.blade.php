<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Work RVU Report</title>
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
        $workrvu_list = $result['workrvu_list'];
        $createdBy = $result['createdBy'];
        $practice_id = $result['practice_id'];
        $export = $result['export'];
        $search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>
                <td colspan="12" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">Work RVU Report</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!}: </span>{{ @$val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($workrvu_list) > 0)
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Transaction Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT/HCPCS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Description</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units Charge($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Charge($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Work RVU($)</th>
                </tr>
            </thead>  
            <tbody>
                @foreach($workrvu_list as  $result)
                <?php
                    @$last_name = @$result->patient_details->last_name;
                    @$first_name = @$result->patient_details->first_name;
                    @$middle_name = @$result->patient_details->middle_name;
                    @$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                    @$total_amt_charge = ($result->units > 0) ? (@$result->charge / @$result->units) : $result->charge;
                ?>
                <tr>
                    <?php //from store procedure
                        if(isset($result->account_no) && $result->account_no != ''){
                    ?>
                    <td>{!! @$result->transaction_date !!}</td>
                    <td>{!! @$result->date_of_service  !!}</td>
                    <td>{!! @$result->account_no !!}</td>
                    <td>{!! @$result->patient_name  !!}</td>
                    <td style="text-align:left;">{!! @$result->cpt_code !!}</td>
                    <td>{!! @$result->medium_description !!}</td> 
                    <td>{!! @$result->rendering_short_name !!} - {!! @$result->rendering_name !!}</td> 
                    <td>{!! @$result->facility_short_name !!} - {!! @$result->facility_name !!}</td>
                    <td style="text-align:left;">{!! @$result->units !!}</td>
                    <td class="text-right" style="text-align:right; @if(@$result->total_amt_charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->total_amt_charge) !!}</td> 
                    <td class="text-right" style="text-align:right; @if(@$result->charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
                    <td class="text-right" style="text-align:right; @if(@$result->work_rvu <0) color:#ff0000; @endif" data-format="#,##0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->work_rvu) !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td>{{ App\Http\Helpers\Helpers::timezone(@$result->transaction_date, 'm/d/y') }}</td>
                    <td>{!! date('m/d/Y',strtotime(@$result->date_of_service)) !!}</td>
                    <td>{{ @$result->patient_details->account_no}}</td>
                    <td>{!! @$patient_name !!}</td>
                    <td class="text-left" style="text-align:left;">{{ @$result->cpt_code}}</td>
                    <td>{{ @$result->medium_description }}</td>
                    <td>{{ @$result->claim_details->rend_providers->short_name}} - {{ @$result->claim_details->rend_providers->provider_name}}</td>
                    <td>{{ @$result->claim_details->facility->short_name}} - {{ @$result->claim_details->facility->facility_name}}</td>
                    <td class="text-left" style="text-align:left;">{{ @$result->units }}</td>
                    <td class="text-right <?php echo(@$total_amt_charge)<0?'med-red':'' ?>" style="text-align:right; @if(@$total_amt_charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$total_amt_charge !!}</td>
                    <td class="text-right <?php echo(@$result->charge)<0?'med-red':'' ?>" style="text-align:right; @if(@$result->charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$result->charge !!}</td>
                    <td class="text-right <?php echo(@$result->work_rvu)<0?'med-red':'' ?>" style="text-align:right; @if(@$result->work_rvu <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$result->work_rvu !!}</td>
                    <?php } ?>
                </tr>
                @endforeach
            </tbody>   
        </table>
        @endif
        <div colspan="12"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>