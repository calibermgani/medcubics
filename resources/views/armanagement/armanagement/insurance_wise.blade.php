<style type="text/css">
                        .insurance_wise table td, .insurance_wise table th{
                            width: 83px;
                        }
                    </style>
<table class="popup-table-border  table-separate table m-b-m-1">
                        <thead style="display: block;">
                            <tr>
                                <th class="text-center" style="border-right: 1px solid #fff ">
                                    Insurance
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">Unbilled
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">0-30
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">31-60
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">61-90
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">91-120
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">121-150
                                </th>
                                <th class="text-center" colspan="2"
                                    style="border-right: 1px solid #fff">>150
                                </th>
                                <th class="text-center" style=" ">Total</th>
                            </tr>
                            <tr>
                                <td class="font600 bg-white line-height-26 text-center"
                                    style="border-right: 1px solid #CDF7FC"><span
                                        class="med-green"></span></td>
                                <td class="font600 text-center line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 bg-white text-center"><span
                                        class="med-green"> </span></td>
                            </tr>
                        </thead>
                        <tbody style="overflow: auto;
    max-height: 700px;
    display: block;">
                            <?php
                                $insurance_aging_data = (array) $insurance_aging_data;
                                $total_arr = [];
                                $main_total = 0
                            ?>
                            @foreach($insurance_aging_data as $key => $insurances)
                            <?php 
                                $insurances_val = (array) $insurances; $count;
                                if(isset($insurances_val['Unbilled']{0}->insurance_id) && !empty($insurances_val['Unbilled']{0}->insurance_id))
                                    $insuranceId = $insurances_val['Unbilled']{0}->insurance_id;
                                elseif(isset($insurances_val['0-30']{0}->insurance_id) && !empty($insurances_val['0-30']{0}->insurance_id))
                                    $insuranceId = $insurances_val['0-30']{0}->insurance_id;
                                elseif(isset($insurances_val['31-60']{0}->insurance_id) && !empty($insurances_val['31-60']{0}->insurance_id))
                                    $insuranceId = $insurances_val['31-60']{0}->insurance_id;
                                elseif(isset($insurances_val['61-90']{0}->insurance_id) && !empty($insurances_val['61-90']{0}->insurance_id))
                                    $insuranceId = $insurances_val['61-90']{0}->insurance_id;
                                elseif(isset($insurances_val['91-120']{0}->insurance_id) && !empty($insurances_val['91-120']{0}->insurance_id))
                                    $insuranceId = $insurances_val['91-120']{0}->insurance_id;                                                
                                elseif(isset($insurances_val['121-150']{0}->insurance_id) && !empty($insurances_val['121-150']{0}->insurance_id))
                                    $insuranceId = $insurances_val['121-150']{0}->insurance_id;
                                elseif(isset($insurances_val['>150']{0}->insurance_id) && !empty($insurances_val['>150']{0}->insurance_id))
                                    $insuranceId = $insurances_val['>150']{0}->insurance_id;    
                                else
                                    $insuranceId = '';
                             ?>
                            @if(isset($insurances_val['Unbilled']) && !empty($insurances_val['Unbilled']) || isset($insurances_val['0-30']) && !empty($insurances_val['0-30']) || isset($insurances_val['31-60']) && !empty($insurances_val['31-60']) || isset($insurances_val['61-90']) && !empty($insurances_val['61-90']) || isset($insurances_val['91-120']) && !empty($insurances_val['91-120']) || isset($insurances_val['121-150']) && !empty($insurances_val['121-150']) || isset($insurances_val['>150']) && !empty($insurances_val['>150']))
                            <tr>
                                <?php $total = 0; ?>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                        class="med-green"><a href="{{url('/armanagement/armanagementlist?search=yes&insurance_id=')}}{{ $insuranceId }}" target="_blank"> {{$key}}</a></span>
                                </td>
                                <?php $insurances_val = (array) $insurances; ?>
                                @foreach($insurances_val as $key => $insurance)
                                <?php
                                    if (array_key_exists($key, $total_arr)) {
                                        array_push($total_arr[$key], @$insurance[0]->insurance_balance);
                                        $total_arr['count'][$key] = $total_arr['count'][$key] + @$insurance[0]->claim_insurance_count;
                                    } else {
                                        $total_arr[$key] = [ @$insurance[0]->insurance_balance];
                                        $total_arr['count'][$key] = @$insurance[0]->claim_insurance_count;
                                    }
                                    $claim_count_single = (@$insurance[0]->claim_insurance_count >0)?@$insurance[0]->claim_insurance_count:0;
                                ?>
                                <td class="font600 text-center bg-white">{{@$claim_count_single}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$insurance[0]->insurance_balance)!!}</td>
                                <?php
                                    $total+= @$insurance[0]->insurance_balance;
                                    $main_total+= @$insurance[0]->insurance_balance;
                                ?>
                                @endforeach
                                <td class="font600 text-right bg-white line-height-26"><span>
                                    {!!App\Http\Helpers\Helpers::priceFormat(@$total)!!}</span>
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            <?php
                                $total_sum_insurance = 0;
                                $total_percent_cal = 0;
                                $total_claim_count = @$total_arr['count'];
                                if (!empty($total_arr['count']))
                                    unset($total_arr['count']);
                            ?>
                        </tbody>
                        <tfoot style="display: block;">
                            <tr>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-orange"> Total Insurance AR</span>
                                </td>
                                <?php //dd($total_arr);?>
                                @if(!empty($total_arr))
                                @foreach($total_arr as $key=>$tot_arr)
                                <?php
                                    $value = 0;
                                    $total_sum = array_sum($tot_arr);
                                    $total_sum_insurance+= $total_sum;
                                    $value = ($total_sum != 0) ? $total_sum / $main_total : 0;
                                    $total_ins_percent[$key] = round(($value * 100),2);
                                    $total_percent_cal = @$total_ins_percent[$key] + $total_percent_cal;
                                ?>
                                <?php $claim_count = (@$total_claim_count[$key] >0)?@$total_claim_count[$key]:'0';?>
                                <td class="font600 text-center bg-white med-orange">{{$claim_count}}</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum)!!}</td>
                                @endforeach
                               
                                <td class="font600 text-right bg-white med-orange">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum_insurance)!!}</td>                                               
                                @endif
                            </tr>
                            <tr>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                        class="med-green"> %</span></td>

                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['Unbilled']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['0-30']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['31-60']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['61-90']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['91-120']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['121-150']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!@$total_ins_percent['>150']!!}
                                    %
                                </td>
                                <?php $total_percent_cal = ($total_percent_cal>100)?100:$total_percent_cal;?>
                                <td class="font600 text-center bg-white med-green">{{round(@$total_percent_cal)}}%</td>
                            </tr>
                        </tfoot>
                    </table>
