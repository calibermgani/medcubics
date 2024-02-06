@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Account Preference <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Claim Sub Status</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/claimsubstatusreports/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/claimsubstatus')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
				<i class="fa fa-bars font14"></i><h3 class="box-title">Claim Sub Status</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_url_permission('claimsubstatus/create') == 1)
						<a href="{{ url('claimsubstatus/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Claim Sub Status</a>
					@endif 
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">	<!-- Box Body Starts -->
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped ">         
						<thead>
							<tr>
                                <th class="width-7">S.No</th>
								<th>Claim Sub-Status</th>
								<th>Status</th>
								<th>Created By</th>
								<th>Updated By</th>
								<th>Updated On</th>
							</tr>
						</thead>
						<tbody>
							@foreach($claimsubstatus as $key=> $rec)
							<?php $rec_encid = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($rec->id,'encode'); ?>
							<tr @if($checkpermission->check_url_permission('claimsubstatus/{claimsubstatus}') == 1) data-url="{{ url('claimsubstatus/'.$rec_encid) }}" @endif class="js-table-click clsCursor" >
								<td>{{$key+1}}</td>
								<td>{{ @str_limit($rec->sub_status_desc,25, '...') }}</td>
								<td>{{ @$rec->status }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($rec->created_by) }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname($rec->updated_by) }}</td>
								<td>
									@if($rec->updated_by !='' && $rec->updated_by !='0')								
										{{ App\Http\Helpers\Helpers::dateFormat($rec->updated_at, 'date') }}
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