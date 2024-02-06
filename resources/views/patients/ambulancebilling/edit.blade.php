{!! Form::model($claimambulancedetail, ['method'=>'post','id' => 'claimbilling','class'=>'js-submit-popupform popupmedcubicsform','url'=>'patients/claimbilling/update/'.$claimambulancedetail->id]) !!}
@include ('patients/ambulancebilling/forms',['submitBtn'=>'Update'])
{!! Form::close() !!}