<table id="example1" class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Module</th>
			<th>Activity</th>
			<th>Activity On</th>
		</tr>
	</thead>
	<tbody>
	@if(count($useractivity)>0)
		@foreach($useractivity as $user) 
			<tr data-activity="practice" data-module="{{ $user->module }}" data-action="{{ $user->action }}" data-url="{{ $user->url }}" class="js-useractivity-click clsCursor">
				<td>{{ ucfirst($user->module) }}</td>
                <td class="td-c-60"><?php echo $user->user_activity_msg; ?></td>
				<td>{{ App\Http\Helpers\Helpers::dateFormat($user->activity_date,'date') }}</td>
			</tr>
		@endforeach
	@endif
	</tbody>
</table>