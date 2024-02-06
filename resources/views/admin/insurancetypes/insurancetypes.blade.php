@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
	<section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.list_ul')}} font14"></i> Insurance Types</small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			@if($checkpermission->check_adminurl_permission('api/admin/insurancetypereports/{export}') == 1)
				<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/insurancetypereports/'])
				</li>
			@endif
			@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/insurance_types')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
           <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Insurance Types List</h3>
            <div class="box-tools pull-right margin-t-2">
                 @if($checkpermission->check_adminurl_permission('admin/insurancetypes/create') == 1)
					<a href="{{ url('admin/insurancetypes/create') }}" class="font14 font600"><i class="fa fa-plus-circle"></i> New Insurance Type</a>
				@endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
						<th>Type Name</th>
						<th>CMS Type</th>
						<th>Updated On</th>
						<th>Created By</th>
						<th>Updated By</th>
                    </tr>
                </thead>
                <tbody>
					@foreach($insurancetypes as $insurancetype)
					<?php $insurancetype->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurancetype->id,'encode'); ?> 
						<tr data-url="{{ url('admin/insurancetypes/'.$insurancetype->id) }}" class="js-table-click clsCursor">
							<td>{{ $insurancetype->type_name}}</td>
							<td>{{ @$insurancetype->cms_type}}</td>
							<td>
								@if($insurancetype->updated_at !='' && $insurancetype->updated_at !='-0001-11-30 00:00:00' && $insurancetype->updated_at !='0000-00-00 00:00:00')
									{{ App\Http\Helpers\Helpers::dateFormat($insurancetype->updated_at,'date')}}
								@endif
							</td>
							<td>
								@if($insurancetype->created_by != '')
									{{ @$insurancetype->user->short_name }}
								@endif
							</td>
							<td>
								@if($insurancetype->updated_by != '')
									{{ @$insurancetype->userupdate->short_name }}
								@endif
							</td>
						</tr>
					@endforeach
                </tbody>
            </table>
          </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
@stop