<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.questionaire") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20"><!--  Left side Content Starts -->   
    <div class="box no-shadow"><!-- General Information Box Starts -->
        <div class="box-block-header">
            <i class="livicon" data-name="info"></i> <h3 class="box-title"> General Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
    <div class="box-body form-horizontal">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">
			<div class="form-group-space">
				{!! Form::label('header', 'Title', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                <div class="form-group col-lg-4 col-md-5 col-sm-6 col-xs-10 no-padding">
					{!! Form::text('title',$title,['class'=>'form-control']) !!}
					<p class="js_title js_error hide"></p>
					<input type="hidden" id="js_title_exist" />
				</div>
			</div> 
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding form-horizontal">
			<div id="sortable" class="js_question cur-move">
			<?php $text_cnt = 0; $radio_cnt = 0; $check_cnt = 0; ?>
			@if(count($questionnaries)>0)
				@foreach($questionnaries as $list_key => $list_val)
				@if($list_val->answer_type =="text")
					<?php $class = "text"; $value = "Text Box";$text_cnt++;$input_count = $text_cnt; ?>
				@elseif($list_val->answer_type =="checkbox")
				<?php $class = "checkbox"; $value = "Check Box";$check_cnt++;$input_count = $check_cnt; ?>
				@elseif($list_val->answer_type =="radio")
				<?php $class = "radio"; $value = "Radio Option";$radio_cnt++;$input_count = $radio_cnt; ?>
				@endif
				@if(count(@$list_val->questionnaries_option) == 0)<?php   $class = "";  ?> @endif
				<div class="form-group-space js_set_question" id="js_question_{{ $list_val->id }}" data-count="{{ $list_val->id }}">
					{!! Form::label('question', 'Question', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
					<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 no-padding">
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							{!! Form::text('question[]',$list_val->question,['class'=>'form-control']) !!}
						</div>
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_select_ans med-orange @if(count(@$list_val->questionnaries_option)>0) hide @endif">
							<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 no-padding">Set Ans :</span>
							<span class="js_input_type med-green form-cursor" data-identify="text">Text box</span>&emsp;&emsp;
							<span class="js_input_type med-green form-cursor" data-identify="checkbox">Check box</span>&emsp;&emsp;
							<span class="js_input_type med-green form-cursor" data-identify="radio">Radio option</span>
							<input type="hidden" name="ques_answer[]" class="ques_answer" value="{{ $class }}" />
						</div>
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_set_answer_type @if(count(@$list_val->questionnaries_option) == 0) hide @endif">
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding">
								<span class='js_box med-orange @if($class =="text")hide @endif font600'>Opt :&emsp;
									<span class="js_addmore_option med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} margin-t-8 font16" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></span>
								</span>
								<span class='@if($class !="text")hide @endif js_text med-orange font600'>Ans :&emsp;</span>
							</div>
							<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 js_add_option">
								<?php $option_count = 1; ?>
								@foreach($list_val->questionnaries_option as $list_ans_key => $list_ans_val)
								<div class="form-group" id="js_{{$list_ans_val->id}}">
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
										<input type="text" name="{{ $class }}" value="{{($class !='text') ? @$list_ans_val->option : '' }}" class="js_need_regex form-control {{ ($class !='text') ? 'option_value' : ''}}" style="border: 1px solid #ccc;float:none;" data-name="{{$class}}" data-count="{{$list_ans_val->id}}" @if($class =='text') readonly @else '' @endif  />
									</div>
									<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 margin-t-2">	
										<span class='med-green js_delete_record form-cursor font600' @if($option_count == "1") data-from="delete_all_option" data-id="{{$list_val->id}}" @else data-from="delete_single_option" data-id="{{$list_ans_val->id}}" @endif>@if($option_count == "1")&nbsp;Reset @else &nbsp;<i class="fa {{Config::get('cssconfigs.common.times-circle')}} font16 margin-t-2" data-placement="bottom" data-toggle="tooltip" data-original-title="Remove"></i>@endif</span>
									</div>
								</div>
								<?php $option_count++; ?>
								@endforeach
							</div>
						</div>
					</div>
					<span class="js_addmore_question med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} margin-t-8 font16" data-placement="bottom" data-toggle="tooltip"></i></span><span class="hide js_delete_record med-green form-cursor" data-from="delete_question" data-id="{{$list_val->id}}">&nbsp;<i class="fa {{Config::get('cssconfigs.common.delete')}} font16 margin-t-5" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></span>
				</div>
				@endforeach
				@else
				<div id="sortable" class="js_question cur-move">
					<div class="form-group js_set_question" id="js_question_1" data-count="1">
						{!! Form::label('question', 'Question', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}                                                                                             
						<div class="col-lg-4 col-md-5 col-sm-6 col-xs-10">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
								{!! Form::text('question[]',NULL,['class'=>'form-control']) !!}
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_select_ans med-orange">
								<span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 no-padding">Set Ans :</span>
								<span class="js_input_type med-green form-cursor" data-identify="text">Text box</span>&emsp;&emsp;
								<span class="js_input_type med-green form-cursor" data-identify="checkbox">Check box</span>&emsp;&emsp;
								<span class="js_input_type med-green form-cursor" data-identify="radio">Radio option</span>
								<input type="hidden" name="ques_answer[]" class="ques_answer" />
							</div>
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_set_answer_type hide">
								<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 no-padding">
									<span class="hide js_box med-orange no-padding font600">Opt :&emsp;
										<span class="js_addmore_option med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></span>
									</span>
									<span class="hide js_text med-orange no-padding font600">Ans :&emsp;</span>
								</div>
								<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 js_add_option no-padding"></div>
							</div>
							<div class="js_pull_input hide"></div>
						</div>
						<span class="js_addmore_question med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}" data-placement="bottom"  data-toggle="tooltip"></i></span><span class="js_delete_question med-green form-cursor hide">&nbsp;<i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></span>
					</div>
				</div>
				@endif
				
			</div>
		</div>
	</div>
		
    </div><!-- General info box Ends-->
    <div class="js_pull_input hide"></div>
	 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center error">
       <p class="js_common_error"></p>
	</div>
    <div class="col-lg-11 col-md-12  col-sm-12 col-xs-12 text-center">
			{!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics js_form_submit']) !!}
			@if($checkpermission->check_url_permission('questionnaire/template/quesansdelete') == 1)
			<a class="btn btn-medcubics js-delete-confirm" data-text="Are you sure would you like to delete?" href="{{ url('questionnaire/template/'.$id.'/delete') }}">Delete</a>
			@endif
			<a href="javascript:void(0)" data-url="{{ url('questionnaire/template/'.$id)}}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>			   
		</div>	
    
    </div>
</div><!--  Left side Content Ends -->  
  
{!! Form::close() !!}

{!! Form::open(['method'=>'POST','class'=>'all_values','name'=>'all_values']) !!}
<input type="hidden" name="delete_id" class="js_set_delete_id" />
<input type="hidden" name="delete_from" class="js_set_delete_from" />
{!! Form::close() !!}

<div id="conform_delete" class="modal fade">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Alert</h4>
            </div>
            <div class="modal-body text-center font600 med-green">
                {!! trans("practice/practicemaster/questionnaries.validation.question_delete") !!}
            </div>
            <div class="modal-footer">
                <button class="confirm btn btn-medcubics-small" type="button" data-dismiss="modal">Yes</button>
                <button class="cancel btn btn-medcubics-small" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>