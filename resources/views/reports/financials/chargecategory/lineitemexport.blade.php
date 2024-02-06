<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Charge Category Report</title>
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
            $total_count_f2 = $result['total_count_f2'];
            $total_count_g2 = $result['total_count_g2'];
            $charges_list = $result['charges_list'];
            $total_arr = $result['total_arr'];
            $createdBy = $result['createdBy'];
            $practice_id = $result['practice_id'];
            $export = $result['export'];
            $search_by = $result['search_by'];
            $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Charge Category Report</td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count($charges_list) > 0)  
            <table>
                <thead>
                    <tr>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT/HCPCS Category</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">CPT/HCPCS</th>                                                                            
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Description</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units</th>                                                                            
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charge Amt($)</th>
                        <?php /* 
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Payments($)</th>
                        */ ?>
                        <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Work RVU($)</th>                                                  
                    </tr>
                </thead>
                <tbody>
                    <?php $temp = 0; $inc = 0;
                        $total_arr = json_decode(json_encode($total_arr), true);                   
                    ?>
                    @foreach($charges_list as  $result)
                    <?php
                        $inc++;
                        $provider_id = $result->provider_id;
                        $provider_name = 'Rendering Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);
                    ?>
                    <?php if($temp != $provider_id){ ?>
                    <tr>
                        <td>{{$provider_name}}</td> 
                    </tr>
                    <?php }?>
                    <tr>
                       <td>{!! @$result->procedure_category !!}</td> 
                       <td class="text-left" style="text-align:left;">{{ @$result->cpt_code}}</td> 
                       <td class="text-left" style="text-align:left;">{{ @$result->description}}</td> 
                       <td class="text-left" style="text-align:left;">{{ @$result->provider_short_name }} - {{ @$result->provider_name }}</td> 
                       <td class="text-left" style="text-align:left;">{!! @$result->units !!}</td>                          
                       <td class="text-right <?php echo(@$result->charge)<0?'med-red':'' ?>" style="text-align:right; @if(@$result->charge <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$result->charge !!}</td> 
                       <?php /* 
                       <td class="text-right <?php echo(@$result->payment)<0?'med-red':'' ?>" style="text-align:right; @if(@$result->payment <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$result->payment !!}</td> 
                       */ ?>
                       <td class="text-right <?php echo(@$result->work_rvu)<0?'med-red':'' ?>" style="text-align:right; @if(@$result->work_rvu <0) color:#ff0000; @endif" data-format="#,##0.00">{!! @$result->work_rvu !!}</td>
                   </tr> 
                    <?php if($inc == @$total_arr[$provider_id]['last_rec']+1){ ?>     
                    <tr>
                        <td class="font600" style="font-weight:600;">Totals</td>
                        <td class="font600" style="font-weight:600;"></td>
                        <td class="font600" style="font-weight:600;"></td>
                        <td class="font600" style="font-weight:600;"></td>
                        <td class="text-left font600" style="text-align:left;font-weight:600;">{!! @$total_arr[$provider_id]['units'] !!}</td>
                        <td class="text-right font600 <?php echo($total_arr[$provider_id]['charge'])<0?'med-red':'' ?>" style="font-weight:600;text-align:right; @if($total_arr[$provider_id]['charge'] <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $total_arr[$provider_id]['charge'] !!}</td>
                        <?php /*
                        <td class="text-right font600 <?php echo($total_arr[$provider_id]['payment'])<0?'med-red':'' ?>" style="font-weight:600;text-align:right; @if($total_arr[$provider_id]['payment'] <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $total_arr[$provider_id]['payment'] !!}</td>
                         */ ?>
                        <td class="text-right font600 <?php echo($total_arr[$provider_id]['work_rvu'])<0?'med-red':'' ?>" style="font-weight:600;text-align:right; @if($total_arr[$provider_id]['work_rvu'] <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $total_arr[$provider_id]['work_rvu'] !!}</td>                    
                    </tr>
                    <?php }?>
                    <?php  $temp = $provider_id;?>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div colspan="8"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>