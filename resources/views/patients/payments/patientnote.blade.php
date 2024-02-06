<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
    <div class="box-body form-horizontal no-padding">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20" >
			<div class="box box-info no-bottom no-shadow">
			  
				<!-- form start -->      
				<div class="box-body form-horizontal js-collpased">
					 {!! Form::open(['url'=>'patients/'.$patient_id.'/notes','id'=>'js-patientpayment-note','name'=>'medcubicsform','class'=>'popupmedcubicsform js-patient-notes']) !!}
					<div class="form-group">
						{!! Form::label('Title', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!} 
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 @if($errors->first('title')) error @endif">
						{!! Form::text('title',null,['class'=>'form-control', 'maxlength' => 25]) !!}
						{!! $errors->first('title', '<p> :message</p>')  !!}
						</div>
					</div>                            
					<div class="form-group">
						{!! Form::label('content', 'Content', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
						<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 @if($errors->first('content')) error @endif">
						{!! Form::textarea('content',null,['class'=>'form-control']) !!}
						{!! $errors->first('content', '<p> :message</p>')  !!}
						</div>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
						{!! Form::hidden('patient_notes_type','patient_notes') !!} 
						{!! Form::hidden('claim_id','') !!}              
						{!! Form::submit("Submit", ['class'=>'btn btn-medcubics-small']) !!}
						<button class="btn btn-medcubics-small close_popup" type="button">Cancel</button>                                        
					</div> 
				{!! Form::close() !!} 
				</div><!-- /.box-body -->                     
			</div><!-- /.box -->
		</div><!--/.col (left) -->
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
    $(document).mapKey('Alt+u',function(e){
        if (!$("body").hasClass("modal-open")) { 
            $(".js-authpopup").click();
            $(".js-collpased").css("display","block");
            closeTheCalendar();
        }
    });
	$(document).ready(function() {
		$('#js-patientpayment-note').bootstrapValidator({
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
						}
					}
				},           
				content:{
					message:'',
					validators:{
						notEmpty:{
							message: '{{ trans("common.validation.content") }}'
						}
					}
				},            
			}
		});
	});
</script>