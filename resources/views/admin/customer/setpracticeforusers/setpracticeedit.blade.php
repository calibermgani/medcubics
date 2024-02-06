{!! Form::model($practice_permissions, ['method'=>'PATCH', 'url'=>'admin/customer/'.$customer_id.'/customerusers/'.$user_id.'/setpracticeforusers/'.$practice_id,'id'=>'js-bootstrap-validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}
@php   $practice_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($practice_id,'decode');  @endphp
@include ('admin/customer/setpracticeforusers/setpracticeform')
{!! Form::close() !!}