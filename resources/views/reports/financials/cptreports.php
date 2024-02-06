@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="barchart"></i> Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Collection Reports <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Adjustment Analysis - Detailed</span></small>
        </h1>
        <ol class="breadcrumb">
            <!-- <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li> -->
            <li><a href="{{ url('reports/collections/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li class="dropdown messages-menu hide js_claim_export"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'reports/adjustment/export/'])
                <input type="hidden" name="report_controller_name" value="ReportController" />
                <input type="hidden" name="report_controller_func" value="adjustmentSearchexport" />
                <input type="hidden" name="report_name" value="Adjustment Analysis - Detailed" />
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/adjustment_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop  


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-view no-shadow ">
            <div class="box-body yes-border" style="border-color:#85E2E6;border-radius: 0px 0px 4px 4px;">
                {!! Form::open(['onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator_edts', 'name'=>'medcubicsform', 'url'=>'reports/search/adjustment', 'data-url'=>'reports/search/adjustment']) !!}

                @php
					$rendering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering'); 
					$billing_provider 	= App\Models\Provider::typeBasedAllTypeProviderlist('Billing'); 
					$reffering_provider = App\Models\Provider::typeBasedAllTypeProviderlist('Referring'); 
				@endphp 
			
                 @include('layouts.search_fields', ['search_fields'=>$search_fields])                

                <div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <!--<div class="form-group">
                                {!! Form::label('Transaction Date', 'Transaction Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('date_option',['enter_date' => 'Choose Date','daily' => 'Today','current_month'=>'Current Month','previous_month'=>'Previous Month','current_year'=>'Current Year','previous_year'=>'Previous Year'],@$report_data['date_option'],['class'=>'select2 form-control js_change_date_option','tabindex'=>'1']) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('From', 'From Date', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('from_date', @$report_data['from_date'],['class'=>'search_start_date form-control datepicker dm-date','tabindex'=>'2','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>  
                            
                            <div class="form-group">
                                {!! Form::label('Adjustment Type', 'Adjustment Type', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance_type', ['all'=>'All','insurance' => 'Insurance','patient' => 'Patient'],@$report_data['insurance_type'],['class'=>'select2 form-control js_select_basis_change_adjusment js_select_change_adj','tabindex'=>'4', 'id' => "js_ins_adj_typ"]) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('Insurance', 'Insurance', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('insurance', [''=>'All']+(array)@$insurance,@$report_data['insurance'],['class'=>'select2 form-control','tabindex'=>'6', 'id' => "js-insurance-adj"]) !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('Billing', 'Billing', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">								
                                    {!! Form::select('billing_provider_id',['all'=>'All']+(array)$billing_provider,@$report_data['billing_provider_id'],['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'8']) !!}
                                </div>                        
                            </div>                           

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group  margin-b-18 hidden-sm hidden-xs">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">
                                    {!! Form::label('', '', ['class'=>'control-label']) !!}

                                </div>                                                        
                            </div> 

                            <div class="form-group">
                                {!! Form::label('To', 'To Date', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::text('to_date', @$report_data['to_date'],['class'=>'search_end_date form-control datepicker dm-date','tabindex'=>'3','placeholder'=>Config::get('siteconfigs.default_date_format')])  !!}
                                </div>                        
                            </div>
                            
                            <div class="form-group">
                                {!! Form::label('Adjustment Reason', 'Adjustment Reason', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('adjustment_reason_id',['Patient'=>'All']+(array)@$adj_reason_patient,null,['class'=>'select2 form-control js_patient_aging js_all_hide_col','disabled']) !!}
                                    {!! Form::select('adjustment_reason_id',['Insurance'=>'All']+(array)@$adj_reason_ins,null,['class'=>'select2 form-control js_insurance_aging js_all_hide_col hide','disabled']) !!}
                                    {!! Form::select('adjustment_reason_id',['all'=>'All'],null,['class'=>'select2 form-control js_all_aging js_all_hide_col hide','disabled','tabindex'=>'5']) !!}
                                </div>                        
                            </div>

                            <div class="form-group">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('facility_id',['all'=>'All']+(array)@$facilities, @$report_data['facility_id'],['class'=>'select2 form-control js_individual_select','id'=>"js_facility",'tabindex'=>'7']) !!}
                                </div>                        
                            </div>  
                            
                            <div class="form-group">
                                {!! Form::label('Rendering', 'Rendering', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-3 control-label']) !!}
                                <div class="col-lg-7 col-md-6 col-sm-6 col-xs-9">
                                    {!! Form::select('rendering_provider_id',['all'=>'All']+(array)$rendering_provider,@$report_data['rendering_provider_id'],['class'=>'select2 form-control js_individual_select','id'=>"js_provider",'tabindex'=>'9']) !!}
                                </div>                        
                            </div>-->

                            

                       <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
                            <input class="btn btn-medcubics-small js_filter_search_submit pull-left" value="Generate Report" type="submit">
                        </div>
                        </div>

                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


</div>
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part_report" value="Exit" type="button">
</div>

@stop
@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script>   
   var wto = '';
	var url = $('#js-bootstrap-searchvalidator_edts').attr("action");
	$(document).ready(function(){
		getMoreFieldData();
		$("#insurance_id").hide();
		$('#adjustment_reason_id').find('option').remove();
		$('select#adjustment_reason_id').attr('disabled',true);   
	});
		
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = '';
		wto = setTimeout(function() {  
			$('select.auto-generate:visible').each(function(){
				data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';           
			});                                                    
			$('input.auto-generate:visible').each(function(){
				data_arr += $(this).attr('name')+'='+$(this).val()+'&';
			});
			
			final_data = data_arr+"_token="+$('input[name=_token]').val(); 
			getAjaxResponse(url, final_data);
		}, 100);
	}
	/* function for get data for fields End */

	/* Onchange code for field Start */
	$(document).on('click','.js_filter_search_submit',function(){
		getData();
	});
	/* Onchange code for field End */ 

	/* Onchange code for more field Start */
	$(document).on('change','select.more_generate',function(){ 
		getMoreFieldData();
	});


	$("#insurance_type.js_select_basis_change").on("click",function(){
		/*$("#facility_id").hide();
		$("#billing_provider_id").hide();
		$("#rendering_provider_id").hide();
		$("#aging_insurance_id").hide();*/
		if($(this).val() == 'insurance'){
			$("#insurance_id").show();
			$('select#adjustment_reason_id').attr('disabled',false);
			$('select#adjustment_reason_id').append('<option value="Insurance">All</option><option value="0">CO253</option><option value="CO45">CO45</option><?php foreach($adj_reason_ins as $key=>$val){ echo '<option value="'.$key.'">'.trim($val).'</option>'; }?>');
			$("select#adjustment_reason_id option[value='Patient']").remove(); 
			$("select#adjustment_reason_id option[value='all']").remove(); 
			<?php foreach($adj_reason_patient as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?>
		}else if($(this).val() == 'patient'){
			$("#insurance_id").hide();
			$('select#adjustment_reason_id').attr('disabled',false);
			$('select#adjustment_reason_id').append('<option value="Patient">All</option><?php foreach($adj_reason_patient as $key=>$val){ echo '<option value="'.$key.'">'.trim($val).'</option>'; }?>'); 
			$("select#adjustment_reason_id option[value='Insurance']").remove(); 
			$("select#adjustment_reason_id option[value='0']").remove(); 
			$("select#adjustment_reason_id option[value='CO45']").remove(); 
			$("select#adjustment_reason_id option[value='all']").remove(); 
			<?php foreach($adj_reason_ins as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?>
		}else if($(this).val() == 'all'){
		  
			$("select#adjustment_reason_id option[value='Insurance']").remove();
			$("select#adjustment_reason_id option[value='0']").remove();
			$("select#adjustment_reason_id option[value='CO45']").remove();     
			<?php foreach($adj_reason_ins as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?> 

			$("select#adjustment_reason_id option[value='Patient']").remove();     
			<?php foreach($adj_reason_patient as $key=>$val){ ?> 
			$("select#adjustment_reason_id option[value='<?php echo $key;?>']").remove();
			<?php }?>
			$('select#adjustment_reason_id').attr('disabled',true);
			 $("#insurance_id").hide();
		}
	//console.log($(this).val());
	})

</script>
@endpush  
