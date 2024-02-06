@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.superbills')}} font14"></i> Superbills <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>New Superbill Template</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a  href="javascript:void(0)" data-url="{{ url('superbills') }}" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/superbills')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice')
{!! Form::open(['onsubmit'=>"event.preventDefault();",'url'=>'superbills','id'=>'js-bootstrap-validator','class'=>'js_superbill_template medcubicsform']) !!}
<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.superbills") }}' />
<div class="col-md-12 space-m-t-15">
    <div class="box-block">
        <div class="box-body">
            <div class="col-lg-8 col-md-8 col-sm-9 col-xs-12">
                <p class="push">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! HTML::decode(Form::label('template_name', 'Template Name <sup class="med-orange">*</sup>', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label'])) !!} 
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                            {!! Form::text('template_name',null,['class'=>'form-control js-letters-caps-format','name'=>'template_name']) !!}
                            <p class="js_template_name js_error hide"></p>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">
                        {!! HTML::decode(Form::label('provider', 'Provider <sup class="med-orange">*</sup>', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label'])) !!}                                                                                             
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                            {!! Form::select('provider_id', array(''=>'-- Select --')+(array)$providers,  NULL,['class'=>'form-control  select2 js-sel-provider-change','id'=>'provider_id']) !!}
                            <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
                            <p class="js_provider_id js_error hide"></p>
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                    <div class="form-group">
                        {!! HTML::decode(Form::label('Header', 'Header <sup class="med-orange">*</sup>', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label'])) !!}
                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-7">
                            {!! Form::select('header_list', ['office_visit'=>'Office Visit','office_procedures'=>'Office Procedures','laboratory'=>'Laboratory','well_visit'=>'Well Visit','medicare_preventive_services'=>'Medicare preventive services','skin_procedures'=>'Skin Procedures','consultation_preop_clearance'=>'Consultation/preop clearance','vaccines'=>'Vaccines','medications'=>'Medications','other_services'=>'Other Services'],null,['class'=>'form-control select2 js_choose_header','multiple'=>'multiple','autocomplete'=>'off']) !!}
                            <p class="js-sel-provider-type-dis hide no-bottom font12"></p>
							<p class="js_header_list js_error hide"></p>
							<input type="hidden" id="selected_list" />
                        </div>
                        <div class="col-sm-1"></div>
                    </div>

                    <div class="form-group">
                        {!! Form::label('status', 'Status', ['class'=>'col-lg-3 col-md-3 col-sm-3 col-xs-5 control-label']) !!} 
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-7 @if($errors->first('status')) error @endif">
                            {!! Form::radio('status', 'Active',true,['class'=>'flat-red']) !!} Active &emsp; {!! Form::radio('status', 'Inactive',null,['class'=>'flat-red']) !!} Inactive 
                            {!! $errors->first('status', '<p> :message</p>')  !!}
                        </div>
                        <div class="col-sm-1"></div>
                    </div>
                </div>
                </p>
            </div>
        </div><!-- /.box-body -->
    </div>
</div>

<div class="js_orgin">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 box-body">
        <div class="box box-view no-shadow"><!--  Box Starts -->
            <div class="box-header-view margin-b-10">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Selected Header List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive js_add_header"></div>
        </div>
    </div>
    {!! Form::close() !!}
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_search_section hide">
        <div class="box box-view no-shadow"><!--  Box Starts -->
            <div class="box-header-view margin-b-10">
                <i class="fa {{Config::get('cssconfigs.common.search')}}"></i> <h3 class="box-title">CPT Search</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                {!! Form::open(['method'=>'POST','class'=>'search_form','name'=>'search_keyword']) !!}

                <div class="col-lg-8 col-lg-offset-2">
                    <div class="input-group input-group-sm">
                        <input name="search_keyword" type="text" class="form-control" placeholder="Search CPT using key words">
                        <span class="input-group-btn">
                            <button class="btn btn-flat btn-medgreen js_search" type="button">Search</button>
                        </span>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 no-padding margin-t-10">
                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10 col-lg-offset-2 js_add_section hide">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-7 no-padding select2-white-popup @if($errors->first('provider_id')) error @endif">
                            {!! Form::select('selected_list', array(''=>'-- Select --'), NULL,['class'=>'form-control  select2','id'=>'js_drop_down','autocomplete'=>'off']) !!}
                        </div>                        
                        <button class="col-lg-2 col-md-2 col-sm-4 col-xs-4 btn btn-medcubics-small margin-t-0 margin-l-5 js_add" type="button">Attach</button>                        
                    </div>
                </div>
                <div class="box-body col-lg-12 col-md-12 col-sm-12">
                    <div id="js_loading_image" class="box-body overlay col-xs-offset-2 med-green font16 font600 hide">
                        <i class="fa fa-spinner fa-spin med-green"></i>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 js_search_reslut"></div>
                </div>

                {!! Form::close() !!}
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->
    </div>

    <!-- Checked codes get contents here  -->
    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 js_checked_content no-padding hide">
        <ul class="cpt-grid no-padding line-height-26 margin-b-4" style="list-style-type:none;">
            <li class="superbill js_count">
                <table class="table-striped-view">
                    <tbody>
                        <tr>
                            <td style="width: 1%"></td>
                            <td style="width: 74%;font-size:11px;" class="js_checked_content_text"></td>
                            <td style="width: 15%" class="js_all_code js_checked_content_code"></td>
                            <td class="bg-white" style="width: 15%;">
                                <i class="fa {{Config::get('cssconfigs.common.times-circle')}} modal-icon remove_selected_icds" data-original-title="Delete" data-toggle="tooltip" data-placement="bottom" style="cursor: pointer;" ></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="all_html_values[]" class="all_html_values" id="all_html_values" />
            </li>
        </ul>
    </div>
    <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 text-center js_genarate">        
        <button class="btn  btn-medcubics" id='js_show_template'>Show template</button>
        <a href="{{ url('superbills')}}">{!! Form::button('Cancel', ['class'=>'btn  btn-medcubics']) !!}</a>        
    </div>
</div>
{!! Form::open(['method'=>'POST','onsubmit'=>"event.preventDefault();",'class'=>'all_values','name'=>'all_values']) !!}
<div class="all_values_input"></div>
{!! Form::close() !!}
<div class="js_prev_template hide"></div>
@stop