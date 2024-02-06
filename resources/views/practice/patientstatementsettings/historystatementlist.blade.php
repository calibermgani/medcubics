@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
		<h1>
			<small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Patient Statement <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Statement History</span></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url('patientstatementsettings') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="#js-help-modal" data-url="{{url('help/patientstatement')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
	</section>
</div>
@stop

@section('practice-info')
	@include ('practice/patientstatementsettings/tabs')
@stop

@section('practice')

    <div class="col-lg-12 col-md-12 col-xs-12 margin-t-20"><!--  Col-12 Starts -->

        <div class="box no-shadow"><!--  Left side Content Starts -->
            <div class="box-header-view">
                <i class="fa {{Config::get('cssconfigs.patient.history')}}"></i> <h3 class="box-title">Statement History</h3>
                 <div class="box-tools pull-right">
					<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
            </div><!-- /.box-header -->
			
			<div class="box-body" id="js_table_search_listing">	<!-- Box Body Starts -->
				@include('practice/patientstatementsettings/history_list')
		  	</div>
        </div><!--  Left side Content Ends -->
	</div><!--Background color for Inner Content Ends -->
@stop