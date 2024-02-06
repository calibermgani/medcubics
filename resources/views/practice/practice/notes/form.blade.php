<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

    <div class="box box-info no-shadow no-bottom">
        
        <!-- form start -->
        <div class="box-body form-horizontal">
             <div class="form-group">
                    {!! Form::label('Title', 'Title', ['class'=>'col-lg-12 col-md-12 col-sm-12 control-label star']) !!} 
                    <div class="col-lg-12 col-md-12 col-sm-12 @if($errors->first('title')) error @endif">
                        {!! Form::text('title',null,['class'=>'form-control', 'autocomplete'=>'off']) !!}
                        {!! $errors->first('title', '<p> :message</p>')  !!}
                    </div>
                </div>                       
            <div class="form-group">
                {!! Form::label('content', 'Content', ['class'=>'col-lg-12 col-md-12 col-sm-12 control-label star']) !!} 
                <div class="col-lg-12 col-md-12 col-sm-12 @if($errors->first('content')) error @endif" style="padding-right: 12px;">
                    {!! Form::textarea('content',null,['class'=>'form-control','style'=>'height:120px;']) !!}
                    {!! $errors->first('content', '<p> :message</p>')  !!}
                </div>
            </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
				{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics']) !!}
				<?php $currnet_page = Route::getFacadeRoot()->current()->uri(); ?>
				@if(strpos($currnet_page, 'facility') !== false)
					<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.facility_notes") }}' />
					<a href="javascript:void(0)" data-url="{{ url('facility/'.$facility->id.'/notes') }}"> 
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics', 'data-dismiss'=>'modal']) !!}
					</a>
				@elseif(strpos($currnet_page, 'employer') !== false)
					<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.employer_notes") }}' />
					<a href="javascript:void(0)" data-url="{{ url('employer/'.$employer->id.'/notes') }}"> 
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics','data-dismiss'=>'modal']) !!}
					</a>
				@elseif(strpos($currnet_page, 'provider') !== false)
					<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.provider_notes") }}' />
					<a href="javascript:void(0)" data-url="{{ url('provider/'.$provider->id.'/notes') }}"> 
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics', 'data-dismiss'=>'modal']) !!}
					</a>
				@elseif(strpos($currnet_page, 'notes') !== false)
					<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.practice_notes") }}' />
					<a href="javascript:void(0)" data-url="{{ url('notes') }}"> 
						{!! Form::button('Cancel', ['class'=>'btn btn-medcubics','data-dismiss'=>'modal']) !!}
					</a>
				@else
					{!! Form::button('Cancel', ['class'=>'btn btn-medcubics', 'data-dismiss'=>'modal']) !!}
				@endif
			</div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->