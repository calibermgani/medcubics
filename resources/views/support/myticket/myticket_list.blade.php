<div class="table-responsive">
	<table id="search_table" class="table table-bordered table-striped table-separate">
		<thead>
			<tr>
				<th>Ticket ID</th>
				<th>Email</th>
				<th>Title</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			@foreach($ticket as $manageticket)
			<?php 
				$manageticket->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($manageticket->id,'encode'); 
			?>
			<tr data-url="{{ url('myticket/'.$manageticket->id) }}" class="js-table-click clsCursor form-cursor">
				<td>{{ $manageticket->ticket_id }}</td>
				<td>{{ str_limit($manageticket->email_id, 50, '..') }}</td>
				<td>{{ str_limit($manageticket->title) }}</td>
				<td>{{ $manageticket->status }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>