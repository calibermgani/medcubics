@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> App Templates </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/templatereports/App/'])
            </li>

            <li><a href="#js-help-modal" data-url="{{url('help/app_templates')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
	@include ('practice/questionnaires/tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">

    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Template List</h3>
        </div><!-- /.box-header -->
        
		<div class="box-body">
			<div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Created On</th>
                    </tr>
                </thead>
				<tbody>
				@if(!empty($templates))
					@foreach($templates->template as $template)
					<?php $template->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($template->id,'encode'); ?>
					<tr data-url="{{ url('apptemplate/'.$template->id) }}" class="js-table-click cur-pointer">
						<td> {{ @$template->name }}</td>
						<td>{{ @$template->status}}</td>
						<td>{{ App\Http\Helpers\Helpers::shortname($template->created_by) }}</td>
						<td><span class="bg-date">
                            {{ App\Http\Helpers\Helpers::timezone($template->created_at, 'm/d/y') }}
                        </span></td>
					</tr>
					@endforeach
				@endif
                </tbody>
            </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<!--End-->
@stop   