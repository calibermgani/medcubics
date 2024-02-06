<table id="example1" class="table table-bordered table-striped js_no_change">
	<thead>
		<tr>
			<th>Modifiers</th>	
			<th>Name</th>
			<th>Description</th>
			<th>Status</th>  
		</tr>
	</thead>
	<tbody>
		@foreach($modifiers as $modifier)
			<?php $modifier->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($modifier->id,'encode'); ?>
			<tr @if($checkpermission->check_url_permission($type.'/{modifierlevel1}') == 1) data-url="{{ url($type.'/'.$modifier->id) }}" @endif class="js-table-click clsCursor">            
				<td><center>{{ $modifier->code }}</center></td>
				<td>{{ str_limit($modifier->name, 30, '..') }}</td>
				<td>{{ str_limit($modifier->description, 40, '..') }}</td>
				<td>{{ $modifier->status }}</td>         
			</tr>
		@endforeach
	</tbody>
</table>