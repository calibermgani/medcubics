{!! Form::model($claimotherdetail, ['method'=>'post', 'form-data' => 'claimotherdetail', 'class'=>'js-submit-popupform popupmedcubicsform','url'=>'patients/claimotherdetail/update/'.$claimotherdetail->id]) !!}
    @include ('patients/claimotherdetail/forms',['submitBtn'=>'Update'])
{!! Form::close() !!}  
