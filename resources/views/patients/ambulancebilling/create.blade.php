{!! Form::open(['url'=>'patients/claimbilling', 'id' => 'claimbilling','class'=>'js-submit-popupform popupmedcubicsform', 'form-data' => 'claimbilling']) !!}
@include ('patients/ambulancebilling/forms',['submitBtn'=>'Save'])
{!! Form::close() !!}
