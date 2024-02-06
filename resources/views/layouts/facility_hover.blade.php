<div class="on-hover-content js-tooltip_{{hash('sha256',@$facility->id)}}" style="display:none;">
    <span class="med-orange font600">{{ @$facility->facility_name }}</span> 
	<?php
		$facility_address1 = (isset($facility->facility_address1)) ? $facility->facility_address1 : @$facility->facility_address->address1;
		$facility_city = (isset($facility->facility_city)) ? $facility->facility_city : @$facility->facility_address->city;
		$facility_state = (isset($facility->facility_state)) ? $facility->facility_state : @$facility->facility_address->state;
		$facility_pay_zip5 = (isset($facility->facility_pay_zip5)) ? $facility->facility_pay_zip5 : @$facility->facility_address->pay_zip5;
		$facility_pay_zip4 = (isset($facility->facility_pay_zip4)) ? $facility->facility_pay_zip4 : @$facility->facility_address->pay_zip4;		
	?>
    <p class="no-bottom hover-color">
		@if(isset($facility->speciality_details->speciality))
			<span class="font600">Specialty :</span> {{ @$facility->speciality_details->speciality }}<br>
		@endif
        <span class="font600">Address :</span> {{ @$facility_address1 }}<br>		
			{{ @$facility_city }} - {{ @$facility_state}}, {{ @$facility_pay_zip5 }} - {{ @$facility_pay_zip4 }}<br>
        @if( @$facility->county->id != 0)<span class="font600">County :</span> {{ @$facility->county->name }} <br>@endif
		@if(isset($facility->pos_details->code))
			<span class="font600">POS :</span> {{ @$facility->pos_details->code }}<br>
		@endif
        @if( @$facility->facility_npi)<span class="font600">NPI :</span> {{ @$facility->facility_npi }}@endif
    </p>
</div>