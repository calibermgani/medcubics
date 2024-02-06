<style>
    table{
        width:100%;
        font-size:13px; font-family:'Open Sans', sans-serif !important;
        color:#000;
    }
    td{font-size: 8px;}
    th {
        text-align:left;
        font-size:9px;
        font-weight: 100 !important;
        border-radius: 0px !important;
    }
      th, th span{line-height: 20px;}
    tr, tr span{line-height: 10px;}
    @page { margin: 110px -20px 100px 0px; }
    body { 
        margin:0;                 
        font-size:13px; font-family:'Open Sans', sans-serif;
        color: #646464;
    }            
    .margin-t-m-10{margin-top: -10px;z-index: 999999;}
    .bg-white{background: #fff;}
    .med-orange{color:#f07d08} 
    .med-green{color: #00877f;}
    .margin-l-10{margin-left: 10px;} 
    .font13{font-size: 13px} 
    .font600{font-weight:600;}
    .padding-0-4{padding: 0px 4px;}
    .text-right{text-align: right;}
    .text-center{text-align: center;}
    .h3{font-size:12px; color: #00877f; margin-bottom: 10px;}
    .pagenum:before { content: counter(page); }
    .header {position: fixed; top:-112px;}
    .footer {bottom: -50px; position: fixed;}
    .last-section{}
</style>
<body>
    <div class="header" style="" >
        <table style="">
            <tr style="">
                <td colspan=""><h4 class="text-center" style="margin-bottom:-10px" > {!! @$basic_info['payee']['practice_company'] !!}</h4> </td>
            </tr>
        </table>
        <div>
            <ul style="text-align:center;list-style-type: none;margin-left:-40px;font-size:10px;line-height: 16px;">
                <li>{!! @$basic_info['payee']['practice_address_info1'] !!}, {!! @$basic_info['payee']['practice_city'] !!}, {!! @$basic_info['payee']['practice_state'] !!} {!! @$basic_info['payee']['practice_zipcode'] !!}</li>
                <li>NPI : {!! @$basic_info['payee']['payee_npi_id'] !!}</li>                
            </ul>
        </div>
        
       
        <table style="width:96.5%;margin-left: 5px;float: left; border-bottom: 1px dashed #ccc; color:#646464; padding-bottom: 5px;margin-top: -10px;">
            <tr>
                
                <td style="line-height:14px;">
                    <div class="" style="">Check Date : {!! date("m/d/Y",strtotime(@$basic_info['check_details']['check_date'])) !!}</div>
                    <div class="">Check/EFT Number : {!! @$basic_info['check_details']['check_no'] !!}</div>                                         
                    <div>Check Amount : ${!! App\Http\Helpers\Helpers::priceFormat(@$basic_info['check_details']['check_paid_amount']) !!}</div>
                </td>
                
                <td style="line-height:14px;text-align: right">
                    <div class="" style="">{!! @$basic_info['payer']['insurance_company'] !!}</div>
                    <div class="">{!! @$basic_info['payer']['insurance_address_info1'] !!}, {!! @$basic_info['payer']['insurance_city'] !!}, {!! @$basic_info['payer']['insurance_state'] !!}  {!! @$basic_info['payer']['insurance_zipcode'] !!}</div>
                                       
                </td>
                
            </tr>
        </table>
        
        
    </div>
    <div class="" style="padding-left: 10px;margin-top:30px;display:none">
        <table class="" style="width:97%;padding-left: 30px;">
            <tr>
                <td>File Name : {{ @$filename }}</td>
            </tr>
        </table>
    </div>
    <div class="footer med-green" style="padding-left:10px;">
        <table style=" ">
            <tr style="">
                <td colspan="6" style=""><span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></td>
                <td colspan="6" class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
            </tr>
        </table>
    </div>
    <?php 
		$claimSno = $total_billed = $deductible = $conIns = $serviceLevel = $claimLevel = $totalAdj = $totalAllowed = $providerPaid = $providerNetPaid = 0.00;  
	?>
	@if(isset($basic_info['claim']))
		@foreach($basic_info['claim'] as $list)
		<div class="" style="width:95.5%;border-bottom: 1px solid #ccc; padding-bottom: 10px;page-break-after: auto; page-break-inside: avoid;margin-left: 10px; margin-top:10px;">
			<?php $claimSno++;  ?>
			<table style="border-spacing: 0px; padding-left: 0px; ">
				<tr>
					<td><span style="font-weight:600;">{{ @$list['patient_lastname'] }}, {{ @$list['patient_firstname'] }}</span></td>
					<td>ID : {{ @$list['patient_hic'] }}</td>
					<td>Acnt : {{ isset($list['claim_id']) ? $list['claim_id'] : 0 }}</td>
					<td>{{ @$list['claim_insurance_type'] }}</td>
					<td>ICN : {{ @$list['patient_icn'] }}</td>
				</tr>
			</table>
			<table style="border-spacing: 0px;width:98%;margin-top:-5px;">
				<thead >
					<tr>
						<th style="padding-top: 10px;">Service Date</th>
						<th style="padding-top: 10px;">POS</th>
						<th style="padding-top: 10px;">Units</th>                    
						<th style="padding-top: 10px;">Proc</th>
						<th style="padding-top: 10px;">Modifiers</th>
						<th style="padding-top: 10px;" class="text-right">Billed</th>
						<th style="padding-top: 10px;" class="text-right">Allowed</th>
						<th style="padding-top: 10px;" class="text-right">Deduct</th>
						<th style="padding-top: 10px;" class="text-right">CoIns</th>					
						<th style="padding-top: 10px;" class="text-left"><span style="margin-left:10px;">Grp/RC</span></th> 
						<th style="padding-top: 10px;" class="text-right">Amount</th>
						<th style="padding-top: 10px;" class="text-right">Prov Paid</th>
						<th style="padding-top: 10px;" class="text-center">Claim Received</th>
					</tr>
				</thead>
				<tbody>
					<!-- Added new segment codes for provider level adjustment in era pdf generation issues fixed -->
					<!-- Revision 1 : Ref :  MR-2759 : 28 Aug 2019 : selva -->
					<?php $total_allowed =  $adj_total = 0;  ?>
					<?php $deductible = $conIns = $co_payment = 0.00; ?>
					@if(isset($list['cpt_details']))
					@foreach($list['cpt_details'] as $cpt_list)
					<?php $deductible = $conIns = $co_payment = 0.00; ?>
					<tr>
						@if((isset($cpt_list['start_date']) && !empty($cpt_list['start_date'])) && (isset($cpt_list['end_date']) && !empty($cpt_list['end_date'])))
						<td> {!! @$cpt_list['start_date'] !!} {!! @$cpt_list['end_date'] !!}</td>
						@else
						<td>
							@if(isset($cpt_list['service_date']) && !empty($cpt_list['service_date']))
							{!! $cpt_list['service_date'] !!} 
							@endif
						</td>
						@endif
						<td>
							@if(isset($cpt_list['pos']) && !empty($cpt_list['pos'])) 
							{!! @$cpt_list['pos'] !!} 
							@endif
						</td>
						<td>{!! @$cpt_list['units'] !!}</td>
						<td>
							@if(isset($cpt_list['proc']) && !empty($cpt_list['proc'])) 
							{!! @$cpt_list['proc'] !!} 
							@endif
						</td>
						<td></td>
						<td class="text-right">
							@if(isset($cpt_list['billed_amount']) && !empty($cpt_list['billed_amount'])) 
							<?php $total_billed = $total_billed + $cpt_list['billed_amount'];  ?>
							{!! App\Http\Helpers\Helpers::priceFormat($cpt_list['billed_amount']) !!} 
							@endif
						</td>                            
						<td class="text-right">
							<?php
								$total_allowed = $total_allowed + @$cpt_list['allowed'];
								$totalAllowed = $totalAllowed + @$cpt_list['allowed']; 
							?>
							{!! App\Http\Helpers\Helpers::priceFormat(@$cpt_list['allowed']) !!}
						</td>

						<td class="text-right">
							@if(isset($cpt_list['deductible']) && !empty($cpt_list['deductible'])) 
							<?php $deductible = $deductible + $cpt_list['deductible']; ?>
							{!! App\Http\Helpers\Helpers::priceFormat($cpt_list['deductible']) !!} 
							@else
							0.00
							@endif</td>

						<td class="text-right" >
							@if(isset($cpt_list['coinsurance']) && !empty($cpt_list['coinsurance'])) 
							<?php $conIns = $conIns + $cpt_list['coinsurance']; ?> 
							{!! App\Http\Helpers\Helpers::priceFormat($cpt_list['coinsurance']) !!} 
							@else
							0.00
							@endif
						</td>
						@if(isset($cpt_list['co_payment']) && !empty($cpt_list['co_payment'])) 
						<?php $co_payment = $conIns + $cpt_list['co_payment']; ?>  
						@endif

						<td></td>
						<td class="text-right"></td>
						<td class="text-right">
							@if(isset($cpt_list['insurance_paid_amount']) && !empty($cpt_list['insurance_paid_amount']))
							<?php $providerNetPaid = $providerPaid = $providerPaid + @$cpt_list['insurance_paid_amount'];  ?> 
							{!! App\Http\Helpers\Helpers::priceFormat(@$cpt_list['insurance_paid_amount']) !!}
							@else
								0.00
							@endif
						</td>
						<td class="text-center">{{ @$fileReceivedDate }}</td>
					</tr>
					@if(isset($cpt_list['adj_reason']))
					@foreach($cpt_list['adj_reason'] as $key => $adj)
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
						<td><span style="margin-left:10px;">{{ $adj }}</span></td>
						@if(!empty($cpt_list['adj_reason_val'][$key]))
							<td class="text-right"> <?php  $adj_total = $adj_total + @$cpt_list['adj_reason_val'][$key]; ?> {!! App\Http\Helpers\Helpers::priceFormat(@$cpt_list['adj_reason_val'][$key]) !!}</td>
						@else
							<td class="text-right"></td>
						@endif
						<td></td>
						<td></td>
					</tr>
					@endforeach
					@endif
					@endforeach
					@endif
					<tr>
						<td>Pat Resp: <?php echo $deductible + $conIns + $co_payment; ?></td>
						<td></td>
						<td></td>
						<td></td>
						<td colspan="2" class="text-right" style="font-weight:600;"><span style="font-weight:600">Claim Totals</span> : {!! App\Http\Helpers\Helpers::priceFormat(@$list['charge_amount']) !!}</td>                            
						<td class="text-right" style="font-weight:600">{!! App\Http\Helpers\Helpers::priceFormat(@$total_allowed) !!}</td>
						<td class="text-right" style="font-weight:600">0.00</td>
						<td class="text-right" style="font-weight:600;">{!! App\Http\Helpers\Helpers::priceFormat(@$list['charge_coins_amount']) !!}</td>
						<td></td>
						<td class="text-right" style="font-weight:600">{!! App\Http\Helpers\Helpers::priceFormat(@$adj_total) !!}</td>
						<td class="text-right" style="font-weight:600">{!! App\Http\Helpers\Helpers::priceFormat(@$list['charge_paid_amount']) !!}</td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php $totalAdj = $claimLevel = $serviceLevel = $serviceLevel + $adj_total; ?>
		@endforeach
	@endif
    	
    
    @if($claimSno != 0)
		<div class="last-section">
		<div class="" style="width:94%;margin-left:10px;border: 1px solid #ccc;padding-left:10px; margin-top:20px;margin-bottom: 1px;page-break-after: auto; page-break-inside: avoid;">
			<h4 style="margin-top:10px;">Report Totals</h4>
			<table style="border-spacing: 0px;width:98%; padding-bottom: 10px;margin-top:-10px">
				<thead>
					<tr>
						<th>No of Claims</th>
						<th class="text-right">Billed</th>
						<th class="text-right">Deductible</th>
						<th class="text-right">CoIns</th>                    
						<!--<th class="text-center" style="">Service Level <br>
							Adj Amount</th>
						<th class="text-center" style="">Claim Level <br>
							Adj Amount</th>-->
						<th class="text-right" style="">Total Adj</th>
						<th class="text-right">Allowed</th>
						<th class="text-right">Provider Paid</th>
						<!--<th class="text-center">Provider Net Pd</th>-->
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{{ $claimSno }}</td>
						<td class="text-right">{!! "$".App\Http\Helpers\Helpers::priceFormat($total_billed) !!}</td>
						<td class="text-right">{!! "$".App\Http\Helpers\Helpers::priceFormat($deductible) !!}</td>
						<td class="text-right">{!! "$".App\Http\Helpers\Helpers::priceFormat($conIns) !!}</td>
						<!--<td class="text-right" style="padding-right:10px;">{!! "$".App\Http\Helpers\Helpers::priceFormat($serviceLevel) !!}</td>
						<td class="text-right" style="padding-right:10px;">{!! "$".App\Http\Helpers\Helpers::priceFormat($claimLevel) !!}</td>-->                            
						<td class="text-right">{!! "$".App\Http\Helpers\Helpers::priceFormat($totalAdj) !!}</td>
						<td class="text-right">{!! "$".App\Http\Helpers\Helpers::priceFormat($totalAllowed) !!}</td>
						<td class="text-right">{!! "$".App\Http\Helpers\Helpers::priceFormat($providerPaid) !!}</td>
						<!--<td class="text-right" style="padding-right:10px;">{!! "$".App\Http\Helpers\Helpers::priceFormat($providerNetPaid) !!}</td>-->
					</tr>
				</tbody>
			</table>
					  
		</div>
		
		@if(isset($basic_info['plb']))
			<div class="" style="width:95.5%;border-bottom: 1px solid #ccc; padding-bottom: 10px;page-break-after: auto; page-break-inside: avoid;margin-left: 10px; margin-top:10px;">
			PLB ADJ DETAILS:
			<table>
				<tr>
					<th>&nbsp;</th>
					<th>Reason</th>
					<th>FCN/Other Identifier</th>
					<th>Amount</th>
				</tr>
				@foreach($basic_info['plb'] as $plbdata)
					<tr>
						<td style="width:100px">&nbsp;</td>
						<td>{{$plbdata['reason']}}</td>
						<td>{{$plbdata['desc']}}</td>
						<td>{{$plbdata['amt']}}</td>
					</tr>
				@endforeach
			</table>
			</div>
		@endif
		<div class="" style="width:94%;margin-left:10px;margin-top: 5px; padding-left: 10px;padding-bottom: 10px;margin-right:10px;page-break-after: auto; page-break-inside: avoid;border: 1px solid #ccc;  ">
			 <h4>Provider Level Adjustments Total </h4> 
			<table style="border-spacing: 0px;width:97%;margin-top:-10px;">
				@foreach($glossary_details as $key=>$glossary_list)
				<tr style="">
					<td>{!! @$key !!}</td>
					<td style="text-align: justify;">{!! @$glossary_list !!}</td>
				</tr>
				@endforeach
			</table>
		</div>
		</div>
	@endif
</body>