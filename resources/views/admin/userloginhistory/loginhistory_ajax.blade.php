@if(!empty($userLoginInfo))
	@foreach($userLoginInfo as $list)
	<tr>
		<td>{{ @$list->user }}</td>
		<td>{{ @$list->email }}</td>
		<td>{{ @$list->customer }}</td>
		<td>{{ App\Http\Helpers\Helpers::getPracticeNames(@$list->admin_practice_id,@$list->userid) }}</td>
		
		<td>{{ $list->security_code }}</td>
		<td>{{ $list->ip_address }}</td>
		<?php $totalDays = App\Http\Helpers\Helpers::daysSinceCreated($list->created_at)  ?>
		@if($totalDays != 0 && $list->approved == 'No')
		<td>Expired</td>
		@else
		<td>{{ $list->approved }}</td>
		@endif
		<td class="attempt_code_{{ @$list->userid }}">{{ $list->security_code_attempt }}</td>
		<td>{{ date('m/d/y h:i:s',strtotime($list->created_at)) }}</td>
		@if($list->status == 'Inactive')
		<td><span style="cursor: pointer;" data-user-id="{{ @$list->userid }}" data-user-status="Active" class="med-green js-block" >Active</span>
		@else
			<td><span style="cursor: pointer;" data-user-id="{{ @$list->userid }}" data-user-status="Inactive" class="med-green js-block">Block</span>
			@endif
		| <span style="cursor: pointer;" data-userip-id="{{ $list->id }}" class="js-reset-code med-green" >Reset</span>
		</td>
	</tr>
	@endforeach
	
@endif