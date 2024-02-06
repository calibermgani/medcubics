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
                        {!! Form::text('title',null,['class'=>'form-control', 'autocomplete'=>'off']) !!}
                        <p class="js_title js_error hide"></p>
                        <input type="hidden" id="js_title_exist" />
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding form-horizontal">
                    <div id="sortable" class="js_question cur-move">
                        <div class="form-group-space js_set_question" id="js_question_1" data-count="1">                                           
                            {!! Form::label('question', 'Question', ['class'=>'col-lg-2 col-md-2 col-sm-3 col-xs-12 control-label star']) !!}
                            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-10 no-padding">
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                    {!! Form::text('question[]',NULL,['class'=>'form-control', 'autocomplete'=>'off']) !!}
                                </div>
                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_select_ans med-orange">
                                    <span class="col-lg-3 col-md-3 col-sm-3 col-xs-3 no-padding font600">Set Ans :</span>
                                    <span class="js_input_type med-green form-cursor font600" data-identify="text">Text box</span>&emsp;&emsp;
                                    <span class="js_input_type med-green form-cursor font600" data-identify="checkbox">Check box</span>&emsp;&emsp;
                                    <span class="js_input_type med-green form-cursor font600" data-identify="radio">Radio option</span>
                                    <input type="hidden" name="ques_answer[]" class="ques_answer" />
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding js_set_answer_type hide">
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 no-padding">
                                        <span class="hide js_box med-orange no-padding font600">Opt :&emsp;
                                            <span class="js_addmore_option med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} margin-t-8 font16" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></span>
                                        </span>
                                        <span class="hide js_text med-orange no-padding font600">Ans :&emsp;</span>
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 js_add_option"></div>
                                </div>
                            </div>
                            <span class="js_addmore_question med-green form-cursor"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}} margin-t-8 font16" data-placement="bottom"  data-toggle="tooltip"></i></span><span class="js_delete_question med-green form-cursor hide">&nbsp;<i class="fa fa-trash font16 margin-t-5" data-placement="bottom"  data-toggle="tooltip" data-original-title="Delete"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="js_pull_input hide"></div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center error">
            <p class="js_common_error"></p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
            {!! Form::submit($submitBtn, ['class'=>'js_form_submit btn btn-medcubics']) !!}
            <a href="javascript:void(0)" data-url="{{ url('questionnaire/template') }}"> {!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        </div>	
    </div><!-- General info box Ends-->
</div>
{!! Form::close() !!}