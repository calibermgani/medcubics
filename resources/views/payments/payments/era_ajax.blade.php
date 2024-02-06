@foreach($e_remittance as $list) 
<tr> 
	@if($list->claim_nos != '' && $list->claim_nos != 'null')
		<td><input type="checkbox" value="{!! $list->check_no !!}" data-claim-count='{!! $list->claim_nos !!}' id="{!! $list->id !!}" name="era_claim" class="js-era-post"><label for="{!! $list->id !!}" class="no-bottom">&nbsp;</label></td>
	@else
		<td><input type="checkbox" disabled value="{!! $list->check_no !!}" data-claim-count='{!! $list->claim_nos !!}' id="{!! $list->id !!}" name="era_claim" class="js-era-post"><label for="{!! $list->id !!}" class="no-bottom">&nbsp;</label></td>
	@endif
	@if($list->claim_nos != '' && $list->claim_nos != 'null')
		<?php $checkNo = trim($list->check_no); $checkNo = str_replace(' ','XXOOXX',$checkNo); ?>
		<td data-toggle="modal" data-tile="@if(!empty($list->insurance_details)){!! App\Http\Helpers\Helpers::getInsuranceName(@$list->insurance_details->id) !!} @endif" data-target="#auto_post_status" data-url = "{{url('autoPostStatus/'.$checkNo.'/'.$list->id)}}" class="js-show-patientsearch js-insurance-popup claimdetail">
			<a id="someelem{{hash('sha256',@$list->id)}}" class="someelem" data-id="{{hash('sha256',@$list->id)}}" href="javascript:void(0);">
				{!! App\Http\Helpers\Helpers::dateFormat($list->receive_date) !!}
			</a>
			@include ('layouts/era_hover')
		</td> 
	@else
		<td>
			<a id="someelem{{hash('sha256',@$list->id)}}" class="someelem" data-id="{{hash('sha256',@$list->id)}}" href="javascript:void(0);">
			{!! App\Http\Helpers\Helpers::dateFormat($list->receive_date) !!}
			</a>
			@include ('layouts/era_hover')
		</td>
	@endif
	<td>@if(!empty($list->insurance_details)){!! @$list->insurance_details->short_name !!} @else -Nil- @endif</td>
	<td>{!! $list->check_no !!}</td>
	<td>{!! App\Http\Helpers\Helpers::dateFormat($list->check_date) !!}</td>
	<td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($list->check_paid_amount) !!}	</td>
	<td class="text-right">  
		@if(isset($list->check_details->pmt_details) && isset($list->eft_details->pmt_details))
			@if($list->id == $list->check_details->pmt_details->pmt_mode_id)
				{!! App\Http\Helpers\Helpers::priceFormat($list->check_details->pmt_details->amt_used) !!} 
			@else
				{!! App\Http\Helpers\Helpers::priceFormat($list->eft_details->pmt_details->amt_used) !!}
			@endif
		@elseif(isset($list->check_details->pmt_details))  
			{!! App\Http\Helpers\Helpers::priceFormat($list->check_details->pmt_details->amt_used) !!} 
		@elseif(isset($list->eft_details->pmt_details))
			{!! App\Http\Helpers\Helpers::priceFormat($list->eft_details->pmt_details->amt_used) !!} 
		@else
			0.00
		@endif
	</td>
	
	<td class="text-right">
		@if(isset($list->check_details->pmt_details))  
			{!! App\Http\Helpers\Helpers::priceFormat($list->check_details->pmt_details->pmt_amt - $list->check_details->pmt_details->amt_used) !!} 
		@elseif(isset($list->eft_details->pmt_details))
			{!! App\Http\Helpers\Helpers::priceFormat($list->eft_details->pmt_details->pmt_amt - $list->eft_details->pmt_details->amt_used) !!} 
		@else
			{!! App\Http\Helpers\Helpers::priceFormat($list->check_paid_amount) !!}
		@endif
	</td>
	
	<?php $era_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$list->id,'encode'); ?>
	<td >
		&nbsp;&nbsp;<a target="_blank" href="{{url('eraPDFData/'.$era_id.'/'.$list->check_no.'/show')}}"><i class="fa fa-file-pdf-o"></i></a> 
		&nbsp;&nbsp;<a href="{{url('eraPDFData/'.$era_id.'/'.$list->check_no.'/download')}}"><i class="fa fa-download"></i></a> 
		<a target="_blank" class="hide" href="{{url('autoPostData/'.$era_id.'/'.$list->check_no)}}">Data</a>
		<?php $tempFileName = explode('.',$list->pdf_name); ?>
		@if(isset($tempFileName[1]) && $tempFileName[1] == '835')
			&nbsp;&nbsp;<a target="_blank"  href="{{url('eraRsponseFile/'.$era_id)}}"><i class="fa fa-file-text-o"></i></a>
		@endif
	</td>
</tr>
@endforeach