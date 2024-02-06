@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports </small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow no-border">

        <div class="box-body table-responsive">       
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 space20">
				<!-- AREA CHART -->
				<div class="box box-view no-shadow ">
					<div class="box-header-view">
						<h3 class="box-title">Area Chart</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body chart-responsive no-padding">
						<div class="chart" id="revenue-chart" style="height: 300px;"></div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<!-- DONUT CHART -->
				<div class="box box-view no-shadow">
					<div class="box-header-view">
						<h3 class="box-title">Donut Chart</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body chart-responsive">
						<div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

			</div><!-- /.col (LEFT) -->
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 space20">
				<!-- LINE CHART -->
				<div class="box box-view no-shadow">
					<div class="box-header-view">
						<h3 class="box-title">Line Chart</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body chart-responsive no-padding">
						<div class="chart" id="line-chart" style="height: 300px;"></div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

				<!-- BAR CHART -->
				<div class="box box-view no-shadow">
					<div class="box-header-view">
						<h3 class="box-title">Bar Chart</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							<button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
						</div>
					</div>
					<div class="box-body chart-responsive">
						<div class="chart" id="bar-chart" style="height: 300px;"></div>
					</div><!-- /.box-body -->
				</div><!-- /.box -->

			</div><!-- /.col (RIGHT) -->

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop