@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="home"></i> Home </small> <span class="font14 med-orange">|</span> <small class="toolbar-heading"> <i class="livicon med-breadcrum" data-name="calendar"></i> Calendar </small> <span class="font14 med-orange">|</span> <small class="toolbar-heading"> <i class="livicon med-breadcrum" data-name="user"></i> Patient List </small> <span class="font14 med-orange">|</span> <small class="toolbar-heading"> <i class="livicon med-breadcrum" data-name="calendar"></i> Scheduler </small>
        </h1>
        <ol class="breadcrumb">          
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href=""><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a></li>
            <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="box no-shadow no-border margin-t-m-20">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">        
		<div class="well well-sm">
			<strong>Category Title</strong>
			<div class="btn-group">
				<a href="#" id="list" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-th-list">
				</span>List</a> <a href="#" id="grid" class="btn btn-default btn-sm"><span
					class="glyphicon glyphicon-th"></span>Grid</a>
			</div>
		</div>
		<div id="products" class="row list-group">
			<div class="item  col-xs-4 col-lg-4">
				<div class="thumbnail">
					<img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
					<div class="caption">
						<h4 class="group inner list-group-item-heading">
							Product title</h4>
						<p class="group inner list-group-item-text">
							Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit,
							sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<p class="lead"> $21.000</p>
							</div>
							<div class="col-xs-12 col-md-6">
								<a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="item  col-xs-4 col-lg-4">
				<div class="thumbnail">
					<img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
					<div class="caption">
						<h4 class="group inner list-group-item-heading">
							Product title</h4>
						<p class="group inner list-group-item-text">
							Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit,
							sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<p class="lead"> $21.000</p>
							</div>
							<div class="col-xs-12 col-md-6">
								<a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="item  col-xs-4 col-lg-4">
				<div class="thumbnail">
					<img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
					<div class="caption">
						<h4 class="group inner list-group-item-heading">
							Product title</h4>
						<p class="group inner list-group-item-text">
							Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit,
							sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<p class="lead"> $21.000</p>
							</div>
							<div class="col-xs-12 col-md-6">
								<a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="item  col-xs-4 col-lg-4">
				<div class="thumbnail">
					<img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
					<div class="caption">
						<h4 class="group inner list-group-item-heading">
							Product title</h4>
						<p class="group inner list-group-item-text">
							Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit,
							sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<p class="lead"> $21.000</p>
							</div>
							<div class="col-xs-12 col-md-6">
								<a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="item  col-xs-4 col-lg-4">
				<div class="thumbnail">
					<img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
					<div class="caption">
						<h4 class="group inner list-group-item-heading">
							Product title</h4>
						<p class="group inner list-group-item-text">
							Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit,
							sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<p class="lead"> $21.000</p>
							</div>
							<div class="col-xs-12 col-md-6">
								<a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="item  col-xs-4 col-lg-4">
				<div class="thumbnail">
					<img class="group list-group-image" src="http://placehold.it/400x250/000/fff" alt="" />
					<div class="caption">
						<h4 class="group inner list-group-item-heading">
							Product title</h4>
						<p class="group inner list-group-item-text">
							Product description... Lorem ipsum dolor sit amet, consectetuer adipiscing elit,
							sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</p>
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<p class="lead"> $21.000</p>
							</div>
							<div class="col-xs-12 col-md-6">
								<a class="btn btn-success" href="http://www.jquery2dotnet.com">Add to cart</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	   
	</div><!-- /.box -->
</div>
<!--End-->
@stop

@push('view.scripts')
<script type="text/javascript">
    // $(document).ready(function () {
    // $(function () { alert('ffff');
    $("#scheduler_calendar").datepicker({
        dateFormat: "yy-mm-dd",
        onSelect: function (dateText) {
            current_date = $(this).val();
            triggerCalendar();
        }
    });
    // });      
    //});

    var current_date = '{{date("Y-m-d")}}';
    triggerCalendar();
</script>
@endpush