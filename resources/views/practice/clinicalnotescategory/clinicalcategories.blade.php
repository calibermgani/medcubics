@extends('admin')

@section('toolbar')
	<div class="row toolbar-header"><!-- Toolbar row Starts -->
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.apisettings')}} font14"></i> Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Clinical Notes Category</span></small>
			</h1>
			<ol class="breadcrumb">
			   
						
				<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/clinicalcategoriesreports/'])
				</li>

				<li><a href="#js-help-modal" data-url="{{url('help/clinicalcategories')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
			</ol>
		</section>
	</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
	@include ('practice/apisettings/tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" >
		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
				<i class="fa fa-bars font14"></i><h3 class="box-title">Clinical Notes Category List</h3>
				<div class="box-tools pull-right margin-t-2">
					<a href="{{ url('clinicalnotescategory/create') }}" class="font600 font14"> <i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Clinical Category</a>
				</div>
			</div><!-- /.box-header -->
			<!-- form start -->
			<div class="box-body">
				<table id="example1" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Name</th>
							<th>Created By</th>	
							<th>Created On</th>
						</tr>
					</thead>
					<tbody>
						@foreach($clinicalcategories as $clinicalcategories)	
							<?php $clinicalcategories->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($clinicalcategories->id,'encode'); ?>
							<tr data-url="{{ url('clinicalnotescategory/'.$clinicalcategories->id.'/edit') }}" class="js-table-click clsCursor">
								<td> {{ @$clinicalcategories->category_value }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($clinicalcategories->created_by) }}</td>
								<td>{{ App\Http\Helpers\Helpers::dateFormat($clinicalcategories->created_at,'date')}}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div><!--/.col (left) -->
	<!--End-->
@stop 