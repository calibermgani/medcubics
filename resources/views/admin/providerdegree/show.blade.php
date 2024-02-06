@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.providerdegree')}}" data-name="certificate"></i> Provider Degree <i class="fa fa-angle-double-right med-breadcrum"></i><span>View</span></small>
        </h1>
		<?php $degrees->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($degrees->id,'encode'); ?>
        <ol class="breadcrumb">
			<li><a href="{{ url('admin/providerdegree')}}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>           
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
             @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/provider_degree')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-m-18">    
	@if($checkpermission->check_adminurl_permission('admin/providerdegree/{providerdegree}/edit') == 1)
        <a href="{{ url('admin/providerdegree/'.$degrees->id.'/edit')}}"  class="font600 font14 pull-right margin-r-5"><i class="fa fa-edit" data-placement="bottom"  data-toggle="tooltip" data-original-title="Edit"></i> Edit</a>
    @endif
</div>
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
						<td>Provider Degree Name</td>
						<td>{{ $degrees->degree_name }} </td>
					</tr>

					<tr>
						<td>Created On</td>
						<td>@if(App\Http\Helpers\Helpers::dateFormat($degrees->created_at,'date'))<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($degrees->created_at,'date')}}</span>@endif</td>
					</tr>

					<tr>
						<td>Created By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($degrees->created_by) }}</td>
					</tr>

					<tr>
						<td>Updated On</td>
						<td>@if($degrees->updated_by != '')<span class='bg-date'>{{ App\Http\Helpers\Helpers::dateFormat($degrees->updated_at,'date')}}</span>@endif</td>
					</tr>

					<tr>
						<td>Updated By</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($degrees->updated_by) }}</td>
					</tr>
				</tbody>
			</table>
        </div><!-- /.box-body -->
    </div><!-- /.box Ends-->
</div><!--  Left side Content Ends -->
<!--End-->
@stop