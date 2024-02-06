@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.admin.users')}}" data-name="users"></i> Set Permission <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> User <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>{{ $customerusers->firstname.' '.$customerusers->lastname }}</span> </small>
        </h1>
        <ol class="breadcrumb">
        <li><a  href="{{ url('admin/customer/'.$customer_id.'/customerusers/'.$customer_user_id.'/setpracticeforusers')}}"><i class="fa fa-reply" data-placement=	"bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/customer')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
    {!! Form::open(['url'=>'admin/customer/'.$customer_id.'/customerusers/'.$customer_user_id.'/setpracticeforusers','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}  
	{!! Form::hidden('customer_id',$customer_id) !!}
	{!! Form::hidden('user_id',$customer_user_id) !!}
    @include ('admin/customer/setpracticeforusers/form',['submitBtn'=>'Save'])</center>
    {!! Form::close() !!}
@stop