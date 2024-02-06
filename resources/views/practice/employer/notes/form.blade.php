{!! Form::label('code', 'Title', ['class'=>'control-label lab-size']) !!}                           
<div class="controls @if($errors->first('title')) error @endif">
	{!! Form::text('title') !!}
	{!! $errors->first('title', '<p>:message</p>') !!}
</div>

{!! Form::label('code', 'Content', ['class'=>'control-label lab-size']) !!}                           
<div class="controls @if($errors->first('content')) error @endif">
	{!! Form::textarea('content') !!}
	{!! $errors->first('content', '<p>:message</p>') !!}
</div>

{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics','style'=>'float:right']) !!}
{!! Form::button('Cancel', ['class'=>'btn btn-medcubics','onclick' => 'history.back(-1) js_cancel_site']) !!}
<script type="text/javascript">
// JavaScript Document<script type="text/javascript">

$(document).ready(function() {	
    $('#kl').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
			title:{
                message:'The Code is invalid',
                validators:{
                    notEmpty:{
                        message: '{{ trans("common.validation.title") }}'
                   	}
                }
			},			
			content:{
                message:'The practice name is invalid',
                validators:{
                    notEmpty:{
                        message: '{{ trans("common.validation.content") }}'
                   	}
                }
			}
			
		}
	});
});
</script>