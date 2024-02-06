@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> {{ucfirst($selected_tab)}} </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			@if(count($facilitymodule)>0)
				<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/facilityreports/'])
				</li>
			@endif
			
            <li><a href="#js-help-modal" data-url="{{url('help/facility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" ><!-- Col-12 starts -->
	
    <div class="box box-info no-shadow"><!-- Box Starts -->
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Facility List</h3>
            <div class="box-tools pull-right margin-t-2">
               @if($checkpermission->check_url_permission('facility/create') == 1)
               <a href="{{ url('facility/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Facility</a>    
		@endif	
            </div>
        </div><!-- /.box-header -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			@if(Session::get('message')!== null) 
				<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
			@endif
		</div>
        <div class="box-body" id="js_table_search_listing">	<!-- Box Body Starts -->
             @include('practice/facility/facility_list')
        </div><!-- /.box-body ends -->
    </div><!-- /.box  ends -->
</div><!-- Col-12 Ends -->
<!--End-->
@stop    