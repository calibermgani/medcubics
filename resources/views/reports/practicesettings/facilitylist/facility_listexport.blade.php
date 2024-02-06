<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Facility</title>
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
            @$facilities_count_summary = $result['facilities_count_summary'];
            @$start_date = $result['start_date'];
            @$end_date = $result['end_date'];
            @$charges = $result['charges'];
            @$practice_opt = $result['practice_opt'];
            @$unit_details = $result['unit_details'];
            @$adjustments = $result['adjustments'];
            @$patient = $result['patient'];
            @$insurance = $result['insurance'];
            @$patient_bal = $result['patient_bal'];
            @$insurance_bal = $result['insurance_bal'];
            @$facilities = $result['facilities'];
            @$search_by = $result['search_by'];
            @$createdBy = $result['createdBy'];
            @$practice_id = $result['practice_id'];
            @$header = $result['header'];
			$heading_name = App\Models\Practice::getPracticeDetails();
            @$req = @$practice_opt;
        ?>
        <table>
            <tr>                   
                <td @if($req == "provider_list") colspan="4" @else colspan="10" @endif style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr> 
            <tr>
                <td @if($req == "provider_list") colspan="4" @else colspan="10" @endif style="text-align:center;">Facility @if($req == "provider_list") List @else Summary @endif</td>
            </tr>
            <tr>
                <td @if($req == "provider_list") colspan="4" @else colspan="10" @endif style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="10" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if($req == "provider_list")
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th style="width:22px;font-size: 10px !important;">Facility Name</th>
                    <th style="width:22px;">POS</th>              
                    <th style="">Created On</th>
                    <th style="">User</th>
                </tr>
            </thead>
            <tbody>
                @if(count((array)@$filter_group_fac_list) > 0)  
                <?php
                    $total_adj = 0;
                    $patient_total = 0;
                    $insurance_total = 0;
                    $count = 0;					
                ?>			
                @foreach(@$filter_group_fac_list as $list)
                <tr>                   
                    <td>{!! @$list->facility_name !!}</td>
                    <td>{!! @$list->pos_details->code !!}-{!! @$list->pos_details->pos !!}</td>                 
                    <td>{!! date('m/d/Y',strtotime(@$list->created_at)) !!}</td>
                    <td>{!! @$list->facility_user_details->short_name !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif                
        @else
        @if(count((array)$facilities) > 0)
        <?php
            $total_adj = $patient_total = $insurance_total = $total = $count = $payments = $total_payments = $tot_avg = $cnts = 0;
            $wallet = isset($patient->wallet) ? $patient->wallet : 0;
        ?>
        @if(!empty($charges) || !empty($adjustments) || !empty($patient) || !empty($insurance) || !empty($patient_bal) || !empty($insurance_bal) || !empty($unit_details))
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">POS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Units</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Charges($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payments($)</th>  
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Avg pmts/Pat($)</th>	
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Pat Balance($)</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Ins Balance($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Total Balance($)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facilities as $key => $list)			
                <?php 
                    $practice_timezone = App\Http\Helpers\Helpers::getPracticeTimeZone();
                    $exp = explode("to",$search_by->{'Transaction Date'});
                    $start_date = date("Y-m-d",strtotime(trim($exp[0])));
                    $end_date = date("Y-m-d",strtotime(trim($exp[1])));
                    $total_bal = @$list->patient_bal+@$list->insurance_bal;
                    $payments = @$list->patient+@$list->insurance;
                    if(!empty(App\Models\Pos::select('id')->where('code',$list->code)->get()->toArray())){
                        $pos_id = App\Models\Pos::select('id')->where('code',$list->code)->get()->toArray()[0]['id'];
                        $count = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where facility_id = ".$list->facility_id." and pos_id = ".$pos_id." and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
                        if(!empty($count)){
                            $patient_cnt = ($count[0]->cnt!=0)?$count[0]->cnt:1;
                        }else{
                            $patient_cnt = 1;
                        }
                            $tot_avg_pmt = ((@$list->patient+@$list->insurance)!=0 && $patient_cnt != 0)?round((@$list->patient+@$list->insurance)/$patient_cnt):0.00;
                    }
                ?>			
                <tr>
                    <td>{!! @$list->facility_name !!}</td>
                    <td>{!! @$list->code !!}-{!! @$list->pos !!}</td>
                    <td style="text-align:left">{!! (@$list->unit_details!='')?$list->unit_details:0 !!}</td>                      
                    <td style="text-align:right; @if(@$list->charges <0) color:#ff0000; @endif" data-format="#,##0.00">{!! isset($list->charges) ? @$list->charges : '0.00' !!}</td>
                    <td style="text-align:right; @if(@$list->adjustments <0) color:#ff0000; @endif" data-format="#,##0.00">{!! isset($list->adjustments)?@$list->adjustments : '0.00' !!}</td>
                    <td style="text-align:right; @if(@$payments <0) color:#ff0000; @endif" data-format="#,##0.00">{!! isset($payments) ? $payments : '0.00' !!}</td>
                    <td style="text-align:right; @if(@$tot_avg_pmt <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $tot_avg_pmt !!}</td>
                    <td style="text-align:right; @if(@$list->patient_bal <0) color:#ff0000; @endif" data-format="#,##0.00">{!! isset($list->patient_bal) ? @$list->patient_bal : '0.00' !!}</td>
                    <td style="text-align:right; @if(@$list->insurance_bal <0) color:#ff0000; @endif" data-format="#,##0.00">{!! isset($list->insurance_bal) ? @$list->insurance_bal : '0.00' !!}</td>
                    <td style="text-align:right; @if(@$total_bal <0) color:#ff0000; @endif" data-format="#,##0.00">{!! $total_bal !!}</td> 
                </tr>
                 <?php $tot_avg += $tot_avg_pmt; ?>
                @endforeach
                <?php
                    if(!empty($header)){
                        $counts = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where facility_id in (".implode(',', $header).") and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
                    } else {
                        $counts = DB::select("select count(claim.facility_id) as cnt from (select facility_id, pos_id from claim_info_v1 where DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date' and deleted_at is null group by patient_id) as claim");
                    }
                    if(!empty($counts)){
                        $cnts = $counts[0]->cnt;
                        $tot_avg = ($cnts != 0)?$tot_avg/$cnts:$tot_avg;
                    }
                    //$tot_avg = (($patient+$insurance)!=0)?round(($patient+$insurance)/$cnts):0.00;
                ?>
                @if(!empty($facilities))
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-weight:600;">Totals</td>
                    <td></td>
                    <td style="font-weight:600;text-align:left;">{{@$unit_details}}</td>
                    <td style="text-align:right;font-weight:600; @if(@$charges <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{{@$charges}}</td>
                    <td style="text-align:right;font-weight:600; @if(@$adjustments <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{{@$adjustments}}</td>
                    <td style="text-align:right;font-weight:600; @if($patient+$insurance <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $patient+$insurance !!}</td>
                    <td style="text-align:right;font-weight:600; @if(@$tot_avg <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $tot_avg !!}</td>
                    <td style="text-align:right;font-weight:600; @if(@$patient_bal <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $patient_bal !!}</td>
                    <td style="text-align:right;font-weight:600; @if(@$insurance_bal <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $insurance_bal !!}</td>
                    <td style="text-align:right;font-weight:600; @if(@$patient_bal+@$insurance_bal <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $patient_bal+$insurance_bal !!}</td>
                </tr>
                @endif
            </tbody>
        </table>
        
        @endif
        @endif
        @endif
		<?php $wallet = (isset($result['wallet']) )? $result['wallet'] : 0; ?>					
		<div style="margin: 0 15px;">
			<table>
				<td>Wallet Balance</td>
				<td style="font-weight:600;" data-format='"$"#,##0.00_-'>{{ $wallet }}</td>
			</table>
		</div>

        <td @if($req == "provider_list") colspan="4" @else colspan="10" @endif>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>