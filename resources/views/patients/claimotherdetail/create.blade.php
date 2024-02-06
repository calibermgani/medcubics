{!! Form::open(['url'=>'patients/claimotherdetail','class'=>'js-submit-popupform popupmedcubicsform', 'form-data' => 'claimotherdetail']) !!}
@include ('patients/claimotherdetail/forms',['submitBtn'=>'Save'])
{!! Form::close() !!}