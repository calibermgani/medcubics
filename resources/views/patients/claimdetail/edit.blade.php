{!! Form::model($claimdetail, ['method'=>'post','id'=>'ClaimValidate', 'form-data' => 'claimdetail','class'=>'js-submit-popupform popupmedcubicsform','url'=>'patients/claimdetail/update/'.$claimdetail->id]) !!}
@include ('patients/claimdetail/forms',['submitBtn'=>'Update'])
{!! Form::close() !!}