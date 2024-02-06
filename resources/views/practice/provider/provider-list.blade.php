<table id="example1" class="table table-bordered table-striped js_no_change">
	<thead>
		<tr>                            
			<th>Short Name</th>
			<th>Provider Name</th>
			<th>Type</th>
			<th>ETIN Type</th>
			<th>Tax ID/SSN</th>
			<th>NPI</th>
			<th>Specialty</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		@foreach($providers as $provider)
			<?php $provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode'); ?>
			<tr @if($checkpermission->check_url_permission('provider/{provider}') == 1)data-url="{{ url('provider/'.$provider_id) }}" @endif class="js-table-click clsCursor">
				<td>
					<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
						<a id="someelem{{hash('sha256',@$provider->id)}}" class="someelem" data-id="{{hash('sha256',@$provider->id)}}" href="javascript:void(0);"> {{ @$provider->short_name }}</a> 
						@include ('layouts/provider_hover')
					</div>   
				</td>
				<td>{{ str_limit($provider->provider_name." ".@$provider->degrees->degree_name,25,'...') }}</td>
				<td><span class="" href="javascript:void(0);" data-toggle="tooltip" data-fetchid="{{$provider->id}}">{{ !empty($provider->provider_types->name)?$provider->provider_types->name:@$provider->provider_type_details->name }}</span></td>
				<td>{{ $provider->etin_type }}</td>
				<td>{{ $provider->etin_type_number }}</td>
				<td>{{ $provider->npi }}</td>
				<td>@if($provider->speciality != ''){{ $provider->speciality->speciality }}@endif</td>
				<td>{{ $provider->status }}</td>               
			</tr>
		@endforeach
	</tbody>
</table>