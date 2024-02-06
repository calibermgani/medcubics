<table class="popup-table-border  table-separate table m-b-m-1">
                        <thead>
                            <tr>
                                <th class="text-center" style="border-right: 1px solid #fff ">Status
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
                                <td class="font600 text-center  line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center  line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Claims</span>
                                </td>
                                <td class="font600 text-center line-height-26"
                                    style="border-right: 1px solid #a4ede9;background: #dbfaf8;"><span class="med-green"> Value</span>
                                </td>
                                <td class="font600 text-center "
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
                        <tbody>
                            <?php
                                $claims_status_balances = (array) $claims_status_balances;
                                $total_status_arr = [];
                                $main_status_total = 0;
                            ?>
                            @foreach($claims_status_balances as $keys => $claims_balances)
                            <tr>
                                <?php $total_data = 0; ?>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                        class="med-green"><a href="{{url('/armanagement/armanagementlist?search=yes&status=')}}{{ $keys }}" target="_blank"> {{$keys}}</a></span></td>
                                <?php $claims_balance = (array) $claims_balances; ?>
                                @foreach($claims_balance as $key => $claims_bal)
                                <?php
                                    if (array_key_exists($key, $total_status_arr)) {
                                        array_push($total_status_arr[$key], @$claims_bal[0]->total_ar);
                                        if($total_status_arr['count']>0 && $claims_bal[0]->claim_count>0)
                                        $total_status_arr['count'][$key] = $total_status_arr['count'][$key] + @$claims_bal[0]->claim_count;
                                    } else {
                                        $total_status_arr[$key] = [ @$claims_bal[0]->total_ar];
                                        $total_status_arr['count'][$key] = @$claims_bal[0]->claim_count;
                                    }
                                    $claim_count_sin  = (@$claims_bal[0]->claim_count>0)?@$claims_bal[0]->claim_count:0;
                                    if($key=='Unbilled' && $keys=='Patient')
                                        $claim_count_sin  = (@$claims_bal[0]->claim_count=='NA')?@$claims_bal[0]->claim_count:'na';
                                ?>
                                <td class="font600 text-center bg-white">{{@$claim_count_sin}}</td>
                                <td class="font600 text-right bg-white" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$claims_bal[0]->total_ar)!!}</td>
                                <?php
                                    $total_data+= @$claims_bal[0]->total_ar;
                                    $main_status_total+= @$claims_bal[0]->total_ar;
                                ?>
                                @endforeach
                                <td class="font600 text-right bg-white line-height-26"><span>
                                    {!!App\Http\Helpers\Helpers::priceFormat(@$total_data)!!} </span>
                                </td>
                            </tr>
                            @endforeach
                            <?php
                                $total_status_sum = 0;
                                $total_percent_status_cal = 0;
                                $total_claim_count = $total_status_arr['count'];
                                unset($total_status_arr['count']);
                            ?>
                            <tr>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span class="med-orange"> Outstanding AR</span>
                                </td>
                                @foreach($total_status_arr as $key=>$tot_arr)
                                <?php
                                    $value = 0;
                                    $total_sum = array_sum($tot_arr);
                                    $total_status_sum+= $total_sum;
                                    $value = ($total_sum != 0) ? $total_sum / $main_status_total : 0;
                                    $total_status_percent[$key] = round(($value * 100),2);
                                    $total_percent_status_cal = $total_status_percent[$key] + $total_percent_status_cal;
                                    $claim_count_total = (@$total_claim_count[$key]>0)?@$total_claim_count[$key]:0;
                                ?>
                                <td class="font600 text-center bg-white med-orange">{{@$claim_count_total}}</td>
                                <td class="font600 text-right bg-white med-orange" style="border-right: 1px solid #CDF7FC">{!!App\Http\Helpers\Helpers::priceFormat(@$total_sum)!!}</td>
                                @endforeach
                                <td class="font600 text-right bg-white med-orange">{!!App\Http\Helpers\Helpers::priceFormat(@$total_status_sum)!!}</td>
                            </tr>

                            <tr>
                                <td class="font600 bg-white line-height-26" style="border-right: 1px solid #CDF7FC"><span
                                        class="med-green"> %</span></td>
                                <!--td class="font600 text-right bg-white"></td-->
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['Unbilled']!!}
                                    %
                                </td>
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['0-30']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['31-60']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['61-90']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['91-120']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['121-150']!!}
                                    %
                                </td>
                                
                                <td colspan="2" class="font600 text-center bg-white med-green" style="border-right: 1px solid #CDF7FC">{!!$total_status_percent['>150']!!}
                                    %
                                </td>

                                <td class="font600 text-center bg-white med-green">{{round($total_percent_status_cal)}}
                                    %
                                </td>
                            </tr>
                        </tbody>
                    </table>