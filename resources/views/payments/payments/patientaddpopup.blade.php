<div class="box box-view no-shadow no-border no-bottom">
    <span class = "js-length"></span>
            
	<div class = "js-append-mainpayment-table">
		<div class="box-body table-responsive  no-padding "><!-- Notes Box Body starts -->  
			<div class="col-lg-12 col-md-12 col-md-12 col-sm-12 col-xs-12 chat ar-notes js_payment no-padding">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive claim-transaction-scroll">
				
				@foreach($basic_info as $basic_list)
					@if($basic_list['check_details']['check_no'] == $cheque)
						@foreach($basic_list['claim'] as $list) 
						<?php $claim_cpt_count = 0; ?>
						<table class="popup-table-wo-border table table-responsive">                    
							<thead>
								<tr>   
									<th>Claim #</th>
									<th>Patient Name</th>                                
									<th>DOS</th>                               
									<th class="text-right">CPT</th>
									<th class="text-right">Billed </th>
									<th class="text-right">Allowed</th>
									<th class="text-right">PR Amt</th>
									<th class="text-right">Write Off</th>
									<th class="text-right">OA Amt</th>
								</tr>
							</thead>
							<tbody>
								 @foreach($list['cpt_details'] as $cpt_list) 
								 
								<tr> 
									@if($claim_cpt_count == 0)
										<!-- showing claim number -->
										<td>@if(!empty($list['claim_id'])) {!! $list['claim_id'] !!} @endif</td>
										<!-- showing Patient Name -->
										<td>@if(!empty($list['patient_lastname'])) {!! $list['patient_lastname'].",".$list['patient_firstname'] !!} @endif </td> 
									@else
										<!-- showing claim number -->
										<td>&nbsp;</td>
										<!-- showing Patient Name -->
										<td>&nbsp;</td> 
									@endif
									<!-- showing DOS -->
									<td>@if(!empty($cpt_list['service_date'])) {!! $cpt_list['service_date'] !!} @endif</td>
									<!-- showing CPT -->
									<td>@if(!empty($cpt_list['proc'])) {!! $cpt_list['proc'] !!} @endif</td>     
									<!-- showing Billed -->
									<td>@if(!empty($cpt_list['billed_amount'])) {!! $cpt_list['billed_amount'] !!} @else 0.00 @endif</td>
									<!-- showing Allowed -->
									<td>@if(!empty($cpt_list['allowed'])) {!! $cpt_list['allowed'] !!} @else 0.00 @endif</td>
									<!-- showing PR Amt -->
									<td>@if(!empty($cpt_list['type_coins_PR']))  {!! $cpt_list['type_PR'] !!} @else 0.00 @endif</td>
									<!-- showing Write Off -->
									<td>@if(!empty($cpt_list['type_coins_CO']))  {!! $cpt_list['type_CO'] !!} @else 0.00 @endif</td>
									<!-- showing OA Amt -->
									<td>@if(!empty($cpt_list['type_coins_OA']))  {!! $cpt_list['type_OA'] !!} @else 0.00 @endif</td>
									
								</tr>
								<?php $claim_cpt_count++; ?>
								@endforeach
							</tbody>
						</table>
						@endforeach
					@endif
				@endforeach
				</div> 
			</div>          		   
		</div><!-- Notes box-body Ends--> 
	</div>   
</div>
  
<!--Patient payment posting ends here -->
<script type="text/javascript">
    $(document).ready(function () {
        $('input[name="check_date"]').on('change', function () {
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'check_date');
        });
        $('input[name="cardexpiry_date"]').on('change', function () {
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'cardexpiry_date');
        });
        $('input[name="payment_amt_pop"]').on('change', function () {
            if ($('input[name="wallet_refund"]').length)
                $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'wallet_refund');
        });
        $('select[name="card_type"]').on('change', function () {
            $('form#js-bootstrap-validator').bootstrapValidator('revalidateField', 'card_type');
        });
    });
</script>