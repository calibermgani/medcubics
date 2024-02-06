@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.superbills')}} font14"></i> Superbills</small>
        </h1>
        <ol class="breadcrumb">
			<li class="dropdown messages-menu"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/superbillreports/'])
            </li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			<li><a href="" data-target="#js-help-modal" data-url="{{url('help/superbills')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
           <i class="fa fa-bars"></i><h3 class="box-title">Superbills List</h3>
            <div class="box-tools pull-right margin-t-2">
                <a href="{{ url('superbills/create') }}" class="font14 font600"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New E-Superbill</a>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Template Name</th>
                        <th>Provider</th>	
                        <th>Status</th>
						<th>Created On</th>
						<th>Created By</th>
                    </tr>
                </thead>
                <tbody>
				
                @foreach($superbill as $superbills)	
    			<?php $superbills->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($superbills->id);	?>    			
    				<tr data-url="{{ url('superbills/'.$superbills->id) }}" class="js-table-click clsCursor">
                        <td>{{ str_limit(@$superbills->template_name, 25, '...') }}</td>
                        <td>
        					<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
        						<a id="someelem{{hash('sha256',@$superbills->provider->id)}}" class="someelem" data-id="{{hash('sha256',@$superbills->provider->id)}}" href="javascript:void(0);"> {{ str_limit(@$superbills->provider->provider_name,25,'...') }} {{@$superbills->provider->degrees->degree_name }}</a>
        						<?php $provider = $superbills->provider; ?>  
        						@include ('layouts/provider_hover')
        					</div>
        				</td>
                        <td>{{ @$superbills->status }}</td>
        				<td>{{ App\Http\Helpers\Helpers::dateFormat(@$superbills->created_at,'date')}}</td>
        				<td>{{ @$superbills->creator->name}}</td>
                    </tr>    			
                @endforeach
                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div> 
<!--End-->
@stop   