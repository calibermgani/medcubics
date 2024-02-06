@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.contact_detail')}} font14"></i> CPT</small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			@if($checkpermission->check_adminurl_permission('api/admin/cptreportsmedcubics/{export}') == 1)
				<li class="dropdown messages-menu hide"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/admin/cptreportsmedcubics/'])
				</li>
            @endif
			@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('admin/cpt/tabs')
@stop

@section('practice')

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
		<div class="box box-info no-shadow">
			<div class="box-header margin-b-10">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">CPT List</h3>
				<div class="box-tools pull-right margin-t-2">
					  @if($checkpermission->check_adminurl_permission('admin/cpt/create') == 1)
						<a href="{{ url('admin/cpt/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<div class="table-responsive">
					<table class="table table-bordered table-striped list-cpt-admin">
						<thead>
							<tr>
								<th>CPT / HCPCS</th>
								<th>Short Description</th>
								<th>Billed Amount</th>
								<th>Allowed Amount</th>
								<th>POS</th>
								<th>Type of service</th>								
							</tr>
						</thead>
					</table>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->
	</div>
  
@stop
