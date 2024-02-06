@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Account Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Statement Category</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/statementcategoryreports/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/hold_option')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
	@include ('practice/apisettings/tabs')
@stop

@section('practice')
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" ><!-- Col-12 starts -->
		<div class="box box-info no-shadow"><!-- Box Starts -->
			<div class="box-header margin-b-10">
				<i class="fa fa-bars font14"></i><h3 class="box-title">Statement Category</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_url_permission('statementcategory/create') == 1)
						<a href="{{ url('statementcategory/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Category</a>
					@endif 
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">	<!-- Box Body Starts -->
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped ">         
						<thead>
							<tr>
								<th>Statement Category</th>
								<th>Statement Option</th>								
								<th>Status</th>
								<th>Created By</th>
								<th>Updated By</th>
								<th>Updated On</th>
							</tr>
						</thead>
						<tbody>
							@foreach($statementcategory as $stmt_cat)
							<?php $stmt_cat->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($stmt_cat->id,'encode'); ?>
							<tr  @if($checkpermission->check_url_permission('statementcategory/{statementcategory}') == 1) data-url="{{ url('statementcategory/'.$stmt_cat->id) }}" @endif class="js-table-click clsCursor" >
								<td>{{ @str_limit($stmt_cat->category,25, '...') }}</td>
								<td>{{ $stmt_cat->stmt_option }}</td>								
								<td>{{ @$stmt_cat->status }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($stmt_cat->created_by) }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($stmt_cat->updated_by) }}</td>								
								<td>
								@if($stmt_cat->updated_by !='' && $stmt_cat->updated_by !='0')
								<?php /*
									{{ App\Http\Helpers\Helpers::dateFormat($stmt_cat->updated_at,'date') }}
								*/ ?>
								{{ App\Http\Helpers\Helpers::dateFormat($stmt_cat->updated_at, 'date') }}
								@endif
								</td>								
							</tr>
							@endforeach      
						</tbody>
					</table>
				</div>                                
			</div><!-- /.box-body ends -->
		</div><!-- /.box  ends -->
	</div><!-- Col-12 Ends -->
<!--End-->
@stop  