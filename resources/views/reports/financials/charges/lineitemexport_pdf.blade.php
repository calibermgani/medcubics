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
                text-align:left;
                font-size:13px;
                font-weight: 600 !important;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 12px !important;}
            @page { margin: 100px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .text-center{text-align: center;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}            
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
        </style>
    </head>
    <body>
        <?php 
            $claims = $result['claims'];
            $include_cpt_option  = $result['include_cpt_option'];
            $status_option  = $result['status_option']; 
            $charge_date_opt  = $result['charge_date_opt']; 
            $tot_summary  = $result['tot_summary']; 
            $header  = $result['header']; 
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">                    
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Charge Analysis - Detailed</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            @if($header !='' && !empty($header))
                            <?php $i = 1; ?>
                            @foreach($header as $header_name => $header_val)
                            <span>
                                <?php $hn = $header_name; ?>
                                {{ @$header_name }}
                            </span> : {{str_replace('-','/', @$header_val)}}
                            @if($i<count((array)$header)) | @endif 
                            <?php $i++; ?>
                            @endforeach
                            <?php
                                $date_cal = json_decode(json_encode($header), true);
                                $trans = str_replace('-', '/', @$date_cal['Transaction Date']);
                                $dos = str_replace('-', '/', @$date_cal['Date Of Service']);
                            ?>
                            @endif
                        </p>
                    </td>
                </tr>
            </table>

            <table class="new-border" style="width:97%; margin-left: 5px; margin-top: -5px;margin-bottom: -5px;">
                <tr style="font-weight:600;">
                    <th colspan="4" style="border:none"><span><b>Created Date :</b></span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',$practice_id) }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                    @if($header !='' && !empty($header))
						@if($status_option == "all")
							@foreach($header as $header_name => $header_val)
								@if((@$header_name == "Transaction Date" || @$header_name == "Date Of Service" ))
									<th style="color:#00877f"><span><?php $hn = $header_name; ?>{{ @$header_name }}</span> <span>: {{str_replace('-','/', @$header_val)}}</span></th>
								@endif
							@endforeach
						@endif
						<?php
							$date_cal = json_decode(json_encode($header), true);
							$trans = str_replace('-', '/', @$date_cal['Transaction Date']);
							$dos = str_replace('-', '/', @$date_cal['Date Of Service']);
						?>
						@if($status_option != "all")
							@foreach($header as $header_name => $header_val)
								@if(@$header_name == "groupBy" && $charge_date_opt="transaction_date" || @$header_name == "groupBy" && $charge_date_opt="dos_date")
									<th style="text-align:center;"><span>{{ucfirst( @$header_name) }}</span> : {{ ucfirst(@$header_val) }}-{{ @$trans }} {{ @$dos }}</th>
								@endif
							@endforeach
						@endif
                    @endif
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-left:15px;padding-top:0px;">
            @if(!empty($claims))
				<?php
					$count = 0;  $total_amt_bal = 0; $count_cpt =0; $claim_billed_total = 0; $claim_paid_total = 0; $claim_bal_total = $total_claim = $total_cpt =  0;   
				?>
            @foreach($claims as $claims_list)
				<?php
					$patient = $claims_list->patient;
					$set_title = (@$patient->title)? @$patient->title.". ":'';
					$patient_name = $set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
				?>
            <div class="box-border" style="page-break-after: auto; page-break-inside: avoid;width:95%;padding-top:0px; overflow: hidden;border-radius:0px;">
                <table style="width:100%;">                    
					<tr>
						<td class="med-green" style="border:0px solid red !important;">Claim No</td>
						<td class="med-orange">{{ $claims_list->claim_number }}</td>
						<td class="med-green">Acc No</td>
						<td>{{ @$claims_list->patient->account_no }}</td>
						<td class="med-green">Patient Name</td>
						<td>{{ $patient_name }}</td>
					</tr>
					<tr>
						<td class="med-green">Billing</td>
						<td>{{ @$claims_list->billing_provider->short_name }}</td>
						<td class="med-green">Rendering</td>
						<td>{{ @$claims_list->rendering_provider->short_name }}</td>
						<td class="med-green">Facility</td>
						<td>{{ @$claims_list->facility_detail->short_name }}</td>
					</tr>
					<tr>
						<td class="med-green">Responsibility</td>
						<td>
							@if($claims_list->self_pay=="Yes")
								Self
							@else
								{{ @$claims_list->insurance_details->short_name }}
							@endif
						</td>
						<td class="med-green">User</td>
						<td>{{ @$claims_list->user->short_name }}</td>
						<td class="med-green">Entry Date</td>
						<td>
							@if($claims_list->created_at != "0000-00-00" && $claims_list->created_at != "1970-01-01" )
							<span>{{ App\Http\Helpers\Helpers::timezone(@$claims_list->created_at, 'm/d/y') }}</span>
							@endif
						</td>
					</tr>
					<tr>
						<td class="med-green">POS</td>
						<td>{{ @$claims_list->pos->code}} - {{@$claims_list->pos->pos }}</td>
						<td class="med-green">Status</td>
						<td>{{ @$claims_list->status }}</td>
						<td class="med-green">Insurance Type</td>
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
					</tr>
					@if(isset($claims_list->claim_reference) && $claims_list->claim_reference !='')
					<tr>
						<td class="med-green">Reference</td>
						<td colspan="5">{{ @$claims_list->claim_reference }}</td>                            
					</tr>
					@endif
					@if(isset($claims_list->hold_option->option) && $claims_list->hold_option->option !='')
					<tr>
						<td class="med-green">Hold Reason</td>
						<td colspan="5">{{ @$claims_list->hold_option->option }}</td>                            
					</tr>
					@endif
                </table>
                <table style="width:100%;border: none !important;border-spacing: 0px; margin-top:-15px;">
                    <thead>
                        <tr style="background: #f0f0f0; color: #605955;">
                            <th>DOS</th>
                            <th>CPT</th>
                            @if(in_array('include_cpt_description',$include_cpt_option))
                            <th>CPT Description</th>
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <th>Modifiers</th>
                            @endif
                            @if(in_array('include_icd',$include_cpt_option))
                            <th class="text-left" style="background: #d9f3f0; color: #00877f;" colspan="12">ICD-10</th>
                            @endif
                            <th class="text-left">Units</th>
                            <th class="text-right">Charges($)</th>
                            <th class="text-right">Paid($)</th>
                            <!--<th class="text-right">Total Bal($)</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Claim Row -->
                        @foreach($claims_list->cpttransactiondetails as $cpt_details)
							<?php $icd_values = App\Models\Icd::getIcdValues(@$cpt_details->cpt_icd_map_key); ?>
							<tr>                              
								<td>{{ App\Http\Helpers\Helpers::dateFormat($cpt_details->dos_from,'dob') }}</td>     
								<td>{{ $cpt_details->cpt_code }}</td> 
								@if(in_array('include_cpt_description',$include_cpt_option))
									<td>{{ App\Models\Medcubics\Cpt::Cptshortdescription(@$cpt_details->cpt_code) }}</td>
								@endif
								@if(in_array('include_modifiers', $include_cpt_option))
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
								<td>{{ @$modifier_val }}</td>
								@endif
								<?php $exp = explode(',', $cpt_details->cpt_icd_code); ?>

								@if(in_array('include_icd',$include_cpt_option))
									@for($i=0; $i<12;$i++)                                               
									<td>{{ @$exp[$i] }}</td>  
									@endfor 
								@endif
								<td class="text-left">{!! @$cpt_details->unit !!}</td>
								<td class="text-right">
									{!! App\Http\Helpers\Helpers::priceFormat(@$cpt_details->charge) !!}
									<?php $claim_billed_total += @$cpt_details->charge; ?>
								</td>
								<td class="text-right">
									{!! App\Http\Helpers\Helpers::priceFormat(@$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid) !!}
									<?php 
										$claim_paid_total += @$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid; 
										$bal = @$cpt_details->charge-(@$cpt_details->claim_cpt_fin_details->patient_paid+@$cpt_details->claim_cpt_fin_details->insurance_paid+@$cpt_details->claim_cpt_fin_details->insurance_adjusted+@$cpt_details->claim_cpt_fin_details->patient_adjusted+@$cpt_details->claim_cpt_fin_details->with_held);
									?>
								</td>
								<!--<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($bal) !!}</td>-->
								<?php 
									$total_amt_bal      += @$bal;
									$claim_bal_total    += @$bal;
									$count_cpt += 1;
								?>
							</tr>
                        @endforeach
                        <!-- Claim Total Row -->
                        <tr>                              
                            <td class="text-right"></td>     
                            <td class="text-right"></td> 
                            @if(in_array('include_cpt_description',$include_cpt_option))
                            <td></td>											
                            @endif
                            @if(in_array('include_modifiers',$include_cpt_option))
                            <td class="text-right"></td>
                            @endif
                            @if(in_array('include_icd',$include_cpt_option))
                            <td colspan="12"></td>
                            @endif
                            <td style="border-radius: 20px 0px 0px 20px" class="text-right"><label class="med-green font600 no-bottom">Total</label></td>
                            <td class="text-right font600">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_billed_total) !!}</td>
                            <td class="text-right font600">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_paid_total) !!}</td>
                            <!--<td class="text-right font600">{!! App\Http\Helpers\Helpers::priceFormat(@$claim_bal_total) !!}
							</td>-->
                                <?php 
									$claim_paid_total = $claim_bal_total = $claim_billed_total = 0;
								?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php $count++; ?>
            @endforeach
			<?php /*
            <!--div>
                    <label for="name">Charge Count :<span>{{ @count($sinpage_claim_arr) }}</span></label>
                    <label for="name">Charge Value :<span>${{@$sinpage_charge_amount}}</span></label>
                    <label for="name">No. of CPT Billed :<span>{{@$sinpage_total_cpt}}</span></label>
            </div-->
			*/ ?>
            <div class="summary-table" style="page-break-after: auto; page-break-inside: avoid;">
                <h4 class="med-orange" style="margin-bottom: 0px;">Summary</h4>
                <table style="width:45%;;border:1px solid #ccc;font-size:11px !important">
                    <thead>
                        <tr style="line-height:16px;"> 
                            <th></th>
                            <th class="text-left font600"><span style="font-weight:600;color:#00877f;">Counts</span></th>
                            <th class="text-right font600"><span style="font-weight:600;color: #00877f;">Value($)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border:none !important;line-height: 30px !important;"> 
                            <td class='med-green font600' style="font-size:13px;border:none !important;" >Total Patients</td>
                            <td class="text-left" style="border:none !important;">{{@$tot_summary->total_patient}}</td>
                            <td class="text-right" style="border:none !important;">{{App\Http\Helpers\Helpers::priceFormat(@$tot_summary->total_charge)}}</td>
                        </tr>
                        <tr style="line-height:30px !important;border:none !important;">
                            <td class='med-green font600' style="border:none !important;">Total CPT</td>
                            <td class="text-left" style="border:none !important;">{{@$tot_summary->total_cpt}}</td>
                            <td class="text-right"style="border:none !important;">{{App\Http\Helpers\Helpers::priceFormat(@$tot_summary->total_charge)}}</td>
                        </tr>
                        <tr style="line-height:30px !important;">
                            <td class='med-green font600' style="border:none !important;">Total Units</td>
                            <td class="text-left" style="border:none !important;" >{{@$tot_summary->total_unit}}</td>
                            <td class="text-right" style="border:none !important;">{{App\Http\Helpers\Helpers::priceFormat(@$tot_summary->total_charge)}}</td>
                        </tr>
                        <tr style="line-height:30px !important;">
                            <td class='med-green font600' style="border:none !important;">Total Charges</td>
                            <td class="text-left" style="border:none !important;">{{@$tot_summary->total_claim}}</td>
                            <td class="text-right" style="border:none !important;">{{App\Http\Helpers\Helpers::priceFormat(@$tot_summary->total_charge)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @else
				<div><h5>No Records Found !!</h5></div>
            @endif	
        </div>
    </body>
</html>