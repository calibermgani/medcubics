<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
            <div class="box box-view no-shadow no-padding yes-border no-bottom">
                
                <div class="box-body no-padding table-responsive margin-t-10">
				{!! Form::open(['url'=>'patients/'.$patients->enc_id.'/notes','id'=>'js-bootstrap-validator','name'=>'medcubicsform','class'=>'medcubicsform js-submit-popupform-notes']) !!}
				@include ('patients/patients/notes/form',['submitBtn'=>'Save'])
				{!! Form::close() !!}

				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $('#js-bootstrap-validator').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {              
                patient_notes_type: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patients_notes.validation.type") }}'
                        }
                    }
                },
                content: {
                    message: '',
                    validators: {
                        notEmpty: {
                            message: ' '
                        }
                    }
                },
               /* claim_id: {
                    enabled: false,
                    message: '',
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/patients/patients_notes.validation.claim_id") }}'
                        }
                    }
                },*/
                'jsclaimnumber':{
                    enabled: false,
                    message:'',
                    selector: '#jsclaimnumber',                   
                    validators:{                        
                         notEmpty: {
                            message: '{{ trans("practice/patients/patients_notes.validation.claim_id") }}'
                        }
                    }
                },
            }
        });
    });
</script>