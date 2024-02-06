<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Payer</title>
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
			@$payers = $result['payers'];
			@$start_date = $result['start_date'];
			@$end_date = $result['end_date'];
			@$charges = $result['charges'];
			@$adjustments = $result['adjustments'];
			@$insurance = $result['insurance'];
			@$insurance_bal = $result['insurance_bal'];
			@$unit_details = $result['unit_details'];
			@$tot_units = $result['tot_units'];
			@$tot_charges = $result['tot_charges'];
			@$total_adj = $result['total_adj'];
			@$total_pmt = $result['total_pmt'];
			@$insurance_total = $result['insurance_total'];
			@$search_by = $result['search_by'];
			@$createdBy = $result['createdBy'];
			@$practice_id = $result['practice_id'];
			$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="7" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;">Payer Summary</td>
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{ $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="7" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key => $val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(isset($payers) && count((array)$payers) > 0)
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adjustments($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payments($)</th>   
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Balance($)</th>                    
                </tr>                       
            </thead>   
            <tbody>
                @foreach($payers as  $list)
                <?php
					$insurance_name = $list->insurance_name;
                    $insurance_id = $list->insurance_id;
					$insurance_category = @$list->insurance_category;
					$units = isset($unit_details->$insurance_id) ? $unit_details->$insurance_id : 0;
					$total_charge = isset($charges->$insurance_id) ? $charges->$insurance_id : 0;
					$adjustment = isset($adjustments->$insurance_id) ? $adjustments->$insurance_id : 0;
					$pmt = isset($insurance->$insurance_id) ? $insurance->$insurance_id : 0;
					$ins_bal = isset($insurance_bal->$insurance_id) ? $insurance_bal->$insurance_id : 0;
                ?>
                <tr>                        
                    <td style="text-align:left;">{{$insurance_name}}</td>           
                    <td style="text-align:left;">{{ $insurance_category }}</td>
                    <td style="text-align:left;">{!! $units!!}</td>
                    <td style=" @if(@$total_charge <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $total_charge !!}</td>
                    <td style=" @if(@$adjustment <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $adjustment !!} </td>
                    <td style=" @if(@$pmt <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $pmt !!}</td>
                    <td style=" @if(@$ins_bal <0) color:#ff0000; @endif text-align:right;" data-format="#,##0.00">{!! $ins_bal !!}</td>
                </tr>
                @endforeach                       
            </tbody>   
        </table>
        @endif
        <td colspan="7">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>