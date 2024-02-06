@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.role')}} font14"></i> Roles <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Role</span></small>
        </h1>
		<?php $roles->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($roles->id,'encode'); ?>	
        <ol class="breadcrumb">
            
            @if($roles->role_type == 'Medcubics')
                <li><a href="javascript:void(0)" data-url="{{ url('admin/medcubicsrole/'.$roles->id)}}" class="js_next_process"> <i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @else
                <li><a href="javascript:void(0)" data-url="{{ url('admin/practicerole/'.$roles->id)}}" class="js_next_process"> <i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            @endif
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
            	<li><a href="#js-help-modal" data-url="{{url('help/role')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice')
    {!! Form::model($roles, ['method'=>'PATCH', 'url'=>'admin/role/'.$roles->id, 'id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
    @include ('admin/role/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
@stop            