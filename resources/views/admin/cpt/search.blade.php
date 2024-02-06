@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} " data-name="notebook"></i>CPT</small>
			</h1>
			<ol class="breadcrumb">                   
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/cpt/tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!-- Inner Content for full width Starts -->
		<div class="box-body-block"><!--Background color for Inner Content Starts -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
				<div id="example1_filter" class="dataTables_filter pull-left"> 
					{!! Form::open(['method'=>'POST','id'=>'js-bootstrap-validator','class'=>'advanced_search_form','name'=>'search_keyword_cpt']) !!}
					<div class= "form-group col-lg-7 col-md-12 col-sm-12 col-xs-12 no-padding">
							<input type="search" name="search_keyword" class="form-control" placeholder="Enter your text here..." aria-controls="example1">
							<input type="hidden" name="search_for" value="cpt">
					</div>
					<div class= "col-lg-5 col-md-12 col-sm-12 col-xs-12 no-padding">
						<a class="js_advanced_search btn btn-medgreen line-height-10" data-value="cpt"><i class="fa fa-search"></i> Search</a>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div><!--Background color for Inner Content Ends -->
		<div class="box-body-block"><!--Background color for Inner Content Starts -->
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
				<div class="box box-info no-shadow hide" id="js_result_show">
					<div class="box-header with-border">
						<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Search List</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div><!-- /.box-header --> 
					<div class="box-body" id="js_advanced_result_table">

					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<!-- IMO Search popmodel and reload button Start-->
				<div id="overlay_part" class="overlay col-xs-offset-2 hide">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 js_processing_image "> <i class="fa fa-spinner fa-spin font20"></i> Processing </div>
				</div>

				<div id="form_modal_view" class="modal fade in">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
								<h4 class="modal-title">Cpt Details</h4>
							</div>
							<div class="modal-body"><span id="js_content"></span></div>
							<div class="modal-footer">
								<button class="btn btn-medcubics" data-dismiss="modal" type="button">Close</button>
							</div>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div>
				<!-- IMO Search popmodel and reload button End-->
			</div><!--Background color for Inner Content Ends -->
		</div><!-- Inner Content for full width Ends -->
    <!--End-->
    @stop
    <!--End-->