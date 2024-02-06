<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Provider</title>
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
            @$providers_c_summary = $result['providers_c_summary'];
            @$start_date = $result['start_date'];
            @$end_date = $result['end_date'];
            @$providers_c_wallet = $result['providers_c_wallet'];
            @$practiceopt = $result['practiceopt'];
            @$providers = $result['providers'];
            @$header = $result['header'];
            @$charges = $result['charges'];
            @$writeoff = $result['writeoff'];
            @$pat_adj = $result['pat_adj'];
            @$ins_adj = $result['ins_adj'];
            @$patient = $result['patient'];
            @$insurance = $result['insurance'];
            @$patient_bal = $result['patient_bal'];
            @$insurance_bal = $result['insurance_bal'];
            @$units = $result['units'];
            @$createdBy = $result['createdBy'];
            @$practice_id = $result['practice_id'];
			@$heading_name = App\Models\Practice::getPracticeDetails();
            $req = @$practiceopt;
        ?>
        <table>
            <tr>                   
                <td @if($req == "provider_list") colspan="4" @else colspan="13" @endif style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td @if($req == "provider_list") colspan="4" @else colspan="13" @endif style="text-align:center;">Provider @if($req == "provider_list") List @else Summary @endif</td>
            </tr>
            <tr>
                <td @if($req == "provider_list") colspan="4" @else colspan="13" @endif style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="13" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($header as $key => $val)
                    @if($i > 0){{' | '}}@endif
                    <span>{{ $key }} : </span>{{ $val }}                           
                    <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if($req == "provider_list")
        <table>
            <thead >
                <tr>
                    <th style="font-size: 10px !important;text-align:center;">Provider Name</th>
                    <th style="text-align:center;">Type</th>              
                    <th style="text-align:center;">Created On</th>
                    <th style="text-align:center;">User</th>
                </tr>
            </thead>
            <tbody>
                @if(count((array)$filter_group_list) > 0)  
                <?php 
					$total_adj = 0;
					$patient_total = 0;
					$insurance_total = 0;
				?>
                @foreach($filter_group_list as $list)
                <tr>                   
                    <td style="float:left;">{!! @$list->provider_name !!}</td>
                    <td style="float:left;">{!! @$list->provider_types->name !!}</td>
                    <td style="float:left;">{!! date('m/d/Y',strtotime(@$list->created_at)) !!}</td>
                    <td style="float:left;">{!! @$list->provider_user_details->short_name !!}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        @else
        <table>
            <thead >
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">@if($header->{'Provider Type'}=="Billing")Billing @else Rendering @endif</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Units</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">W/O($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Adj($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Pmts($)</th>     
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Pmts($)</th>     
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Pmts($)</th>     
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Pat Balance($)</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Balance($)</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Total Balance($)</th> 
                </tr>
            </thead>
            <tbody>
                 @if(!empty($providers) && count($providers)> 0)
                    @foreach($providers as $list)
                    <tr style="cursor:default;"> 
                        <td>{!! @$list->short_name." - ".$list->provider_name !!}</td>
						<?php 
							$name = $list->provider_name;
							$prID = $list->id;
							$charge = isset($charges->$prID) ? $charges->$prID : 0;
							$unit = isset($units->$prID) ? $units->$prID : 0;
							$wo = isset($writeoff->$prID) ? $writeoff->$prID : 0;
							$patient_adj = isset($pat_adj->$prID) ? $pat_adj->$prID : 0;
							$insurance_adj = isset($ins_adj->$prID) ? $ins_adj->$prID : 0;
							$pat_pmt = isset($patient->$prID) ? $patient->$prID : 0;
							$ins_pmt = isset($insurance->$prID) ? $insurance->$prID : 0;
							$pat_bal = isset($patient_bal->$prID) ? $patient_bal->$prID : 0;
							$ins_bal = isset($insurance_bal->$prID) ? $insurance_bal->$prID : 0;
						?> 
                        <td style="text-align:left;">{!! $unit !!}</td>
                        <td style="text-align:right; @if(@$charge <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $charge !!}</td>
                        <td style="text-align:right; @if(@$wo <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $wo !!}</td>
                        <td style="text-align:right; @if(@$patient_adj <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $patient_adj !!}</td>
                        <td style="text-align:right; @if(@$insurance_adj <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $insurance_adj !!}</td>
                        <td style="text-align:right; @if(@$wo+$patient_adj+$insurance_adj <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $wo+$patient_adj+$insurance_adj !!}</td>
                        <td style="text-align:right; @if(@$pat_pmt <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $pat_pmt !!}</td>
                        <td style="text-align:right; @if(@$ins_pmt <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $ins_pmt !!}</td>
                        <td style="text-align:right; @if($pat_pmt+$ins_pmt <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $pat_pmt+$ins_pmt !!}</td>
                        <td style="text-align:right; @if(@$pat_bal <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $pat_bal !!}</td>
                        <td style="text-align:right; @if(@$ins_bal <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $ins_bal !!}</td>
                        <td style="text-align:right; @if(@$pat_bal+$ins_bal <0) color:#ff0000; @endif " data-format="#,##0.00">{!! $pat_bal+$ins_bal !!}</td>   
                    </tr>
                    @endforeach
                    @endif  
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="3" style="color: #00877f;font-weight: bold;font-size:13.5px;"><h3>Summary</h3></td>
            </tr>
            <thead>
                <tr>
                    <th style="font-weight:600;">Title</th>
                    <th style="font-weight:600;text-align:right;">Value</th>
                </tr>
            </thead>
            <tbody>
                <?php
					$wallet = isset($patient->wallet)?$patient->wallet:0;
					if($wallet<0)
						$wallet = 0;
				?>
                <tr>
                    <td>Wallet Balance</td>
                    <td style=" @if($wallet <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{{$wallet}}</td>
                </tr>
                <tr> 
                    <td>Total Units</td>                                            
                    <td style="text-align:right;font-weight:600;">{!! array_sum((array)$units) !!}</td>                      
                </tr>
                <tr> 
                    <td>Total Charges</td>                                            
                    <td style=" @if((array_sum((array)$charges)) <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! array_sum((array)$charges) !!}</td>                      
                </tr>
                <tr> 
                    <td>Total Adjustments ( Writeoff included )</td>                                            
                    <td style=" @if((array_sum((array)$writeoff)+array_sum((array)$pat_adj)+array_sum((array)$ins_adj)) <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! array_sum((array)$writeoff)+array_sum((array)$pat_adj)+array_sum((array)$ins_adj) !!}</td>
                </tr> 
                <tr> 
                    <td>Total Payments</td>                                            
                    <td style=" @if((array_sum((array)$patient)+array_sum((array)$insurance)) <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'>{!! array_sum((array)$patient)+array_sum((array)$insurance) !!}</td>                                            
                </tr>
                <tr> 
                    <td>Total Balance</td>                                            
                    <td style=" @if((array_sum((array)$patient_bal)+array_sum((array)$insurance_bal)) <0) color:#ff0000; @endif text-align:right;font-weight:600;" data-format='"$"#,##0.00_-'> {!! array_sum((array)$patient_bal)+array_sum((array)$insurance_bal) !!}</td>
                </tr>  
            </tbody>
        </table>
        @endif
        <td @if($req == "provider_list") colspan="4" @else colspan="13" @endif>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>