<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Unbilled Claims</title>
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
        @$unbilled_claim_details = $result['unbilled_claim_details'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$total_charges = $result['total_charges'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="11" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>                 
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;">Unbilled Claims Analysis</td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="11" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!}: </span>{{ @$val }}                           
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>  
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payer</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering </th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing </th>  
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Created Date</th>  		
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Days Since Created</th>  		
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>  		
                </tr>
            </thead>
            <tbody>
                <?php $grand_total = 0;  ?>
                @if(count($unbilled_claim_details) > 0)
                @foreach($unbilled_claim_details as $lists)
                <tr>
                    <?php // from stored procedure
                    if(isset($lists->account_no) && $lists->account_no != ''){ 
                    ?>
                    <td style="text-align:left;">{!! @$lists->account_no !!}</td>
                    <td>{!! @$lists->patient_name  !!}</td>
                    <td>{!! @$lists->dos  !!}</td>
                    <td style="text-align:left;">{!! @$lists->claim_number !!}</td>
                    <td>{!! @$lists->insurance_short_name !!} - {!! @$lists->insurance_name !!}</td>
                    <td>{!! @$lists->facility_short_name !!} - {!! @$lists->facility_name !!}</td>
                    <td>{!! @$lists->rendering_provider_short_name !!} - {!! @$lists->rendering_provider_name !!}</td>
                    <td>{!! @$lists->billing_provider_short_name !!} - {!! @$lists->billing_provider_name !!}</td>
                    <td>{!! @$lists->created_at !!}</td>
                    <td style="text-align:left;">{!! @$lists->daysSinceCreatedCount !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td style="text-align:left;">{!! @$lists->patient->account_no !!}</td>
                    <?php $name = App\Http\Helpers\Helpers::getNameformat(@$lists->patient->last_name,@$lists->patient->first_name,@$lists->patient->middle_name); ?>
                    <td>{!! @$name !!}</td>
                    <td>{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$lists->date_of_service, '','-Nil-')  !!}</td>
                    <td style="text-align:left;">{!! @$lists->claim_number !!}</td>		
                    <?php $insurance_name = App\Models\Insurance::where('id', @$lists->insurance_id)->value("insurance_name");?>
                    <td>{!! $insurance_name !!}</td> 
                    <td>{!! isset($lists->facility->short_name) ? @$lists->facility->short_name:"-Nil-" !!}</td>
                    <td>{!! isset($lists->rendering_provider->short_name) ? @$lists->rendering_provider->short_name:"-Nil-" !!}</td>
                    <td>{!! isset($lists->billing_provider->short_name) ? @$lists->billing_provider->short_name:"-Nil-" !!}</td>
                    <td>{{ App\Http\Helpers\Helpers::timezone(@$lists->created_at, 'm/d/y') }}</td>
                    <td style="text-align:left;">{!!  App\Http\Helpers\Helpers::daysSinceCreatedCount(date('Y-m-d',strtotime(@$lists->created_at))) !!}</td>
                    <?php } ?>                    
                    <td class="text-right <?php echo(@$lists->total_charge)<0?'med-red':'' ?>"  style="text-align:right; @if(@$lists->total_charge <0) color:#ff0000; @endif"  data-format="#,##0.00">{!! @$lists->total_charge !!}</td>
                    <?php
                        $grand_total = $grand_total + $lists->total_charge;
                    ?>

                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <table>
            <tr>
                <td colspan="11">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>