<?php try{ ?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>Reports - Adjustment Analysis</title>
    </head>
    <body>
        <?php 
        $adjustments = @$result['adjustment'];
        $sdate = @$result['sdate'];
        $tdate = @$result['tdate'];
        $instype = @$result['instype'];
        $export = @$result['export'];
        $createdBy = @$result['createdBy'];
        $practice_id = @$result['practice_id'];
        $search_by = @$result['search_by'];
        $tot_adjs = @$result['tot_adjs'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tbody>
                <tr>                   
                    <td  colspan="16" style="text-align:center;color: #00877f;font-weight: 800;font-size:13.5px;">{{$heading_name['practice_name']}}</td>                    
                </tr>
                <tr>
                    <td colspan="16" style="text-align:center;font-size:12px;">Adjustment Analysis - Detailed</td>
                </tr>
                <tr>
                    <td colspan="16" style="text-align:center;font-size:12px;">User: @if(isset($createdBy)) {{  $createdBy }} @endif | Created: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</td>
                </tr>
                <tr>
                    <td colspan="16" style="text-align:center;font-size:12px;">
                        <?php $p = 0; ?>
                        @foreach($search_by as $key=>$val)
                        @if($p > 0) {{' | '}} @endif
                        <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                        <?php $p++; ?>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
        @if(count((array)$adjustments)>0)
            <table>
                <thead>
                    <tr>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Patient Name</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Responsibility</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payer</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adj Date</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Adj Reason</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT Adj($)</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Tot Adj($)</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Reference</th>
                        <th  valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
						$j = 0;$ins_adj=0;$pat_adj=$claim_id=0;$last=0;$tot_adj = array(); 
						$adjustments = (array)$adjustments;
						$numItems = count((array)$adjustments);$tot=[]; 
					?>
                    @foreach(@$adjustments as $k=>$adjustment)
						<?php
							$patient_name = @$adjustment->title.' '.App\Http\Helpers\Helpers::getNameformat(@$adjustment->last_name,@$adjustment->first_name,@$adjustment->middle_name);
						?>
                        <tr>
                            <td valign="center" style="text-align: left;"> {{$adjustment->claim_number}}</td>
                            <td valign="center">{{ @$adjustment->patient_name }}</td>
                            <td valign="center">{{ $adjustment->account_no }}</td>
                            <td valign="center">{{ $adjustment->self_pay }}</td> 
                            <td valign="center">{{ @$adjustment->billing_short_name  }} - {{ $adjustment->billing_provider_name  }}</td>
                            <td valign="center">{{ @$adjustment->rendering_short_name}} - {{ $adjustment->rendering_provider_name}}</td>
                            <td valign="center">{{ @$adjustment->facility_short_name }} - {{str_limit($adjustment->facility_name)}}</td> 
                            <td valign="center">{{ $adjustment->insurance_name }}</td>
                            <td valign="center">{{ $adjustment->adj_date }}</td>                            
                            <td valign="center">{{ $adjustment->dos_from }} </td>
                            <td valign="center" style="text-align:left;">{{ @$adjustment->cpt_code }}</td>
                            <td valign="center">{{ @$adjustment->adjustment_shortname }}</td>
                            <td valign="center" style="@if($adjustment->adjustment_amt<0) color:#ff0000; @endif" data-format="#,##0.00">{{$adjustment->adjustment_amt}}</td>
                            <?php
								if($claim_id!=$adjustment->claim_number){
									$j=0;
								} else {
									$j=1;
								}
								$claim_id = $adjustment->claim_number; $tot_adj = []; $i = 0;
								if($j==0 || $k==0)
								foreach(@$adjustments as $key=>$adj){
									if($claim_id==$adj->claim_number){
										$tot_adj[] = $adj->adjustment_amt;
									}else{
										if(!empty($tot_adj))
											$i=1;
									}
									if($i==1){
										$j=1;
										break;
									}
								}
								if($k+count($tot) == $numItems) {
									$i=1;
								}
								krsort($adjustments);$claim='';
								if($last==0)
								foreach(@$adjustments as $kj=>$ad){
									if($claim=='' || $claim==$ad->claim_number){
										$claim = $ad->claim_number;
										$tot[] = $ad->adjustment_amt;
									}
									$last++;
								}
								if(count($tot) == $numItems) {
									if($k==0){
										$i=1;$j=1;
									}
								}
								if($adjustment->adjustment_type=='Patient'){
									$pat_adj += $adjustment->adjustment_amt;
								}else{
									$ins_adj += $adjustment->adjustment_amt;
								}
                            /*if($k ==1)
                                dd($tot_adj);*/
                            ?>
                            @if($i==1)
                            <td valign="center" rowspan="{{ count($tot_adj) }}" data-format="#,##0.00" style="@if(array_sum($tot_adj)<0) color:#ff0000; @endif"> {!! array_sum($tot_adj) !!} </td>
                            <?php unset($tot_adj); ?>
                            @endif
                            <td>{{@$adjustment->reference}}</td>
                            <td valign="center" class="text-center">{{App\Http\Helpers\Helpers::user_names($adjustment->created_by)}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table>
                <tr>
                    <td colspan="16" style="color:#00877f;font-weight: bold;font-weight: 600;font-size:13.5px;"><h3>Summary</h3></td>
                </tr>
                <tbody>
                    <tr>
                        <td valign="center" colspan="2"  style="font-size:12px;font-weight: 600;">Transaction Date</td>
                        <td style="text-align:right;">{{ (@$search_by->{'Transaction Date'}[0]) }}</td>
                    </tr>
                    @if(@$instype == "insurance")
                    <tr>
                        <td colspan="2"  style="font-size:12px;font-weight: 600;">Total Insurance Adjustments</td>
                        <td style="text-align:right; @if( $ins_adj < 0) color:#ff0000; @endif"  data-format='"$"#,##0.00_-'>{!! $ins_adj !!}</td>
                    </tr>
                    @endif
                    @if(@$instype == "self")
                    <tr>
                        <td colspan="2"  style="font-size:12px;font-weight: 600;">Total Patient Adjustments</td>
                        <td style="text-align:right; @if( $pat_adj < 0) color:#ff0000; @endif"  data-format='"$"#,##0.00_-'>{!! $pat_adj !!}</td>
                    </tr>
                    @endif
                    @if(@$instype == "all")
                    <tr>
                        <td colspan="2"  style="font-size:12px;font-weight: 600;">Total Insurance Adjustments</td>
                        <td style="text-align:right; @if( $ins_adj < 0) color:#ff0000; @endif"  data-format='"$"#,##0.00_-'>{!! $ins_adj !!}</td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-size:12px;font-weight: 600;">Total Patient Adjustments</td>
                        <td style="text-align:right; @if( $pat_adj < 0) color:#ff0000; @endif"  data-format='"$"#,##0.00_-'>{!! $pat_adj !!}</td>
                    </tr>
                    <tr>
                        <td colspan="2"  style="font-size:12px;font-weight: 600;">Total Adjustments</td>
                        <td style="text-align:right; @if( ($ins_adj+$pat_adj) < 0) color:#ff0000; @endif"  data-format='"$"#,##0.00_-'>{!! ($ins_adj+$pat_adj) !!}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        @endif    
        <table>
            <tr>
                <td colspan="16"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></td>
            </tr>
        </table>
    </body>
</html>
<?php 
} catch(Exception $e){ \Log::info("Exception in adjustment report. Msg".$e->getMessage()); } 
?>