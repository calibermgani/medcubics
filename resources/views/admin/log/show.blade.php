@if(!empty($file_content))
	@foreach($file_content as $list)
		<p>{!! $list !!}</p>
	@endforeach
@else
	<p>No contents found to show</p>
@endif