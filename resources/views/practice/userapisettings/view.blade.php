@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>User API Settings</span> </small>
        </h1>
        <ol class="breadcrumb">
			<!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->			
            <li><a href="#js-help-modal" data-url="{{url('help/userapisettings')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row ends -->
@stop

@section('practice-info')
@include ('practice/apisettings/apisettings_tabs')
@stop

@section('practice')
	{!! Form::open(['url'=>'userapisettings','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
		@include ('practice/userapisettings/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop