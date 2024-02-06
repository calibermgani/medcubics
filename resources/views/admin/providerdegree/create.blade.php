@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.providerdegree')}}" data-name="certificate"></i>Provider Degree <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Provider Degree</span></small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="javascript:void(0)" data-url="{{ url('admin/providerdegree')}}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)    
				<li><a href="#js-help-modal" data-url="{{url('help/provider_degree')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
{!! Form::open(['url'=>['admin/providerdegree'], 'id'=>'js-bootstrap-validator', 'files'=>true, 'name'=>'medcubicsform', 'class'=>'medcubicsform']) !!}    
    @include ('admin/providerdegree/form',['submitBtn'=>'Save'])
{!! Form::close() !!}
@stop            