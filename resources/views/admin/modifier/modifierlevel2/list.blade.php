@extends('admin')

@section('toolbar')
	<div class="row-fluid">
		<div class="span12 navbar-fixed-top2 search-top">                  
			<div class="widget-title1 toolbar-band">                                            
				 <div class="span8">
					<h3 class="toolbar-heading"><span><i class="fa {{Config::get('cssconfigs.common.modifiers')}}  font14"></i> Modifier List</span> </h3>
				</div>     
			   
				<div class="span4 icon-space">
					<div class="fon">
					<li><a href="#" onclick="history.go(-1);return false;"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
						<span><a href="{{ url('modifier/create') }}"><i class="icon-plus-sign right1 tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Add"></i></a></span>                       
						<span><a href=""><i class="icon-print right1 tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></span>
						<span><a href=""><i class="icon-share right1 tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Export"></i></a></span>
						<span><a href="{{url('help/modifiers')}}"><i class="icon-question-sign right1 tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Help"></i></a></span>
					</div>
				</div>
			</div>
		</div>
	</div>              
@stop

@section('practice')
	<div class="row-fluid">
	  <div class="table-responsive">
		 <table class="table table-striped table-bordered space " id="mcsorting">
			<thead>
				<tr>
					<th>Code</th>	
					<th>Name</th>
					<th>Anesthesia Base Unit</th>
					<th>Status</th>           
					<th colspan="1"></th>
				</tr>
			</thead>
			<tbody>
				@foreach($modifiers as $modifier)
				<tr>
					@php $modifier->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($modifier->id,'encode'); @endphp
					<td>{{ $modifier->code }}</td>
					<td>{{ $modifier->name }}</td>
					<td>{{ $modifier->AnesthesiaBaseUnitValue }}</td>
					<td>{{ $modifier->activeinActive }}</td>                
					<td><a href="modifier/{{ $modifier->id }}/edit"><input type="button" class="btn btn-warning" value="Edit"/></a></td>
				</tr>
				@endforeach
			</tbody>
		</table>
	  </div>
	</div>
@stop