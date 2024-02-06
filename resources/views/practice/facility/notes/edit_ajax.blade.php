<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
            <div class="box box-view no-shadow no-padding yes-border no-bottom">
                
                <div class="box-body no-padding table-responsive margin-t-10">
				 {!! Form::model($notes, ['method'=>'PATCH','id'=>'js-bootstrap-validator', 'url'=>'notes/'.$notes->id,'name'=>'medcubicsform','class'=>'medcubicsform js-submit-popupform-notes']) !!}
					@include ('practice/practice/notes/form',['submitBtn'=>'Save'])
				{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $('#js-bootstrap-validator').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            title:{
                message:'',
                validators:{
                    notEmpty:{
                        message: '{{ trans("common.validation.title") }}'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z ]+$/,
                         message: '{{ trans("common.validation.alphaspace") }}'
                    }
                }
            },
            content:{
                message:'',
                validators:{
                    notEmpty:{
                        message: '{{ trans("common.validation.content") }}'
                    },
                }
            },
        }
    });
});
</script>