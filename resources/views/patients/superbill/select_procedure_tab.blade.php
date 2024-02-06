{!! Form::hidden('selected_codes_cpts_arr',null,['class'=>'form-control input-sm','id'=>'selected_codes_cpts_arr']) !!}
{!! Form::hidden('temp_popup_icds_val',null,['class'=>'form-control input-sm','id'=>'temp_popup_icds_val']) !!}
{!! Form::hidden('temp_popup_cpt_val',null,['class'=>'form-control input-sm','id'=>'temp_popup_cpt_val']) !!}

<div id="popup_icd_modal" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="popup_icds_close();" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">ICD List - <span id="popup_cpt_title"></span></h4>
            </div>
            <div id="popup_icd_check_validation" style="display:none;">
                <small class="help-block" data-bv-result="INVALID" data-bv-for="document_title" data-bv-validator="notEmpty" style="color:#a94442;padding-left: 20px; padding-top: 10px;">Select any one ICD!</small>
            </div>
            <div class="modal-body form-horizontal">

            </div>
            <div class="modal-footer">
                <a class="btn btn-medcubics-small" onclick="popup_icds_save();">Submit</a>
                <a class="btn btn-medcubics-small" onclick="popup_icds_reset();">Reset</a>
                <a class="btn btn-medcubics-small" onclick="popup_icds_close();">Close</a>
            </div>
        </div>
    </div>
</div>


<div class="tab-pane" id="select_procedure">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">                            
        <div class="box box-view-border no-shadow no-border yes-border"><!--  Box Starts -->
            <div class="box-header-view-white">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom no-padding m-b-m-20">                                  

                    <div class="col-lg-4 col-md-7 col-sm-7 col-xs-7 padding-t-20">
                        <h5><span style="background: #f07d08; color:#fff; padding: 2px 10px; border-radius: 4px;">DOS : <span id="dos_seleted_display">MM/DD/YYYY</span></span> <span style="background: #4fc5cc; color:#fff; padding: 2px 10px; border-radius: 4px;">Provider : <span id="providername_seleted_display"></span></span></h5>
                    </div> 

                    <div class="form-group" style="margin-bottom: 0px;">                                 
                        <div class="col-lg-3 col-md-5 col-sm-5 col-xs-5 superbill-select2">
                            {!! Form::label('Template', 'Choose Template') !!}
                            {!! Form::select('templates_id', array(''=>'-- Select --'),  null,['class'=>'form-control input-sm select2','id'=>'templates_id']) !!}  
                        </div>                                
                    </div>                                                                                 

                </div><!-- Col 12 Ends -->                                
            </div><!-- /.box-header -->
        </div><!-- Box Ends -->

        <div class="box box-view-border no-shadow no-bottom"><!--  Box Starts -->
            <div class="box-header-view">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Selected Codes</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  mobile-scroll">
                    <div id="no_cpt_codes_display_part"><p class="text-center med-gray font14">No Codes Selected</p></div>
                    <div id="selected_cpt_codes_display_part"></div>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->
        <span id='selected_codes_cpts_arr_err' style='display:none;' class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='selected_codes_cpts_arr_err' data-bv-result='INVALID'>Select atleast one code!</span>



        <div class="box box-view-border no-shadow margin-t-20"><!--  Box Starts -->
            <div class="box-header-view">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Templates</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <div id="selected_templates_display_part"></div>
                
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->

        <div class="box box-view-border no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">CPT Search</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <div class="input-group input-group-sm">
                        <input name="search_cpt_keyword" type="text" class="form-control js_search_cpt_list" placeholder="Search CPT using key words">
                        <span class="input-group-btn">
                            <button class="btn btn-flat btn-medgreen js_search_cpt_button" type="button">Search</button>
                        </span>
                    </div>
                    <span id='search_cpt_keyword_err' class='help-block med-red hide pull-left' data-bv-validator='notEmpty' data-bv-for='search_cpt_keyword_err' data-bv-result='INVALID'><span id="search_cpt_keyword_err_content">Please enter search keyword!</span></span>
                </div>
				
				<div id="js_loading_image_cpt" class="box-body col-lg-12 col-md-12 col-sm-12 hide">
					<div class="box-body overlay col-xs-offset-2 med-green font16 font600">
						<i class="fa fa-spinner fa-spin med-green"></i>
					</div>
				</div>
                <span id="cpt_imo_search_part">
                </span>

            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->
    </div><!-- /.box-body -->
    <div class="box-footer">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <a onclick="form_supmit('procedure_tab');">{!! Form::button('Next', ['class'=>'btn btn-medcubics pull-right']) !!}</a>
            <a href="javascript:void(0)" data-url="#select_icd">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics pull-left js_cancel_site']) !!}</a>
        </div>
    </div><!-- /.box-footer -->
</div><!-- /.tab-pane -->
