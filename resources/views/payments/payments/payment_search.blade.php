<!-- Main payment posting search append process starts here-->
<table id="search_table_payment" class="table table-bordered table-striped">   
    <thead>
        <tr>           
            <th>Payment ID</th>                                
            <th>Payer</th>
            <th>Check/EFT/CC No</th>
            <th>Mode</th>
            <th>Check Date</th>
            <th>Check Amt</th>
            <th>Posted</th>
            <th>Un Posted</th>
            <th>Created On</th>
			<th>User</th>
            <th></th>                              
        </tr>
    </thead>
	<tbody> 
	<?php
		//$insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();         
	?>
	@foreach($payment_details as $payment_detail)
		<?php  
		$type = $payment_detail->pmt_type;
		if ($payment_detail->pmt_method == "Patient") {
			$insurance = $payment_detail->pmt_method;
		} else {
			if(isset($payment_detail->insurancedetail)) {
				$insurance = !empty($payment_detail->insurancedetail->short_name) ? $payment_detail->insurancedetail->short_name : "";
				//$insurance = !empty($insurances[@$payment_detail->insurancedetail->id]) ? $insurances[@$payment_detail->insurancedetail->id] : App\Http\Helpers\Helpers::getInsuranceName(@$payment_detail->insurancedetail->id);
			} else {
				$insurance = "";    
			}
		}
		$check_mode = $payment_detail->pmt_mode;
		$check_date = '';
		if ($check_mode == "Check" ) {
			$check_no = isset($payment_detail->check_details) ? @$payment_detail->check_details->check_no : '';
			$check_date = isset($payment_detail->check_details) ? @$payment_detail->check_details->check_date : '';
		} elseif ($check_mode == "EFT" && isset($payment_detail->eft_details)) {
			$check_no = $payment_detail->eft_details->eft_no;
			$check_date = $payment_detail->eft_details->eft_date;
		} elseif ($check_mode == "Cash") {
			$check_no = "-";
		} elseif($check_mode == "Credit"){
			$check_no = isset($payment_detail->credit_card_details->card_last_4) ? @$payment_detail->credit_card_details->card_last_4 : '';
		} else {				
			$check_no = '';//$payment_detail->card_no;                
		}
		$check_date = (!empty($check_date) && $check_date != '1970-01-01' && $check_date != '0000-00-00') ? App\Http\Helpers\Helpers::dateFormat($check_date) : "-";
		$payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_detail->id, 'encode');
		$bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;
		?>
		 <tr>                        
			<td><a data-toggle="modal" data-payment-info = "{{$payment_detail->pmt_no}}" data-url = "{{url('payments/getpaymentdata/'.$payment_detail_id)}}" 
				class = "js-modalboxopen" data-target="#payment_detail">{{$payment_detail->pmt_no}}</a></td>                                
			<td><a href = "#" data-toggle="modal" data-payment-info = "{{$payment_detail->pmt_no}}" data-url = "{{url('payments/editcheck/'.$payment_detail_id)}}" class = "js-modalboxopen" data-target="#payment_detail">{!!$insurance!!}</a></td>
			<td>{{$check_no}}</td>                       
			<td>{{$check_mode}}</td>
			<td>{{$check_date}}</td>
			<td>{{$payment_detail->pmt_amt}}</td>
			<td>{{$payment_detail->amt_used}}</td>
			<td>{!!App\Http\Helpers\Helpers::priceFormat(@$bal_amt)!!}</td>
			<td>{{App\Http\Helpers\Helpers::dateFormat($payment_detail->created_at)}}</td>
			<td>{{@$payment_detail->created_user->short_name}}</td>
			<td>E</td>                          
		</tr>
	@endforeach                                                      
	</tbody>
</table>    
<!-- Main payment posting search append process ends here-->