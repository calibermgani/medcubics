<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">	
		<div class="box-info no-shadow js-template">  
			@foreach($selected_variable as $selected_variable)
			@if((preg_match('/date/',strtolower($selected_variable-> value)) == 1) || $selected_variable-> input_types =="date" || (preg_match('/DOB/',strtolower($selected_variable->label)) == 1) )
				@if((preg_match('/today/',strtolower($selected_variable->key)) == 1) ||(preg_match('/current/',strtolower($selected_variable->key)) == 1))
					<div class="form-group">
						{!! Form::label($selected_variable->key, $selected_variable->label,['class'=> 'col-lg-4 col-md-4 col-sm-4 control-label']) !!} 
						<div class="col-lg-6 col-md-6 col-sm-6"> 
							{!! Form::text($selected_variable->value,date('m/d/Y'),['id'=>$selected_variable->key,'class'=>'form-control col-lg-6 col-md-6 col-sm-6 js_field','readonly'=>'readonly']) !!}
							<small id="js_{{$selected_variable->key}}" class="help-block js_error_msg med-red"></small>
						</div>
						<div class="col-sm-1"></div>
					</div>
				@else
					<div class="form-group">
						{!! Form::label($selected_variable->key, $selected_variable->label,['class'=> 'col-lg-4 col-md-4 col-sm-4 control-label']) !!}  
						<div class="col-lg-6 col-md-6 col-sm-6">
							{!! Form::text($selected_variable->value,null,['id'=>$selected_variable->key,'class'=>'form-control dm-date col-lg-6 col-md-6 col-sm-6 js_field form-cursor dob','placeholder'=>'mm/dd/yyyy']) !!}
							<small id="js_{{ $selected_variable->key }}" class="help-block js_error_msg med-red"></small>
						</div>
						<div class="col-sm-1"></div>
					</div>
				@endif
			@elseif((preg_match('/insurance/',$selected_variable->key) == 1)&& $selected_variable->input_types == "select")
				<div class="form-group">
					{!! Form::label($selected_variable->key, $selected_variable->label,['class'=> 'col-lg-4 col-md-4 col-sm-4 control-label']) !!}                                  
					<div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">  
						{!! Form::select($selected_variable->value, array('' => '-- Select --') + (array)$insurances ,null,['class'=>'select2 form-control js_field','id'=>$selected_variable->key ]) !!}
						<small id="js_{{$selected_variable->key}}" class="help-block js_error_msg med-red"></small>
					</div>
				</div>
			@elseif (((preg_match('/id/',$selected_variable->key) == 1) || (preg_match('/num/',$selected_variable->key) == 1)) || $selected_variable->input_types =="number" )
				<div class="form-group">
					{!! Form::label($selected_variable->key, $selected_variable->label,['class'=> 'col-lg-4 col-md-4 col-sm-4 control-label']) !!}                            
					<div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">
						{!! Form::text($selected_variable->value,null,['id'=>$selected_variable->key,'class'=>'form-control col-lg-6 col-md-6 col-sm-6 js-number js_field dm-ssn',]) !!}
						<small id="js_{{$selected_variable->key}}" class="help-block js_error_msg med-red"></small>
					</div>
				</div>
			@elseif (((preg_match('/patientname/',$selected_variable->key) == 1) || (preg_match('/patient_name/',$selected_variable->key) == 1)) && $selected_variable->input_types =="text" )
			<div class="form-group">       
					<div class="col-lg-6 col-md-6 col-sm-7 col-xs-10">  
						{!! Form::hidden($selected_variable->value,$patients->last_name." ".$patients->first_name." ".$patients->middle_name,['id'=>$selected_variable->key,'class'=>'form-control col-lg-6 col-md-6 col-sm-6 js_field']) !!}
						<small id="js_{{$selected_variable->key}}" class="help-block js_error_msg med-red"></small>
					</div>
				</div>
			@else
				<div class="form-group"> 
					{!! Form::label($selected_variable->key, $selected_variable->label,['class'=> 'col-lg-4 col-md-4 col-sm-4 control-label']) !!} 
					<div class="col-lg-6 col-md-6 col-sm-6">
						{!! Form::text($selected_variable->key,null,['id'=>$selected_variable->key,'class'=>'form-control col-lg-6 col-md-6 col-sm-6 js_field','maxlength'=>"25"]) !!}
						<small id="js_{{$selected_variable->key}}" class="help-block js_error_msg med-red"></small>
					</div>
					<div class="col-sm-1"></div>
				</div>
				@endif
				<p class="total_id hide">{{ $selected_variable->key }}.</p>
			@endforeach
			@if(@$selected_variable)
				<div class="form-group">  
					{!! Form::button("Generate",['class'=>'col-lg-2 col-md-2 col-sm-2 js_generate add-btn no-border','id'=>'generate']) !!}
				</div>
			@else
				<input type="hidden" value="show" id="view-source" />
			@endif
				
		</div>
	</div>
</div>

<div class="form-group">
	<div class='col-lg-12 col-md-12 col-sm-12 hide'>
	{!! Form::textarea('content',@$templates_text,['class'=>'form-control ','name'=>'content','id'=>"editor1"]) !!}
	{!! $errors->first('content', '<p> :message</p>')  !!}
	</div>
	<div class="col-sm-1"></div>
</div>