@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.feeschedule')}}" data-name="inbox-empty"></i>Fee Schedule</small>
        </h1>
        <ol class="breadcrumb">
            @if($checkpermission->check_adminurl_permission('admin/feeschedule/create') == 1)
				<li><a href="{{ url('admin/feeschedule/create') }}"><i class="fa fa-plus-circle" data-placement="bottom" data-toggle="tooltip" data-original-title="New"></i></a></li>
            @endif
			<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			@if($checkpermission->check_adminurl_permission('api/admin/feeschedulereportsmedcubics/{export}') == 1)
				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/feeschedulereportsmedcubics/'])
				</li>
            @endif
			@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/fees_schedule')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
	<div class="col-lg-12">
		@if(Session::get('message')!== null)
			<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
		@endif
	</div>
<div class="col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Fee Schedule List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Fees Type</th>
                        <th>Template</th>
                        <th>Year</th>
                        <th>Created By</th>
                        <th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feeschedules as $feeschedule)
						<tr data-url="{{ url('admin/feeschedule/'.$feeschedule->id) }}" class="js-table-click clsCursor">
							<td>{{ $feeschedule->file_name }}</td>
							<td>{{ $feeschedule->fees_type }}</td>
							<td>{{ $feeschedule->template }}</td>
							<td>{{ @$feeschedule->choose_year }}</td>
							<td>@if($feeschedule->created_by != ''){{ @$feeschedule->user->name }}@endif</td>
							<td>@if($feeschedule->updated_by != ''){{ @$feeschedule->userupdate->name }}@endif</td>
						</tr>
                    @endforeach
                </tbody>
            </table>
          </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@stop