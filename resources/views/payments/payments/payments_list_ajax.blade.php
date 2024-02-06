@if(!empty($payment_details))
    <?php 
       // $insurances = App\Http\Helpers\Helpers::getInsuranceNameLists();
    ?>
    @foreach($payment_details as $payment_detail)
        <?php
            $type = @$payment_detail->pmt_type;
            if ($payment_detail->pmt_method == "Patient") {
                $insurance = $payment_detail->pmt_method;
            } else {
                if($payment_detail->insurance_name!='') {
                    //$insurance = !empty($insurances[@$payment_detail->insurancedetail->id]) ? $insurances[@$payment_detail->insurancedetail->id] : App\Http\Helpers\Helpers::getInsuranceName(@$payment_detail->insurancedetail->id);
                    $insurance = !empty($payment_detail->insurance_name) ? $payment_detail->insurance_name : "";
                } else {
                    $insurance = "-Nil-";
                }
            }
            $check_mode = $payment_detail->pmt_mode;
            $check_date = '';
            if ($check_mode == "Check" && $payment_detail->check_no!='') {
                $check_no = $payment_detail->check_no;
                $check_date = $payment_detail->check_date;
            } elseif ($check_mode == "EFT" && $payment_detail->eft_no!='') {
                $check_no = $payment_detail->eft_no;
                $check_date = $payment_detail->eft_date;
            } elseif ($check_mode == "Cash") {
                $check_no = "-Nil-";
            } elseif($check_mode == "Credit" && $payment_detail->card_last_4!=''){
                $check_no = isset($payment_detail->card_last_4) ? @$payment_detail->card_last_4 : '';
				$check_date = isset($payment_detail->expiry_date) ? @$payment_detail->expiry_date : '';
            } elseif($check_mode == "Money Order"){
                $check_no = isset($payment_detail->check_no) ? str_replace("MO-", "",@$payment_detail->check_no) : '';
                $check_date = isset($payment_detail->check_date) ?$payment_detail->check_date : '';
            } else {
                $check_no = '-Nil-';//$payment_detail->card_no;
            }
            if ($payment_detail->pmt_type == "Refund") {
                $check_no = $check_no." - Refund";                
            }
            $check_date = (!empty($check_date) && $check_date != '1970-01-01' && $check_date != '0000-00-00') ? App\Http\Helpers\Helpers::dateFormat($check_date) : "-Nil-";
            $payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_detail->pmt_id, 'encode');
            $bal_amt = @$payment_detail->pmt_amt - @$payment_detail->amt_used;
        ?>
        <tr data-id= "js_pmt_{{$payment_detail->pmt_no}}" data-toggle="modal" data-payment-info-number = "{{$payment_detail->pmt_no}}" data-url = "{{url('payments/getpaymentdata/'.$payment_detail_id)}}" class = "js-modalboxopen" data-target-new="#payment_detail">    
			<td><a href = "#" >{{$payment_detail->pmt_no}}</a></td>
            <td>{!!$insurance!!}</td>
            <td>{{$check_no}}</td>                       
            <td>{{$check_mode}}</td>
            <td class="js_check_date">{{$check_date}}</td>
            <td class="text-right js_check_pmtamt @if($payment_detail->pmt_type == 'Refund')  med-red @endif">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_detail->pmt_amt)!!}</td>
            <td class="text-right js_check_used @if($payment_detail->pmt_type == 'Refund')  med-red @endif">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_detail->amt_used)!!}</td>
            <td class="text-right js_check_bal js-prevent-redirect @if($payment_detail->pmt_type == 'Refund')  med-red @endif">{!!App\Http\Helpers\Helpers::priceFormat(@$bal_amt)!!}
                <div class="font600 tooltip-content" href="#" >
                    <i class="fa fa-sticky-note-o @if(@$payment_detail->pmt_notes->notes != '') med-orange @else med-darkgray @endif fa-5x cursor-pointer bill-lblue" style="font-size: 12px; margin-top: 1px;">
                    <div class="tooltiptext modal-content" style="position: absolute;z-index: 9999;">
                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5">
                                <textarea name="unposted_notes" class="unposted_notes_{{ $payment_detail->id }} form-control" style="width:100%;" placeholder='Type Your Notes'>{{ @$payment_detail->pmt_notes->notes }}</textarea>
                                <i class="fa " id="unposted_icon_status_{{ $payment_detail->id }}"> </i>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-5">
                                <input type="button" value="Save" data-pmt-id="{{ $payment_detail->id }}" class="unposted_notes_save btn btn-medcubics-small margin-t-m-5 pull-right"/>
                            </div>
                        </div>
                    </div>
                    </i>
                </div>
            </td>
            <?php /*
            <td>{{App\Http\Helpers\Helpers::dateFormat($payment_detail->created_at)}}</td>
            */ ?>
            <td>{{ App\Http\Helpers\Helpers::timezone($payment_detail->created_date, 'm/d/y') }}</td>
            <td>{{ @$payment_detail->user_name }}</td>
            <td>E</td>							
        </tr>
    @endforeach    
@else
    <tr>
        <td colspan="11"> No Records Found.</td>
    </tr>
@endif