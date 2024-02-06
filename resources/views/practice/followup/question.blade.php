@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.calendar')}} font14"></i> Follow-up Template</small>
        </h1>
        <ol class="breadcrumb">
<!-- <li><a href="javascript:void(0);l" data-url="{{url('help/codes')}}" class="js-help" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li> -->
        </ol>
    </section>
</div>
@stop
@section('practice-info')
	@include ('practice/followup/tabs')
@stop
@section('practice')
<div class="col-lg-12">
	@if(Session::get('message')!== null) 
	<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
	@endif
</div> 
<?php $type = 'followup/category'; ?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!-- Col Starts -->
	<div class="box box-info no-shadow"><!-- Box Starts Here -->
		<div class="box-header margin-b-10">
			<i class="fa fa-bars"></i> <h3 class="box-title">Claim Status Question</h3>
			<div class="box-tools pull-right margin-t-2">
				@if($checkpermission->check_url_permission('followup/create-question') == 1)
				<a href="{{ url('followup/create-question') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Followup Questions</a>
				@endif
			</div>
		</div><!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive" id="js_table_search_listing">
				@include('practice/followup/followup-question-list')
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box ends -->
</div><!-- Col Starts -->
<!--End-->
@stop   