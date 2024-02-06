@if(!empty($question))
	<table id="example1" class="table table-bordered table-striped js_no_change">
		<thead>
			<tr>
				<th>Question</th>	
				<th>Claim Status</th>
				<th>Field Type</th>
				<th>Field Validation Type</th>
				<th>Created Date</th>  
			</tr>
		</thead>
		<tbody>
		@foreach($question as $list)	
			<tr class="js-table-click clsCursor" data-url="{{ url('followup/view/question/') }}/{{ $list->id }}">            
				<td>{{ ucfirst($list->question) }}</td>
				<td>{{ ucfirst($list->category->name) }}</td>
				<td>{{ ucfirst($list->field_type) }}</td>
				<td>@if(empty($list->field_validation)) {{ str_replace('_',' ',ucfirst($list->date_type)) }} @else {{ ucfirst($list->field_validation) }} @endif </td>
				<td>{{ date('m/d/y',strtotime($list->created_at)) }}</td>
			</tr>
		@endforeach
		</tbody>
	</table>
@else
	No Question Found..
@endif