@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i>Customers <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Notes <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Note</span></small>
        </h1>
        <ol class="breadcrumb">
           <li><a href="javascript:void(0)" data-url="{{ url('admin/customer/'.$customer->id.'/customernotes') }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            @if($checkpermission->check_adminurl_permission('help/{type}') == 1)
				<li><a href="#js-help-modal" data-url="{{url('help/note')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            @endif
        </ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('admin/customer/tabs')   
@stop
<?php
    if(isset($customer->encid) && $customer->encid != ''){
        $customer->id = $customer->encid;
    }else{
        $customer->id = $customer->id;
    }
?>
@section('practice')
	{!! Form::open(array('url' => 'admin/customer/'.$customer->id.'/customernotes','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform')) !!}
	@include ('admin/customer/customernotes/form',['submitBtn'=>'Save','label'=>'New'])
	{!! Form::close() !!}
@stop            