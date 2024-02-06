@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.feeschedule')}}" data-name="inbox-empty"></i>Fee Schedule <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="{{ url('admin/feeschedule') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @if($checkpermission->check_adminurl_permission('admin/feeschedule/{feeschedule}/edit') == 1)
				<li><a href="{{ url('admin/feeschedule/'.$feeschedules->id.'/edit')}}"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i></a></li>
            @endif
			<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/fees_schedule')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box box-view no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
           <i class="livicon" data-name="info"></i> <h3 class="box-title">General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table class="table-responsive table-striped-view table">
                <tbody>
					<tr>
						<td>File Name</td>
						<td>{{ $feeschedules->file_name }}</td>
					</tr>
					<tr>
						<td>Fees Type</td>
						<td>{{ $feeschedules->fees_type }} </td>
					</tr>
					<tr>
						<td>Template</td>
						<td>{{ $feeschedules->template }} </td>
					</tr>
					<tr>
						<td>Choose year</td>
						<td>{{ $feeschedules->choose_year }} </td>
					</tr>
					<tr>
						<td>Conversion factor</td>
						<td>{{ $feeschedules->conversion_factor }} </td>
					</tr>
					<tr>
						<td>Percentage</td>
						<td>{{ $feeschedules->percentage }} </td>
					</tr>
					<tr>
						<td>Created At</td>
						<td><span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($feeschedules->created_at,'date')}}</td>
					</tr>
					<tr>
						<td>Created By</td>
						<td>@if($feeschedules->created_by != ''){{ @$feeschedules->user->name }}@endif</td>
					</tr>
					<tr>
						<td>Updated At</td>
						<td>@if($feeschedules->updated_at !='')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($feeschedules->updated_at,'date')}}</span>@endif</td>
					</tr>
					<tr>
						<td>Updated By</td>
						<td>@if($feeschedules->updated_by != ''){{ @$feeschedules->userupdate->name }}@endif</td>
					</tr>
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
<!--End-->
@stop