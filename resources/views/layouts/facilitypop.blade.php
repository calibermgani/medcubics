<div class="on-hover-content" style="display:none;">
	@if(isset($data->facility_name))
		<span class="med-orange sm-size"><i class="med-orange med-gender fa margin-r-5 "></i>{{ str_limit($data->facility_name,25,'...') }}</span> 
		</br>
		@if(isset($from) && $from ='facility')	
			<b>Address:</b> @if($data->facility_address){{ $data->facility_address->address1 }}@endif<br>
			<b>City:</b> @if($data->facility_address){{ $data->facility_address->city }}@endif<br>
			<b>Place of Service:</b> @if($data->pos_details){{ $data->pos_details->pos }}@endif<br>	
		@else
			<?php  $value = App\Models\Facility::getfacilitydetail($data->id) ?>
			<b>Address:</b> {{@$value->address}}<br>
			<b>City:</b> {{ @$value->city }}<br>
			<b>Place of Service:</b>{{@$value->pos}}<br>
		@endif
		<b>NPI:</b> @if($data->facility_npi){{ $data->facility_npi }}@endif<br>
	@endif
</div>