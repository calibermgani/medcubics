<div class="table-responsive mobile-scroll">
	<table id="example1" class="table table-bordered table-striped mobile-width">
		<thead>
			<tr>                        
				<th>Short Name</th>
				<th>Facility</th>
				<th>Specialty</th>
				<th>POS</th>
				<th>City</th>
				<th>State</th>
				<th>Scheduled</th>
			</tr>
		</thead>
		<tbody>
			@if(count($facilities) > 0)
				@foreach($facilities as $facility)
				<?php $facility->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>
				<tr data-url="{{ url('facilityscheduler/facility/'.$facility->id) }}" class="js-table-click clsCursor" style="cursor: pointer">
					<td>
						 <span class="js-display-detail"> </span>
						@include('layouts.facilitypop', array('data' => @$facility, 'from' =>'facility'))	
						<div style="padding-bottom: 0px; padding-left: 0px;">
							<a id="someelem{{hash('sha256',@$facility->id)}}" class="someelem" data-id="{{hash('sha256',@$facility->id)}}" href="javascript:void(0);"> {{ @$facility->short_name }}</a>
							@include ('layouts/facility_hover')
						</div>	
					</td>
					<td>{{ str_limit(@$facility->facility_name,25,'...') }}</td>
					<td>{{ @$facility->speciality_details->speciality }}</td>    
					<td>{{ @$facility->pos_details->code }}</td>        
					<td>{{ @$facility->facility_address->city }}</td> 
					<td>{{ @$facility->facility_address->state }}</td>            
					<td>
					<?php $scheduled_count = App\Models\ProviderScheduler::getScheduledCountByProviderId($facility->id,'Facility'); ?>
					@if($scheduled_count > 0) Yes @else No @endif
					</td>            
				</tr>
				@endforeach
			@endif	
		</tbody>

	</table>
</div><!-- /.box-body -->