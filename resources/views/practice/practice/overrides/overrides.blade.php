@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Overrides</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('practice/'.$practice->id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @if(App\Models\Practiceoverride::checkHasOverrideOrNot() ==0)<li><a href="{{ url('overrides/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>@endif            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
            <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/practiceoverridesreports/'])
                      </li>
            
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
    @include ('practice/practice/practice-tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
			<div class="box box-info no-shadow">
				<div class="box-header with-border">
				   <i class="fa fa-bars font14"></i><h3 class="box-title">Overrides List</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div><!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Provider</th>
								<th>Tax ID</th>
								<th>NPI</th>
								<th>Provider ID</th>
								<th>ID Type</th>
							</tr>
						</thead>
						<tbody>
						@foreach($overrides as $override)
						<tr data-url="{{ url('overrides/'.$override->id) }}" class="js-table-click clsCursor">
							<td>@if($override->provider)@include ('layouts/provider_popup_msg', array('provider_det'=>$override->provider))@endif</td>
							<td>{{ $override->provider->etin_type_number }}</td>
							<td>{{ $override->provider->npi }}</td>
							<td>{{ $override->provider_id }}</td>
							<td>{{ $override->id_qualifier->id_qualifier_name }}</td>                  
						</tr>
						@endforeach
						</tbody>
					</table>
					</div>
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->
<!--End-->
@stop   