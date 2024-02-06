@if(!empty((array)$history))
	@foreach($history as $history)
		<?php
			$logout_time = trim($history->logout_time);
		?>
		<tr>
			<td>{{ $history->ip_address}}</td>
			<td>{{ $history->browser_name}}</td>
			<td>{{ App\Http\Helpers\Helpers::dateFormat(@$history->login_time,'time') }}</td>
			<td>
				@if(@$logout_time !='') 
					{{ App\Models\Profile\UserLoginHistory::LogoutTime(@$history->logout_time) }} 
				@else 
					Current User 
				@endif
			</td>
			<td>{{ @$history->short_name }}</td>	
			<td>{{ isset($history->user_type)?$history->user_type:'-Nil-'}}</td>
		</tr>
	@endforeach
@else
	<tr>
		<td colspan="6">No Records Found</td>
	</tr>
@endif		