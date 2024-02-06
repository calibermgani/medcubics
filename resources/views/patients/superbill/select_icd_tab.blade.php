{!! Form::hidden('selected_codes_ids_arr',null,['class'=>'form-control input-sm','id'=>'selected_codes_ids_arr']) !!}
<div class="tab-pane" id="select_icd">
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow no-border yes-border"><!--  Box Starts -->
            <div class="box-header-white bg-white">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-bottom no-padding margin-b-10">

                    <div class="form-group no-bottom">                                 
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-8" >
                            {!! Form::label('DOS', 'DOS',['class'=>'control-label med-green font600 m-b-m-5']) !!}
                            <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon" style="margin-top:23px; margin-left:-41px;"></i>
                            {!! Form::text('date_of_service',null,['class'=>'form-control dm-date input-sm-header-billing  ','id'=>'date_of_service','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                            <span id='date_of_service_err' style='display:none;' class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='date_of_service' data-bv-result='INVALID'>Select Valid DOS!</span>
                        </div>                                
                    </div>

                    <div class="form-group no-bottom">                                 
                        <div class="col-lg-2 col-md-4 col-sm-6 col-xs-8 select2-white-popup ">
                            {!! Form::label('Provider', 'Provider',['class'=>'control-label med-green font600 m-b-m-5']) !!}
                            {!! Form::select('providers_id', array(''=>'-- Select --')+(array)$providers,  null,['class'=>'form-control select2 js-sel-provider-change','id'=>'providers_id']) !!}  

                            <span id='providers_id_err' style='display:none;' class='help-block med-red' data-bv-validator='notEmpty' data-bv-for='providers_id' data-bv-result='INVALID'>Select Provider!</span>
                        </div> 
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-8 ">
                            {!! Form::label('', '',['class'=>'control-label m-b-m-5']) !!}
                            <p class="no-bottom font13 js-sel-provider-type-dis hide"></p>
                        </div>
                    </div>                                                                                 

                </div>                                
            </div><!-- /.box-header -->
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-b-10">                                  
        <div class="box box-view-border no-shadow no-bottom"><!--  Box Starts -->
            <div class="box-header-view">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Selected Codes</h3>                
            </div><!-- /.box-header -->
            <div class="box-body table-responsive superbill-extra">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                    <div id="no_codes_display_part"><p class="text-center med-gray font14">No Codes Selected</p></div>
                    <div id="selected_codes_display_part"></div>
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->
        <span id='selected_codes_ids_arr_err' style='display:none;' class='help-block med-red margin-b-20' data-bv-validator='notEmpty' data-bv-for='selected_codes_ids_arr_err' data-bv-result='INVALID'>Select atleast one code!</span>             
    </div><!-- /.box-body -->
    
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 margin-b-10">
        <div class="box box-view-border no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">Existing Codes</h3>               
            </div><!-- /.box-header -->
            <div class="box-body table-responsive superbill-extra">
                <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                    @if (count($existing_icds_arr) > 0)
                    @foreach ($existing_icds_arr as $existing_icds_det)
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
                            <li class="superbill">
                                <table class="table-striped-view">
                                    <tbody>
                                        <tr>
                                            <td class="padding-0-4 td-c-5">
                                                <?php $unique_icd_class_name = 'icd_'.str_replace('.', '_', $existing_icds_det['icd_code']); ?>
                                                {!! Form::checkbox('existing_icds[]', $existing_icds_det['id'], null, ['class'=>"chk flat-red",'data-id'=>"$unique_icd_class_name"]) !!}
                                            </td>                                                
                                            <td style="width: 82%">{!! $existing_icds_det['short_description'] !!}</td>
                                            <td style="width: 13%">{!! $existing_icds_det['icd_code'] !!}</td>
                                        </tr>
                                    </tbody>
                                </table>                                     
                            </li>	
                        </ul>
                    </div>
                    @endforeach
                    @else
                    <p class="text-center font14 med-gray">No existing codes available</p>
                    @endif
                </div>
            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->
    </div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20">
        <div class="box box-view-border no-shadow"><!--  Box Starts -->
            <div class="box-header-view">
                <i class="livicon" data-name="info"></i> <h3 class="box-title">ICD-10 Search</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">

                <div class="col-lg-8 col-lg-offset-2 text-center">
                    <div class="input-group input-group-sm">
                        <input name="search_icd_keyword" type="text" class="form-control js_search_icd_list" placeholder="Search ICD using key words">
                        <span class="input-group-btn">
                            <button class="btn btn-flat btn-medgreen js_search_icd_button" type="button">Search</button>
                        </span>
                    </div>
                    <span id='search_icd_keyword_err' class='help-block pull-left med-red hide' data-bv-validator='notEmpty' data-bv-for='search_icd_keyword_err' data-bv-result='INVALID'>
                        <span id="search_icd_keyword_err_content">Please enter search keyword!</span></span>
                </div>
				
				<div id="js_loading_image_icd" class="box-body col-lg-12 col-md-12 col-sm-12 hide">
					<div class="box-body overlay col-xs-offset-2 med-green font16 font600">
						<i class="fa fa-spinner fa-spin med-green"></i>
					</div>
				</div>
                <span id="icd_imo_search_part">
                </span>

            </div><!-- /.box-body -->
        </div><!-- /.box Ends-->
    </div>
    <div class="box-footer">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <a onclick="form_supmit('icd_tab');">{!! Form::button('Next', ['class'=>'btn btn-medcubics pull-right']) !!}</a>
            <a href="javascript:void(0)" data-url="{{ url('patients/'.$patient_id.'/superbill/create') }}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics pull-left js_cancel_site']) !!}</a>
        </div>
    </div><!-- /.box-footer -->
</div><!-- /.tab-pane -->