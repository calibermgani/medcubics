{!! Form::model($patients, array('url'=>'patients/store/'.$id,'id' => 'js-bootstrap-validator','class' => 'patients-info-form medcubicsform','name'=>'patients-form','files' => true)) !!}
{!! Form::hidden('next_tab',null,['class'=>'form-control','id'=>'next_tab']) !!}
@include ('patients/patients/personal-info',['submitBtn'=>'Save']) 
{!! Form::close() !!}