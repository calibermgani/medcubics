{!! Form::open(['url'=>'patients/claimdetail','id'=>'ClaimValidate', 'class'=>'js-submit-popupform popupmedcubicsform', 'form-data' => 'claimdetail']) !!}
@include ('patients/claimdetail/forms',['submitBtn'=>'Save'])
{!! Form::close() !!}