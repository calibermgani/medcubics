<a class="mm" style="color: rgb(134, 134, 134);" >{{ @$provider_det->provider_name }}</a> 
<div class="on-hover-content" style="display:none;">					
	<span class="med-orange sm-size"><i class="med-orange med-gender fa fa-user-plus margin-r-5 "></i>{{@$provider_det->provider_name}}</span> 
	<p style="margin-bottom: 0px;">
		{{@$provider_det->gender}}<br>
		<b>NPI:</b> {{@$provider_det->npi}}<br>
		<b>SSN:</b> {{@$provider_det->ssn}}<br>
		@if(isset($provider_det->provider_types_id))<b>Type:</b> {{ App\Models\Provider_type::get_provider_types_name($provider_det->provider_types_id) }}<br>@endif
		<b>Phone:</b> {{@$provider_det->phone}}<br>
		<b>Fax:</b> {{@$provider_det->fax	}}
	</p>
</div>