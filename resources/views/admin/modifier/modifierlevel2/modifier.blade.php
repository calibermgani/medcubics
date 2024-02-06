@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.modifiers')}}  font14"></i> Modifiers</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/modifierlevel1') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			   
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if(count($modifiers)>0)
					<li class="dropdown messages-menu"><a href ="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/modifierlevelreports/'])
					</li>
				@endif	

				<li><a href ="" data-target="#js-help-modal" data-url="{{url('help/modifiers')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/modifier/tabs')
@stop

@section('practice')

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!-- Col Starts -->
		<div class="box box-info no-shadow"><!-- Box Starts -->
			<div class="box-header margin-b-10">
				<i class="fa fa-bars"></i><h3 class="box-title">Modifier Level II List</h3>
				<div class="box-tools pull-right margin-t-2">
					 @if($checkpermission->check_adminurl_permission('admin/modifierlevel2/create') == 1)
						<a href="{{ url('admin/modifierlevel2/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Modifier</a>
					 @endif	
				</div>
			</div><!-- /.box-header -->
			<div class="box-body"><!-- Box Body Starts -->
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Code</th>	
								<th>Name</th>
								<th>Description</th> 
								<th>Status</th>  
							</tr>
						</thead>
						<tbody>
							@foreach($modifiers as $modifier)
								@php $modifier->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($modifier->id,'encode'); @endphp
								<tr data-url="{{ url('admin/modifierlevel2/'.$modifier->id) }}" class="js-table-click clsCursor">            
									<td><center>{{ $modifier->code }}</center></td>
									<td>{{ str_limit($modifier->name, 30, '...') }}</td>
									<td>{{ str_limit($modifier->description, 40, '...') }}</td>
									<td>{{ $modifier->status }}</td>         
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div><!-- /.box-body ends -->
		</div><!-- /.box ends -->
	</div><!-- Col Ends -->
	<!--End-->
@stop   