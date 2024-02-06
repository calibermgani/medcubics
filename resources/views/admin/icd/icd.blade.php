@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.icd')}} font14"></i> ICD-10</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
				 @if($checkpermission->check_adminurl_permission('api/admin/icdreportsmedcubics/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/icdreportsmedcubics/'])
					</li>
				@endif
				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="" data-target="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop

@section('practice-info')
	@include ('admin/icd/tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
			   <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">ICD-10 List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_adminurl_permission('admin/icd/create') == 1)
						<a href="{{ url('admin/icd/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New ICD</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped icd10_list_admin">
						<thead>
							<tr>
							  <th>Code</th>
                                <th class="td-c-60">Short Description</th>
                                <th>Gender</th>
                                <th>Effective Date</th>
								<th>Inactive Date</th> 
							</tr>
						</thead>
					</table>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>  
@stop