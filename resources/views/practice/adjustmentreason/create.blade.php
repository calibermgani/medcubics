@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Account Preference
                 <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Adjustment Reason <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="javascript:void(0)" data-url="{{ url('adjustmentreason') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/adjustmentreasion')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')

	@include ('practice/apisettings/tabs')
	{!! Form::open(['url'=>'adjustmentreason','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}   
		@include ('practice/adjustmentreason/form',['submitBtn'=>'Save'])
	{!! Form::close() !!}
@stop