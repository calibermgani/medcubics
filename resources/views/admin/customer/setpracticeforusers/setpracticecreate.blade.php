{!! Form::open(['url'=>'admin/customer/'.$customer_id.'/customerusers/'.$customer_user_id.'/setpracticeforusers','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform']) !!}  
    {!! Form::hidden('customer_id',$customer_id) !!}
    {!! Form::hidden('user_id',$customer_user_id) !!}
@include ('admin/customer/setpracticeforusers/setpracticeform')
{!! Form::close() !!}   