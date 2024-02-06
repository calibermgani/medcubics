<div class="js_input_add hide">
	<div class="js_input_add_text">
		<div class="form-group">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				{!! Form::text('text',NULL,['class'=>'form-control','data-name'=>'text','data-count'=>'add1','readonly'=>'true']) !!}
			</div>
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 margin-t-5">
				<span class="med-green js_add_reset form-cursor font600">&nbsp;Reset</span>
			</div>
		</div>
	</div>	
	<div class="js_input_add_radio">
		<div class="form-group">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				{!! Form::text('radio',NULL,['class'=>'js_need_regex form-control','autofocus'=>'on','data-name'=>'radio','data-count'=>'add1']) !!}
			</div>
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 margin-t-5">
				<span class="med-green js_add_reset form-cursor font600">&nbsp;Reset</span>
			</div>
		</div>
	</div>	
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
		{!! Form::text('radio',NULL,['class'=>'js_dummy_input form-control  option_value','autofocus'=>'on','data-name'=>'radio','data-count'=>'add1']) !!}
	</div>
	<div class="js_input_add_checkbox">
		<div class="form-group">
			<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
				{!! Form::text('checkbox',NULL,['class'=>'js_need_regex form-control','autofocus'=>'on','data-name'=>'checkbox','data-count'=>'add1']) !!}
			</div>
			<div class="col-lg-2 col-md-2 col-sm-3 col-xs-3 margin-t-5">
				<span class="med-green js_add_reset form-cursor font600">&nbsp;Reset</span>
			</div>
		</div>		
	</div>		
</div>
<div class="js_question_add hide">
	<div class="form-group-space js_set_question">
		{!! Form::label('question', 'Question', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
		<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 no-padding">
			<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
				{!! Form::text('question[]',NULL,['class'=>'form-control']) !!}
			</div>
			<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_select_ans med-orange">
				<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 no-padding font600">Set Ans :</span>
				<span class="js_input_type med-green form-cursor font600" data-identify="text">Text box</span>&emsp;&emsp;
				<span class="js_input_type med-green form-cursor font600" data-identify="checkbox">Check box</span>&emsp;&emsp;
				<span class="js_input_type med-green form-cursor font600" data-identify="radio">Radio option</span>
				<input type="hidden" name="ques_answer[]" class="ques_answer" />
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_set_answer_type hide">
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding">
					<span class="hide js_box med-orange no-padding font600">Opt :&emsp;
						<span class="js_addmore_option med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} font16 margin-t-5" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></span>
					</span>
					<span class="hide js_text med-orange no-padding font600">Ans :&emsp;</span>
				</div>
				<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 js_add_option"></div>
			</div>
			<!--div class="js_pull_input hide"></div-->
		</div>
		<span class="js_addmore_question med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} font16 margin-t-5" data-placement="bottom"  data-toggle="tooltip"></i></span>&nbsp;<span class="js_delete_question med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.delete')}} font16 margin-l-5" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></span>
	</div>
</div>
<style>
.form-group-space{ margin: 0px;} 
.js_input_add input{ margin: 0px;}
</style>