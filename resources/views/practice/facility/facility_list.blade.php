<div class="table-responsive col-lg-12 col-md-12 col-sm-12 col-xs-12">
<table id="example1" class="table table-bordered table-striped ">         
	<thead>
		<tr>                                     
			<th>Short Name</th>
			<th>Facility</th>               
			<th>Specialty</th>
			<th>POS</th>
			<th class="not-desktop">City</th>
			<th>State</th>
			<th>Phone</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		@foreach($facilitymodule as $facility)
		<?php $facility->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>

		<tr @if($checkpermission->check_url_permission('facility/{facility}') == 1) data-url="{{ url('facility/'.$facility->id) }}" @endif class="js-table-click clsCursor">
			<td> <span class="js-display-detail"> </span>
				@include('layouts.facilitypop', array('data' => @$facility, 'from' =>'facility'))	
				<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
					<a id="someelem{{hash('sha256',@$facility->id)}}" class="someelem" data-id="{{hash('sha256',@$facility->id)}}" href="javascript:void(0);"> {{$facility->short_name }}</a>
				@include ('layouts/facility_hover')
			</td>
			<td>{{ str_limit(@$facility->facility_name,25,'...') }}</td>
			<td>@if($facility->speciality_details){{ $facility->speciality_details->speciality }}@endif</td>
			<td>@if($facility->pos_details){{ $facility->pos_details->code }}@endif</td>        
			<td>@if($facility->facility_address){{ $facility->facility_address->city }}@endif</td> 
			<td>@if($facility->facility_address){{ $facility->facility_address->state }}@endif</td>            
			<td>@if($facility->facility_address){{ $facility->facility_address->phone }}@endif</td>  
			<td>{{ $facility->status }}</td>  
		</tr>
		@endforeach      
	</tbody>
</table>
</div>