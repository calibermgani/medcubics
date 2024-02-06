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
            .text-right{text-align: right !important;padding-right: 5px;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center;}
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
            $facilities = $result['facilities'];
            $charges = $result['charges'];
            $adjustments = $result['adjustments'];
            $patient = $result['patient'];
            $insurance = $result['insurance'];
            $patient_bal = $result['patient_bal'];
            $insurance_bal = $result['insurance_bal'];
            $unit_details = $result['unit_details'];
            $practice_opt = $result['practice_opt'];
            @$search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $header= $result['header'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
            foreach ($facilities as $key => $value) {
                $abb_facility[] = @$value->short_name." - ".@$value->facility_name;
                $abb_pos[] = @$value->code." - ".@$value->pos;
            }
            $abb_facility = array_unique($abb_facility);
            foreach (array_keys($abb_facility, ' - ') as $key) {
                unset($abb_facility[$key]);
            }
            $abb_pos = array_unique($abb_pos);
            foreach (array_keys($abb_pos, ' - ') as $key) {
                unset($abb_pos[$key]);
            }
            $pos_imp = implode(':', $abb_pos);
            $facility_imp = implode(':', $abb_facility);
			$req = @$practice_opt;
        ?>        
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Facility @if($req == "provider_list") List @else Summary @endif</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0;
                            $search_by = isset($search_by) ? $search_by : []; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                            <span>{!! $key !!}: </span>{{ $val }}                           
                            <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="2" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="2" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if($req == "provider_list")
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>Facility Name</th>
                            <th>POS</th>
                            <th>Created On</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($filter_group_fac_list))
                        <?php 
							@$total_adj = 0;
							@$patient_total = 0;
							@$insurance_total = 0;
							@$count = 0;
                        ?>
                        @foreach($filter_group_fac_list as $list)
                        <tr>                   
                            <td>{!! @$list->facility_name !!}</td>
                            <td>{!! @$list->pos_details->code !!}-{!! @$list->pos_details->pos !!}</td>                 
                            <td>{!! date('m/d/Y',strtotime(@$list->created_at)) !!}</td>
                            <td>{!! @$list->facility_user_details->short_name !!}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @else
            @if(!empty($facilities))
            <?php
                $total_adj = $patient_total = $insurance_total = $total = $count = $payments = $total_payments = $tot_avg = $cnts = 0;
                $wallet = isset($patient->wallet) ? $patient->wallet : 0;
            ?>
            @if(!empty($charges) || !empty($adjustments) || !empty($patient) || !empty($insurance) || !empty($patient_bal) || !empty($insurance_bal) || !empty($unit_details)) 
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <th>Facility Name</th>
                            <th>POS</th>
                            <th>Units</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">Adj($)</th>
                            <th class="text-right">Payments($)</th>  	
                            <th class="text-right">Avg pmts/Pat($)</th>
                            <th class="text-right">Pat Balance($)</th> 
                            <th class="text-right">Ins Balance($)</th>
                            <th class="text-right">Total Balance($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facilities as $key => $list)
                        <?php
                        $facility_name = $list->facility_name;
                        $patient_balance = isset($patient_bal->$facility_name) ? $patient_bal->$facility_name : 0;
                        $insurance_balance = isset($insurance_bal->$facility_name) ? $insurance_bal->$facility_name : 0;
                        $charge = isset($charges->$facility_name) ? $charges->$facility_name : 0;
                        $adjustment = isset($adjustments->$facility_name) ? $adjustments->$facility_name : 0;
                        $patient_pmt = isset($patient->$facility_name) ? $patient->$facility_name : 0;
                        $insurance_pmt = isset($insurance->$facility_name) ? $insurance->$facility_name : 0;
                        $payments = $patient_pmt + $insurance_pmt;
                        $total_bal = $patient_balance + $insurance_balance;
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
                            <td style="float:left;">{!! @$list->facility_name !!}</td>
                            <td>{!! @$list->code !!}-{!! @$list->pos !!}</td>
                            <td>{!! isset($unit_details->$facility_name)?$unit_details->$facility_name:0 !!}</td>                      
                            <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat($charge) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($adjustment) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($payments) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($tot_avg_pmt) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($patient_balance) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($insurance_balance) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($total_bal) !!}</td> 
                        </tr>
                        <?php
							$total_adj += $adjustment;
							$patient_total += $patient_balance;
							$insurance_total += $insurance_balance;
							$total += $total_bal;
							$total_payments += $patient_pmt+$insurance_pmt;
							$tot_avg += $tot_avg_pmt;
                        ?>
                        @endforeach
                        <?php
                            $patient_payments = !empty($patient)?array_sum((array)$patient):0.00;
                            $insurance_payments = !empty($insurance)?array_sum((array)$insurance):0.00;
                            $pat_balance = !empty($patient_bal)?array_sum((array)$patient_bal):0.00;
                            $ins_balance = !empty($insurance_bal)?array_sum((array)$insurance_bal):0.00;
                            $total_balance = $pat_balance+$ins_balance;
                        ?>
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
                        @if(count($facilities)>1)
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
                            <td class="med-green font600">Totals</td>
                            <td></td>
                            <td class="text-left med-green font600">{{(!empty($unit_details))?array_sum((array)$unit_details):0.00}}</td>
                            <td class="text-right med-green font600">{{(!empty($charges))?App\Http\Helpers\Helpers::priceFormat(array_sum((array)$charges)):0.00}}</td>
                            <td class="text-right med-green font600">{{!empty($adjustments)?App\Http\Helpers\Helpers::priceFormat(array_sum((array)$adjustments)):0.00}}</td>
                            <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($total_payments) !!}</td>
                            <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($tot_avg) !!}</td>
                            <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($pat_balance) !!}</td>
                            <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($ins_balance) !!}</td>
                            <td class="text-right med-green font600">{!! App\Http\Helpers\Helpers::priceFormat($total_balance) !!}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
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
			
            <ul style="line-height:20px;">
                <li>{{$pos_imp}}</li>
                <li>{{$facility_imp}}</li>
            </ul>
        </div>
    </body>
</html>