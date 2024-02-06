@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/cpt/cpt-tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10 margin-t-10">
    <a href="{{url('cpt/'.@$id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Procedure Description Col-12 Starts -->
    <div class="box no-shadow margin-b-10">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="doc-portrait"></i> <h3 class="box-title">Procedure Description</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10">
            <div class="form-group">                
                {!! Form::label('short_description', 'Short Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label','readonly']) !!}                            
                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <p class="show-border no-bottom">{{ @$cpt->short_description }}</p>
                </div>                                           
            </div>

            <div class="form-group">                
                {!! Form::label('long_description', 'Long Description', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label']) !!}
                <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
                    <p class="show-border no-bottom">{{ @$cpt->long_description }}</p>                            
                </div>
            </div>

        </div>               
    </div>
</div><!-- Procedure Description Col-12 Ends -->

<input type="hidden" name="multiFeeScheduleCptID" value="{{ @$cpt->id }}" />
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- Codes Col Starts -->
    <div class="box no-shadow margin-b-10">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="code"></i> <h3 class="box-title">Codes</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body  form-horizontal margin-l-10 p-b-20">
			 
			 <div class="form-group">
                {!! Form::label('Procedure Category', 'Procedure Category', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
					<p class="show-border no-bottom">{{@$cpt->pro_category->procedure_category}}</p>	
                </div>	
                <div class="col-sm-1"></div>
            </div>
			
            <div class="form-group">        
                {!! Form::label('type_of_service', 'Type of service', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->type_of_service }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">
                {!! Form::label('POS', 'POS', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->pos->code }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>    

            <div class="form-group">
                {!! Form::label('applicable_sex', 'Applicable Sex', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
                @if($cpt->applicable_sex == 'Male')  
					{!! Form::radio('applicable_sex', 'Male',true,['class'=>'flat-red']) !!} Male &emsp;
                    {!! Form::radio('applicable_sex', 'Female',null,['class'=>'flat-red','disabled']) !!} Female &emsp;
                    {!! Form::radio('applicable_sex', 'Others',null,['class'=>'flat-red','disabled']) !!} Others
				@elseif($cpt->applicable_sex == 'Female')	
                    {!! Form::radio('applicable_sex', 'Male',null,['class'=>'flat-red','disabled']) !!} Male &emsp;
                    {!! Form::radio('applicable_sex', 'Female',true,['class'=>'flat-red']) !!} Female &emsp;
                    {!! Form::radio('applicable_sex', 'Others',null,['class'=>'flat-red','disabled']) !!} Others
				@elseif($cpt->applicable_sex == 'Others')
                    {!! Form::radio('applicable_sex', 'Male',null,['class'=>'flat-red','disabled']) !!} Male &emsp;
                    {!! Form::radio('applicable_sex', 'Female',null,['class'=>'flat-red','disabled']) !!} Female &emsp;
                    {!! Form::radio('applicable_sex', 'Others',true,['class'=>'flat-red']) !!} Others
				@else
                    {!! Form::radio('applicable_sex', 'Male',null,['class'=>'flat-red','disabled']) !!} Male &emsp;
                    {!! Form::radio('applicable_sex', 'Female',null,['class'=>'flat-red','disabled']) !!} Female &emsp;
                    {!! Form::radio('applicable_sex', 'Others',null,['class'=>'flat-red','disabled']) !!} Others
				@endif	
                </div>
                <div class="col-sm-1"></div>
            </div> 
            <div class="form-group">
                {!! Form::label('Referring provider', 'Referring Provider', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
				@if(@$cpt->referring_provider == 'No')  	
                    {!! Form::radio('referring_provider', 'Yes',null,['class'=>'flat-red','disabled']) !!} Yes &emsp; {!! Form::radio('referring_provider', 'No',true,['class'=>'flat-red']) !!} No &emsp; 
				@else		
                    {!! Form::radio('referring_provider', 'Yes',true,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('referring_provider', 'No',null,['class'=>'flat-red','disabled']) !!} No &emsp;
				@endif		
                </div>	
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">        
                {!! Form::label('age_limit', 'Age Limit', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                   <p class="show-border no-bottom">{{ @$cpt->age_limit }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('modifier', 'Modifier', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6">
                   <p class="show-border no-bottom">{{ @$cpt->modifier_id }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">        
                {!! Form::label('revenue_code', 'Revenue Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->revenue_code }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('drug_name', 'Drug Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6">
                   <p class="show-border no-bottom">{{ @$cpt->drug_name }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('ndc_number', 'NDC Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->ndc_number }}</p>
                </div>
                {!! Form::hidden('temp_doc_id','',['id'=>'temp_doc_id']) !!}
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('min units', 'Min Units', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6 ">
                    <p class="show-border no-bottom">{{ @$cpt->min_units }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('max units', 'Max Units', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->max_units }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('anesthesia_unit', 'Anesthesia Base Unit', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                   <p class="show-border no-bottom">{{ @$cpt->anesthesia_unit }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('service_id_qualifier', 'Service ID Qualifier', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6 ">
                    <p class="show-border no-bottom">{{ @$cpt->service_id_qualifier }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

        </div>
    </div>
</div><!-- Codes col Ends -->
<?php $year_range = array_combine(range(date("Y")+0, date("Y")-4), range(date("Y")+0, date("Y")-4));  ?>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- Billing Col Starts -->
    <div class="box no-shadow margin-b-10">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="credit-card"></i> <h3 class="box-title">Billing</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body  form-horizontal margin-l-10 p-b-18">
           <div class="form-group">      
				{!! Form::label('year', 'Year', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}    
				<div class="col-lg-3 col-md-3 col-sm-6">
					{!! Form::select('year', array('' => '-- Select --') + (array)@$year_range,null,['class'=>'form-control select2 js-multiFeeYear']) !!}
					{!! $errors->first('allowed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
			<div class="form-group"> 
				{!! Form::label('insurance', 'Insurance', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
				<div class="col-lg-3 col-md-3 col-sm-6">
					{!! Form::select('insurance', array('' => '-- Select --','0'=>'Default'),null,['class'=>'form-control select2 js-multiFeeInsurance']) !!}
					{!! $errors->first('allowed_amount', '<p> :message</p>')  !!}
				</div>
				<div class="col-sm-1"></div>
			</div>
            <div class="form-group">        
                {!! Form::label('allowed_amount', 'Allowed Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                   <p class="show-border no-bottom allowed_amount">{{ $cpt->allowed_amount }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('billed_amount', 'Billed Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom billed_amount">{{ $cpt->billed_amount }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>


            <div class="form-group">
                {!! Form::label('required_clia_id', 'Required CLIA ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="control-group col-lg-6 col-md-6 col-sm-6">
				@if($cpt->required_clia_id == 'No')
                    {!! Form::radio('required_clia_id', 'Yes',null,['class'=>'flat-red js_required_clia_id','id'=>'yes','disabled']) !!} Yes &emsp; 
					{!! Form::radio('required_clia_id', 'No',true,['class'=>'flat-red js_required_clia_id','id'=>'no']) !!} No
				@else	
                    {!! Form::radio('required_clia_id', 'Yes',true,['class'=>'flat-red js_required_clia_id','id'=>'yes']) !!} Yes &emsp; 
					{!! Form::radio('required_clia_id', 'No',null,['class'=>'flat-red js_required_clia_id','id'=>'no','disabled']) !!} No
				@endif	
                </div>
                <div class="col-sm-1"></div>
            </div> 

            <div class="form-group js_required_clia_id_show  hide ">        
                {!! Form::label('clia id', 'CLIA ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <p class="show-border no-bottom">{{ $cpt->clia_id }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>


            <div class="form-group">        
                {!! Form::label('work_rvu', 'Work RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->work_rvu }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('facility_practice_rvu', 'Facility practice RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->facility_practice_rvu }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('nonfacility_practice_rvu', 'Non Facility practice RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->nonfacility_practice_rvu }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('pli_rvu', 'PLI RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->pli_rvu }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>                    
            
            <div class="form-group">        
                {!! Form::label('total_nonfacility_rvu', 'Total Facility RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->total_facility_rvu }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>

            <div class="form-group">        
                {!! Form::label('total_nonfacility_rvu', 'Total Nonfacility RVU', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!}
                <div class="col-lg-3 col-md-3 col-sm-6">
                    <p class="show-border no-bottom">{{ @$cpt->total_nonfacility_rvu }}</p>
                </div>
                <div class="col-sm-1"></div>
            </div>
            
            <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>                       
                <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>
                <div class="bottom-space-20 hidden-sm hidden-xs">&emsp;</div>                       
                <div class="margin-b-5 hidden-sm hidden-xs">&emsp;</div>
          
        </div>      
    </div>
</div><!-- Billing Col Ends -->

@include('practice/layouts/favourite_modal')
@stop

@push('view.scripts')
<script type="text/javascript">
	$(document).ready(function () {
		getmodifierandcpt();
	});
	
	$(document).on('change','.js-multiFeeYear',function(){
		$.ajax({
            type: "GET",
            url: api_site_url + '/yearInsurance/'+$(this).val(),
            success: function (result) { 
				if(result.length == 0){
					$('select[name="insurance"]').find('option').remove().end().append('<option value="">-- Select --</option><option value="0">Default</option>').val(''); 
					$('select[name="insurance"]').select2("val", null);
				} else {
					$('select[name="insurance"]').find('option').remove().end().append('<option value="">-- Select --</option><option value="0">Default</option>').val(''); 
					$.each(result, function(key, value) {   
						 $('select[name="insurance"]').append('<option value="'+ key +'">'+ value +'</option>'); 
					});
				}
            }
        });
	});
	
	$(document).on('change','.js-multiFeeInsurance',function(){
		var year = $('select.js-multiFeeYear').val();
		var insurance = $(this).val();
		var cpt_id = $('input[name="multiFeeScheduleCptID"]').val();
		var token = '<?php echo csrf_token(); ?>';
		$.ajax({
            type: "post",
            url: api_site_url + '/multiFeeScheduleData',
			data: {'_token':token,'insurance_id':insurance,'cpt_id':cpt_id,'year':year},
			dataType: 'json',
            success: function (result) { 
				if(result.billed_amount != '' && result.billed_amount != null) {
                    if(result.billed_amount == "0.00") {
                        $('.billed_amount').text('');
                    } else {                    
                        $('.billed_amount').text(result.billed_amount); 
                    }
                }
				else {
					$('.billed_amount').text(result.billed_amount);
                }
				if(result.allowed_amount != '' && result.allowed_amount != null) {
                    if(result.allowed_amount == "0.00") {
                        $('.allowed_amount').text('');
                    }  else {
						$('.allowed_amount').text(result.allowed_amount);
                    }
                }
				else {
					$('.allowed_amount').text(result.allowed_amount);
                }
				if(result.Modifier != '' && result.Modifier != null)
					$('#modifierId').select2('val', [result.Modifier]).prop("disabled", true).trigger('change');
				else
					$('#modifierId').select2('val', [result.default_modifier_id]).prop("disabled", false).trigger('change');
            }
        });
	});
	
</script>	
@endpush