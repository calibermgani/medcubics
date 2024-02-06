@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.feeschedule')}}" data-name="inbox-empty"></i>Fee Schedule <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Fee Schedule</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="{{ url('admin/feeschedule') }}"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
             @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/fees_schedule')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
             @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
	{!! Form::open(['url'=>['admin/feeschedule'],'id'=>'js-bootstrap-validator','files'=>true]) !!}    
        @include ('admin/feeschedule/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop            