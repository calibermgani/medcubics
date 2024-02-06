@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php 
		$practice_id = $practice->id;
		$practice->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice->id,'encode'); 
	?> 
    <section class="content-header">
        <h1>            
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Provider</span></small>
        </h1>
        <ol class="breadcrumb">
        <li><a href="{{ url('admin/customer/'.$cust_id.'/customerpractices/'.$practice->id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
          
            
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('admin/providerreports/{id}/{export}') == 1)
             <li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'admin/providerreports/'.$practice->id.'/'])
            </li>
            @endif
            
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/provider')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice')
	@include ('admin/customer/customerpractices/tabs')

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="box box-info no-shadow space20">
			<div class="box-header margin-b-10">
				<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Provider List</h3>
				<div class="box-tools pull-right margin-t-2">
					@if($checkpermission->check_adminurl_permission('admin/customer/{customer_id}/practice/{practice_id}/providers/create') == 1)
					<a href="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice->id.'/providers/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> Add</a>
					@endif
				</div>
			</div><!-- /.box-header -->
			<div class="box-body">
				<div class="table-responsive"> 
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Short Name</th>
								<th>Provider Name</th>                                    
								<th>Type</th>
								<th>ETIN Type</th>
								<th>Tax ID/SSN</th>
								<th>NPI</th>
								<th>Specialty</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@foreach($providers as $provider)
							<?php $provider->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode'); ?> 
							<tr data-url="{{ url('admin/customer/'.$cust_id.'/practice/'.$practice->id.'/providers/'.$provider->id) }}" class="js-table-click clsCursor">
								<td>
									<?php $provider_name = $provider->provider_name." ".@$provider->degrees->degree_name; ?>
									<div class="col-lg-12 p-b-0 p-l-0">
									<a id="someelem{{hash('sha256',@$provider->id)}}" class="someelem" data-id="{{hash('sha256',@$provider->id)}}" href="javascript:void(0);"> {{ @$provider->short_name }}</a> 
									@include ('layouts/provider_hover')
							</div>   
								</td>
								<td>{{ $provider->provider_name }} {{ @$provider->degrees->degree_name }}</td>
								<td>{{ @$provider->provider_types->name }}</td>
								<td>{{ $provider->etin_type }}</td>
								<td>{{ $provider->etin_type_number }}</td>
								<td>{{ $provider->npi }}</td>
								<td>@if($provider->speciality != ''){{ @$provider->speciality->speciality }}@endif</td>
								<td>{{ $provider->status }}</td>               
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