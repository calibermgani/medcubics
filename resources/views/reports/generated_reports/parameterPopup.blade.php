<!-- Modal content-->
<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Parameters</h4>
	</div>
	<div class="modal-body">
		@if(isset($parameterInfo) && !empty($parameterInfo))
			<table class="table table-borderless table-striped-view no-bottom">	
			@foreach($parameterInfo as $key => $value)
				@if(isset($key) && isset($value))
					<tr>
						<td>{{ ucwords($key) }}</td>
						<td>: {{ ucwords($value) }}</td>
					</tr>                                
				@endif
			@endforeach
            </table>
		@else
			No Parameter Found
		@endif
	</div>
	<div class="modal-footer p-b-10">
		<button type="button" class="btn btn-medcubics" data-dismiss="modal">Close</button>
	</div>
</div>