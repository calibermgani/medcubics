<table id="js_user_activity_tbl" class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>User Type</th>
			<th>From</th>
			<th>Module</th>
			<th>Activity</th>
			<th>Activity On</th>
		</tr>
	</thead>
	<tbody>
		@foreach($useractivity as $user) 
		<?php 
			$useractivity = $activitytype = '';
			if($user->main_directory!='') {
				if($user->main_directory!='admin') {			
					$activitytype = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($user->main_directory,'encode');
				
				} else {
					$useractivity = 'admin';  
					$activitytype = 'admin';
				}
			}
		?>		
		<tr data-activity="{{ $activitytype }}" data-module="{{ $user->module }}" data-action="{{ $user->action }}" data-url="{{ $user->url }}" class="js-useractivity-click clsCursor">
			<?php $practice = is_numeric(ucfirst($user->main_directory))?'':ucfirst($user->main_directory);?>
			<td>{{ ucfirst($user->usertype) }}</td>
			<td>{{ !empty($user->practice->practice_name)?ucfirst($user->practice->practice_name):$practice }} </td>
			<td>{{ ucfirst($user->module) }}</td>
			<td><?php echo $user->user_activity_msg;?></td>
			<td>{{ @$user->activity_date }}</td>
		</tr>
		@endforeach
	</tbody>
</table>             