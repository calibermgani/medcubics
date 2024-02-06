@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Api Accounts Status </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="#js-help-modal" data-url="{{url('help/apisettings')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
<!-- Toolbar row ends -->
@stop

@section('practice-info')
 <table id="" class="table table-bordered table-striped">
		<thead>   
			<tr>
				<th>Name</th>
				<th>Status</th>
				<th>Message</th>
				<th>URL</th>
			</tr>
		</thead>
		<tr>
			<td>Clearing House</td>
			@if(@$clearing_house['status'] === "success")
				<td>{!! @$clearing_house['message'] !!}</td> 
				<td>{!! @$clearing_house['status'] !!}</td> 
				<td>{!! @$clearing_house['url'] !!}</td> 
			@else
				<<td>{!! @$clearing_house['message'] !!}</td> 
			 	<td>{!! @$clearing_house['status'] !!}</td> 
				<td>{!! @$clearing_house['url'] !!}</td> 
			@endif
		</tr>
		@foreach($practiceApiList as $api_value) 
		  	<tr>			  
			  @if($api_value->status === "Active")
			  <td> {!! ucwords(str_replace(@$apilist_arr[$api_value->api_id]['api_name'],' ',str_replace('_',' ',@$apilist_arr[$api_value->api_id]['category']))) !!} </td>
				 <!--<td>{!! $api_value->api_id !!}</td> -->
				 @if($api_value->api_id == 1)					
					 @if($usps['status'] === "success")
						<td>{!! $usps['message'] !!}</td> 
						<td>{!! $usps['status'] !!}</td> 
						<td>{!! $usps['url'] !!}</td> 
					 @else
						<<td>{!! $usps['message'] !!}</td> 
						 <td>{!! $usps['status'] !!}</td> 
						<td>{!! $usps['url'] !!}</td> 
				   @endif
				 @endif
				 @if($api_value->api_id == 2)
					
					 @if($npi_data['status'] === "success")
						<td>{!! $npi_data['message'] !!}</td> 
						<td>{!! $npi_data['status'] !!}</td> 
						<td>{!! $npi_data['url'] !!}</td> 
					 @else
						<<td>{!! $npi_data['message'] !!}</td> 
						 <td>{!! $npi_data['status'] !!}</td> 
						<td>{!! $npi_data['url'] !!}</td> 
				   @endif
				 @endif
				 
				@if($api_value->api_id == 9  || $api_value->api_id == 10 )
					@if($twilioCC['error'] === "")
						<td>{!! $twilioCC['message'] !!}</td> 
						<td>{!! $twilioCC['status'] !!}</td> 
						<td>{!! $twilioCC['url'] !!}</td> 
					@else
						<td>{!! $twilioCC['error'] !!}}</td> 
						<td>{!! $twilioCC['status'] !!}</td> 
						<td>{!! $twilioCC['url'] !!}</td> 
					@endif
				
				@endif
				@if($api_value->api_id == 5)
					 <td>-</td> 
					 <td>-</td> 
					 <td>-</td> 
				@endif
				
			   @endif	
		  	</tr>
		@endforeach	
	 
		<tr>
			<td>IP Finder:</td>
			@if($ipfinderUsage['error'] === "")
				<td>{!! $ipfinderUsage['message'] !!}</td> 
				<td>{!! $ipfinderUsage['status']!!}</td> 
				<td>{!! $ipfinderUsage['url'] !!}</td> 
			@else
				<td>{!! $ipfinderUsage['error'] !!}}</td> 
				<td>{!! $ipfinderUsage['status'] !!}</td> 
				<td>{!! $ipfinderUsage['url'] !!}</td> 
			@endif
		</tr>
		
	</table>
@stop