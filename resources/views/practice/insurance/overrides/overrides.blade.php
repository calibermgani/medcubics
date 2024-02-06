@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.insurance')}} font14"></i> Insurance <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Overrides</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('insurance/'.$insurance->id) }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
			@if(count($overrides)>0)
			<li class="dropdown messages-menu"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/insuranceoverridesreports/'.$insurance->id.'/'])
           </li>
		   @endif
            
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
    @include ('practice/insurance/insurance_tabs')  
@stop

@section('practice')

        <div class="col-lg-12">
        @if(Session::get('message')!== null) 
        <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
        @endif
        </div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
           <i class="fa fa-bars"></i><h3 class="box-title">Overrides List</h3>
           <div class="box-tools pull-right margin-t-2">
               @if($checkpermission->check_url_permission('insurance/{insurance_id}/insuranceoverrides/create') == 1)
               <a href="{{url('insurance/'.$insurance->id.'/insuranceoverrides/create')}}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Overrides</a>
               @endif
           </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Provider Name</th>
                        <th>Tax ID</th>
                        <th>NPI</th>
                        <th>Provider ID</th>
                        <th>ID Type</th>
                    </tr>
                </thead>
                <tbody>
				<?php $count = 1;   ?> 
                    @foreach($overrides as $override)
					<?php $override->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($override->id,'encode'); ?>
                <tr data-url="{{ url('insurance/'.$insurance->id.'/insuranceoverrides/'.$override->id) }}" class="js-table-click clsCursor">
                     <td><div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
						<a id="someelem{{hash('sha256',@$override->provider->id.$count)}}" class="someelem" data-id="{{hash('sha256',@$override->provider->id.$count)}}" href="javascript:void(0);"> {{ str_limit(@$override->provider->provider_name,25,'...') }} {{ str_limit(@$override->provider->degrees->degree_name,5,'...') }}</a>
						<?php $provider = $override->provider; 
							@$provider->id = @$override->provider->id.$count;
						?>  
						@include ('layouts/provider_hover')
						</div>
					</td>
                    <td>{{ @$override->provider->etin_type_number }}</td>
                    <td>{{ @$override->provider->npi }}</td>
                    <td>{{ $override->provider_id }}</td>
                    <td>{{ @$override->id_qualifier->id_qualifier_name }}</td>                  
                </tr>
				<?php $count++;   ?> 
                @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
  
<!--End-->
@stop   