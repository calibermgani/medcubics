@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php  $insurance->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($insurance->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}}" data-name="bank"></i>Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Overrides</span></small>
        </h1>
        <ol class="breadcrumb">
             <li><a href="{{ url('admin/insurance/'.$insurance->id) }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            
             
              
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
           
		   @if(count($overrides)>0)
				@if($checkpermission->check_adminurl_permission('api/admin/insuranceoverridesreports/{insurance_id}/{export}') == 1)
				<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/insuranceoverridesreports/'.$insurance->id.'/'])
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

    @include ('admin/insurance/insurance_tabs')  
    @stop

@section('practice')

         <div class="col-lg-12">
        @if(Session::get('message')!== null) 
        <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
        @endif
        </div>
        
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow ">
        <div class="box-header margin-b-10">
           <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Overrides List</h3>
            <div class="box-tools pull-right margin-t-2">
               @if($checkpermission->check_adminurl_permission('admin/insurance/{insurance_id}/insuranceoverrides/create') == 1)
               <a href="{{url('admin/insurance/'.$insurance->id.'/insuranceoverrides/create')}}" class="font600 font14"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i> Add</a>  
              @endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Provider</th>
                        <th>Tax ID</th>
                        <th>NPI</th>
                        <th>Provider ID</th>
                        <th>ID Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($overrides as $override)
					<?php  $override->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($override->id,'encode'); ?>
                <tr data-url="{{ url('admin/insurance/'.$insurance->id.'/insuranceoverrides/'.$override->id) }}" class="js-table-click clsCursor">
					 <td><div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
						<a id="someelem{{hash('sha256',@$override->provider->id)}}" class="someelem" data-id="{{hash('sha256',@$override->provider->id)}}" href="javascript:void(0);"> {{ str_limit(@$override->provider->provider_name,25,'...') }} {{ str_limit(@$override->provider->degrees->degree_name,5,'...') }}</a>
						<?php  $provider = $override->provider; ?>  
						@include ('layouts/provider_hover')
						</div>
					</td>
                    <td>{{ $override->provider->etin_type_number }}</td>
                    <td>{{ $override->provider->npi }}</td>
                    <td>{{ $override->provider_id }}</td>
                    <td>{{ $override->id_qualifier->id_qualifier_name }}</td>                  
                </tr>
                @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<!--End-->
@stop   