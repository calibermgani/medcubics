<?php try{ ?>
<!DOCTYPE html>
<html lang="en"> 
    <head>
        <title>Reports - Charge Analysis</title>
        <style>
            thead {
                background:#fff !important;color:#000 !important;
            }
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
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            
        </style>
    </head>
    <body>
        <?php 
        $claims = @$result['claims'];
        $header = @$result['search_by'];
        $column = @$result['column'];
        $include_cpt_option = @$result['include_cpt_option'];
        $sinpage_charge_amount = @$result['sinpage_charge_amount'];
        $sinpage_claim_arr = @$result['sinpage_claim_arr'];
        $sinpage_total_cpt = @$result['sinpage_total_cpt'];
        $status_option = @$result['status_option'];
        $ftdate = @$result['ftdate'];
        $charge_date_opt = @$result['charge_date_opt'];
        $tot_summary = @$result['tot_summary'];
        $user_names = @$result['user_names'];
        $createdBy = @$result['createdBy'];
        $practice_id = @$result['practice_id'];
        $page = @$result['page'];
        $export = @$result['export'];
        $heading_name = App\Models\Practice::getPracticeName($practice_id); ?>
        <table style="background:#fff;empty-cells: hide !important;">
            <?php
            $colspan = 14;
            if (in_array('include_icd', $include_cpt_option))
                $colspan += 1;
            if (in_array('include_cpt_description', $include_cpt_option))
                $colspan += 1;
            if (in_array('include_modifiers', $include_cpt_option))
                $colspan += 1;
            ?>
            <tr style="background:#fff; empty-cells: hide !important;">
                <td colspan="{{$colspan}}" style="text-align:center;empty-cells: hide !important;"><h3><center>{{$heading_name}}</center></h3> </td>
            </tr>
            <tr style="background:#fff;height:20px !important;vertical-align: middle !important; display: table-cell !important;">
                <td colspan="{{$colspan}}" style="text-align:center;empty-cells: hide !important;">Charge Analysis - Detailed </td>
            </tr>
            <tr style="background:#fff;color:#000 !important;height:30px !important;"><td valign="top" colspan="{{$colspan}}" style="text-align:center;vertical-align: baseline !important;">User : @if(isset($createdBy)) {{  $createdBy }} @endif | Created : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',$practice_id) }}</td></tr>
            <tr style="background:#fff;color:#000 !important;height:30px !important;">
                <td valign="top" colspan="{{$colspan}}" style="text-align:center;vertical-align: baseline !important;wrap-text: true;">
                    @if($header !='' && count((array)$header) > 0)
                    <?php $i = 1; ?>
                    @foreach($header as $header_name => $header_val)
                    <span>
                        <?php $hn = $header_name; ?>
                        {{ @$header_name }}
                    </span> : {{str_replace('-','/', @$header_val)}}
                    @if($i < count((array)$header)) | @endif 
                    <?php $i++; ?>
                    @endforeach
                    <?php
                        $date_cal = json_decode(json_encode($header), true);
                        $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                        $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                    ?>
                    @endif
                </td>
            </tr>
        </table>
        <div>
            <div  style="page-break-after: auto; page-break-inside: avoid;">
                <table>
                    <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                        <tr>
                            <th style="width:8px;border-top:1px solid #d4d4d4;border-right:1px solid #d4d4d4; font-size: 10px !important;" class="text-center">DOS</th>
                            <th style="text-align: center;font-size:10px;">Claim No</th>
                            <th style="text-align:center">Acc No</th>
                            <th style="text-align:center">Patient</th>
                            <th style="text-align:center">Billing</th>
                            <th style="text-align:center">Rendering</th>
                            <th style="text-align:center">Facility</th>
                            <th style="text-align:center">POS</th>
                            <th style="text-align:center">Responsibility</th>
                            <th style="text-align:center">Insurance Type</th>
                            <th style="text-align:center">CPT</th>
                            @if(in_array('include_cpt_description',$include_cpt_option))
                            <th style="text-align:center">CPT Description</th>
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <th style="text-align:center">Modifiers</th>
                            @endif
                            @if(in_array('include_icd',$include_cpt_option))
                            <th style="text-align:center">ICD-10</th>
                            @endif
                            <th style="text-align:center;width: 5px;font-size:10px">Units</th>
                            <th style="text-align:center">Charges($)</th>
                            <th style="text-align:center">Paid($)</th>
                            <!--<th style="text-align:center">Total Balance($)</th>-->
                            <th style="text-align:center">Status</th>
                            @if(isset($header->{'Hold Reason'}))
							<th style="text-align:center">Hold Reason</th>
                            @endif
                            <th style="text-align:center;font-size:12px;font-weight:600;border-bottom:1px solid black;">Entry Date</th>
                            <th style="text-align:center;font-size:12px;font-weight:600;border-bottom:1px solid black;">Reference</th>
                            <th style="text-align:center">First Submission</th>
                            <th style="text-align:center">Last Submission</th>
                            <th style="text-align:center;font-size:12px;font-weight:600;border-bottom:1px solid black;">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count((array)$claims) > 0)
                            <?php $count = $total_amt_bal = $count_cpt = $claim_billed_total = $claim_paid_total = 0; $claim_bal_total = $total_claim = $total_cpt =  0;  ?>
                            @foreach($claims as $claims_list)
                                <?php
                                    $patient = $claims_list->patient;
                                    $set_title = (@$patient->title)? @$patient->title.". ":'';
                                    $patient_name =  $set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name);                                 
                                ?>
                                <!-- Claim Row -->
                                @if(isset($claims_list->cpttransactiondetails) && !empty($claims_list->cpttransactiondetails))
                                    @foreach($claims_list->cpttransactiondetails as $cpt_details)  
                                        <tr> 
											<?php  $icd_values = App\Models\Icd::getIcdValues(@$cpt_details->cpt_icd_map_key);?>   
											<td>{{ App\Http\Helpers\Helpers::dateFormat($cpt_details->dos_from,'dob') }}</td>     
											<td>{{ @$claims_list->claim_number }}</td>
											<td>{{ @$claims_list->patient->account_no }}</td>
											<td>{{ $patient_name }}</td>
											<td>{{ @$claims_list->billing_provider->provider_name }}</td>
											<td>{{ @$claims_list->rendering_provider->provider_name }}</td>
											<td>{{ @$claims_list->facility_detail->facility_name }}</td>
											<td>{{ @$claims_list->pos->code}} - {{@$claims_list->pos->pos }}</td>
											<td>
												@if($claims_list->self_pay=="Yes")
													Self
												@else
													{{ @$claims_list->insurance_details->insurance_name }}
												@endif
											</td>
											<td>													   
												@if($claims_list->self_pay=="Yes")
                                                    N/A
                                                @else
                                                    @if(isset($claims_list->patient->insured_detail[0]->insurance_details->insurancetype->type_name)) {{ @$claims_list->patient->insured_detail[0]->insurance_details->insurancetype->type_name }}
                                                    @else
                                                        Nil
                                                    @endif
                                                @endif
											</td>
											<td style="text-align:left">{{ @$cpt_details->cpt_code }}</td>
											@if(in_array('include_cpt_description',$include_cpt_option))
											<td>{{ App\Models\Medcubics\Cpt::Cptshortdescription(@$cpt_details->cpt_code) }}</td>
											@endif
											@if(in_array('include_modifiers',$include_cpt_option))
											<?php
												$modifier_arr = array();
												if ($cpt_details->modifier1 != '')
													array_push($modifier_arr, $cpt_details->modifier1);
												if ($cpt_details->modifier2 != '')
													array_push($modifier_arr, $cpt_details->modifier2);
												if ($cpt_details->modifier3 != '')
													array_push($modifier_arr, $cpt_details->modifier3);
												if ($cpt_details->modifier4 != '')
													array_push($modifier_arr, $cpt_details->modifier4);
												if (count($modifier_arr) > 0) {
													$modifier_val = implode($modifier_arr, ',');
												} else {
													$modifier_val = '-Nil-';
												}
											?>
											<td>{{@$modifier_val}}</td>
											@endif
											<?php $exp = explode(',', @$cpt_details->cpt_icd_code); ?>
											<?php /* @for($i=0; $i<12;$i++)                                               
												<td style="width: 20px;"> {{ @$exp[$i] }}</td>  
											@endfor  */?>
											@if(in_array('include_icd',$include_cpt_option))
											<td style="text-align:left">{{@$cpt_details->cpt_icd_code}}</td>
											@endif
											<td style="text-align:left">{!! @$cpt_details->unit !!}</td>
											<td style="text-align:right; @if($cpt_details->charge <0) color:#ff0000; @endif"  data-format="#,##0.00">
												{!! @$cpt_details->charge !!}
												<?php $claim_billed_total += @$cpt_details->charge; ?>
											</td>
											<td class="text-right <?php echo(@$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid)<0?'med-red':'' ?>"  style="text-align:right; @if(@$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid <0) color:#ff0000; @endif"  data-format="#,##0.00">
												{!! @$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid !!}
												<?php 
													$claim_paid_total += @$cpt_details->claim_cpt_fin_details->patient_paid+@	
													$cpt_details->claim_cpt_fin_details->insurance_paid; 
													$bal = @$cpt_details->charge-(@$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid+@$cpt_details->claim_cpt_fin_details->insurance_adjusted+@$cpt_details->claim_cpt_fin_details->patient_adjusted+@$cpt_details->claim_cpt_fin_details->with_held);  
												?>
											</td>
											<!--<td style="text-align:right; @if($bal <0) color:#ff0000; @endif"  data-format="#,##0.00">
												{!! $bal !!}
											</td>--> 
											<td>{{ @$claims_list->status }}</td>
                                            @if(isset($header->{'Hold Reason'}))
											<td>{{ (@$claims_list->hold_option->option!='')?@$claims_list->hold_option->option:'Nil' }}</td>
                                            @endif
											<td>
												@if(@$claims_list->created_at != "0000-00-00" && $claims_list->created_at != "1970-01-01" )
												<span class="bg-date">{{ App\Http\Helpers\Helpers::timezone($claims_list->created_at, 'm/d/y') }}</span>
												@endif
											</td>
											<td>{{ (@$claims_list->claim_reference!='')?@$claims_list->claim_reference:"Nil" }}</td>
                                            @if(@$claims_list->submited_date != "0000-00-00 00:00:00" && $claims_list->submited_date != "1970-01-01 00:00:00" )
                                            <td>{{ App\Http\Helpers\Helpers::timezone($claims_list->submited_date, 'm/d/y') }}</td>
                                            @else
                                            <td>-Nil-</td>
                                            @endif
                                            @if(@$claims_list->last_submited_date != "0000-00-00 00:00:00" && $claims_list->last_submited_date != "1970-01-01 00:00:00" )
                                            <td>{{ App\Http\Helpers\Helpers::timezone($claims_list->last_submited_date, 'm/d/y') }}</td>
                                            @else
                                            <td>-Nil-</td>
                                            @endif
											<td>{{ @$claims_list->user->name }}</td>
											<?php  
												$total_amt_bal      += @$bal;
												$claim_bal_total    += @$bal;
												$count_cpt += 1;
											?>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr> 
                                        <td>
                                            @if(@$claims_list->date_of_service != "0000-00-00" && $claims_list->date_of_service != "1970-01-01" )
                                            {{  App\Http\Helpers\Helpers::dateFormat($claims_list->date_of_service,'dob') }}
                                            @endif
                                        </td>     
                                        <td>{{ @$claims_list->claim_number }}</td>
                                        <td>{{ @$claims_list->patient->account_no }}</td>
                                        <td>{{ $patient_name }}</td>
                                        <td>{{ @$claims_list->billing_provider->provider_name }}</td>
                                        <td>{{ @$claims_list->rendering_provider->provider_name }}</td>
                                        <td>{{ @$claims_list->facility_detail->facility_name }}</td>
                                        <td>{{ @$claims_list->pos->code}} - {{@$claims_list->pos->pos }}</td>
                                        <td>
                                            @if($claims_list->self_pay=="Yes")
                                                Self
                                            @else
                                                {{ @$claims_list->insurance_details->insurance_name }}
                                            @endif
                                        </td>
                                        <td>                                       	
											@if(isset($claims_list->patient->insured_detail[0]->insurance_details->insurancetype->type_name))
												{{ @$claims_list->patient->insured_detail[0]->insurance_details->insurancetype->type_name }}
                                            @endif
                                        </td>
                                        <td style="text-align:left"></td>
                                        @if(in_array('include_cpt_description',$include_cpt_option))
                                        <td></td>
                                        @endif
                                        @if(in_array('include_modifiers',$include_cpt_option))
                                        <td></td>
                                        @endif
                                        @if(in_array('include_icd',$include_cpt_option))
                                        <td style="text-align:left"></td>
                                        @endif
                                        <td style="text-align:left"></td>
                                        <td style="text-align:right">0.00</td>
                                        <td style="text-align:right">0.00</td>
                                        <td style="text-align:right">0.00</td> 
                                        <td>{{ @$claims_list->status }}</td>
                                        @if(isset($header->{'Hold Reason'}))
										<td>{{ (@$claims_list->hold_option->option!='')?@$claims_list->hold_option->option:'Nil' }}</td>
                                        @endif
                                        <td>
                                            @if(@$claims_list->created_at != "0000-00-00" && $claims_list->created_at != "1970-01-01" )
                                            <span class="bg-date">{{ App\Http\Helpers\Helpers::timezone($claims_list->created_at, 'm/d/y') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ @$claims_list->claim_reference }}</td>
                                        @if(@$claims_list->submited_date != "0000-00-00 00:00:00" && $claims_list->submited_date != "1970-01-01 00:00:00" )
                                        <td>{{ App\Http\Helpers\Helpers::timezone($claims_list->submited_date, 'm/d/y') }}</td>
                                        @else
                                        <td>-Nil-</td>
                                        @endif
                                        @if(@$claims_list->last_submited_date != "0000-00-00 00:00:00" && $claims_list->last_submited_date != "1970-01-01 00:00:00" )
                                        <td>{{ App\Http\Helpers\Helpers::timezone($claims_list->last_submited_date, 'm/d/y') }}</td>
                                        @else
                                        <td>-Nil-</td>
                                        @endif
                                        <td>{{ @$claims_list->user->name }}</td>
                                    </tr>
                                @endif
                                <!-- Claim Total Row -->
								<?php 
									$claim_billed_total = 0; 
									$claim_paid_total = 0;
									$claim_bal_total = 0; 
									$count++;   
								?> 
							@endforeach
						@endif
                    </tbody>
                </table>
            </div>
			
			@if(count((array)$claims) > 0)
            <div>
                <table>  
                <tr>
                <td class="med-green"  style="font-size:13.5;font-weight:600;color:#00877f;" colspan="2">Summary</td>
                </tr> 
                    <thead>
                        <tr style="background:#fff; ">
                            <th style="border-bottom:1px solid black;"></th>
                            <th style="text-align:center;font-size:12px;font-weight:600;border-bottom:1px solid black;">Counts</th>
                            <th style="text-align:center;font-size:12px;font-weight:600;border-bottom:1px solid black;">Value($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="font600"> 
                            <td style="font-weight:600;font-size:9px;">Total Patients</td>
                            <td style="font-size:9px;font-weight:600;text-align:left;">{{@$tot_summary->total_patient}}</td>
                            <td style="font-size:9px;font-weight:600;text-align:right;"  data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                        <tr class=" font600">
                            <td style="font-weight:600;font-size:9px;">Total CPT</td>
                            <td style="font-size:9px;font-weight:600;text-align:left;">{{@$tot_summary->total_cpt}}</td>
                            <td style="font-size:9px;font-weight:600;text-align:right;"  data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                        <tr class="font600">
                            <td style="font-weight:600;font-size:9px;">Total Units</td>
                            <td style="font-size:9px;font-weight:600;text-align:left;">{{@$tot_summary->total_unit}}</td>
                            <td style="font-size:9px;font-weight:600;text-align:right;"  data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                        <tr class=" font600">
                            <td style="font-weight:600;font-size:9px;">Total Charges</td>
                            <td style="font-size:9px;font-weight:600;text-align:left;">{{@$tot_summary->total_claim}}</td>
                            <td style="font-size:9px;font-weight:600;text-align:right;"  data-format="#,##0.00">{{@$tot_summary->total_charge}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif	
        </div>
        @if($export == "xlsx" || $export == "csv")
        <td colspan="{{$colspan}}" style="margin-left:10px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
        @endif
    </body>
</html>
<?php 
} catch(Exception $e){ \Log::info("Exception Msg".$e->getMessage()); } 
?>