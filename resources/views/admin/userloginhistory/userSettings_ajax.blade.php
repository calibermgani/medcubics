@if(!empty($usersettingInfo))
	@foreach($usersettingInfo as $list)
	<tr class="remove-row_{{ @$list->id }}">
		<td class="text-center" >{{ @$list->practice_user_type }}</td>
		<td class="text-center" >{{ isset($list->customer->customer_name) ? $list->customer->customer_name : '' }}</td>
		<td class="text-center" >{{ isset($list->admin_practice_id->practice_name) ? @$list->admin_practice_id->practice_name : "Practice Not assigned" }}</td>
		<td class="text-center" >{{ @$list->name }}</td>		
		<td class="text-center" ><span style="cursor: pointer;" data-user-id="{{ @$list->id }}" data-user-status="Active" class=" removeApproval med-green js-block" >Remove Approval</span> </td>
	</tr>
	@endforeach
@else
	<tr>
		<td colspan="10">
			No records found
		</td>
	</tr>
@endif