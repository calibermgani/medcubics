@extends('admin')


@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Starts here -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Level I </span></small>
        </h1>
        <ol class="breadcrumb">
           	
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			@if(count($modifiers)>0)
            <li class="dropdown messages-menu hide"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/modifierreports/'])
            </li>
			@endif
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/modifiers')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Ends here -->
@stop

@section('practice-info')
@include ('practice/modifier/tabs')
@stop
@section('practice')
<div class="col-lg-12">
	@if(Session::get('message')!== null) 
	<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
	@endif
</div> 
<?php $type = 'modifierlevel1'; ?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!-- Col Starts -->
	<div class="box box-info no-shadow"><!-- Box Starts Here -->
		<div class="box-header margin-b-10">
			<i class="fa fa-bars"></i> <h3 class="box-title">Level I List</h3>
			<div class="box-tools pull-right margin-t-2">
				@if(count($modifiers)>0)
					@if($checkpermission->check_url_permission('modifierlevel1/create') == 1)
					<a href="{{ url('modifierlevel1/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Modifier</a>
					@endif
				@else
					<a href="" class="selFnMod med-red font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> Import Modifier</a>
				@endif				
			</div>
		</div><!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive" id="js_table_search_listing">
				@include('practice/modifier/modifier-list')
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box ends -->
</div><!-- Col Starts -->
   
<!--End-->
@stop
@push('view.scripts')
<script type="text/javascript">
	var api_site_url = '{{url('/')}}';
    $(document).ready(function () {
		$(document).on('click', '.selFnMod', function (e) {
            
			$.ajax({
				type: 'GET',
				url: api_site_url + '/getmastermodifier',
				success: function (result) {
					js_alert_popup(result.message);
					 window.location = api_site_url + '/modifierlevel1'; 
				}
			});
			e.preventDefault();
		});	
	});
</script>
@endpush