@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}  font14"></i> Insurance</small>
        </h1>
        <ol class="breadcrumb">
       
           
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
			
			@if(count(@$insurances)>0)
				@if($checkpermission->check_adminurl_permission('api/admin/insurancereports/{export}') == 1)
				<li class="dropdown messages-menu hide"><a href="" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/insurancereports/'])
				 </li>
				@endif
			@endif

            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('admin/insurance/tabs')
@stop


@section('practice')

    <div class="col-lg-12">
    @if(Session::get('message')!== null)
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
    </div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  margin-t-10">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
           <i class="fa fa-bars"></i><h3 class="box-title">Insurance List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_adminurl_permission('admin/insurance/create') == 1)
                <a href="{{ url('admin/insurance/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New Insurance</a>
            @endif

            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive" >
            <table id="example1" class=" list-insurance-admin table table-bordered table-striped">
                <thead>
                    <tr>
                       <th>Short Name</th>                       	
                       	<th>Insurance Name</th> 
						<th>Insurance Type</th>	                      	
                        <th>Address</th>
						<th>Phone</th>
                        <th>Payer ID</th>
                    </tr>
                </thead>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
    
@include('practice/layouts/favourite_modal')
@stop