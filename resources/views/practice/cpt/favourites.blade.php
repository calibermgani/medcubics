@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Favorites</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li-->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->  
            @if(count((array)@$favourites) > 0)
                <li class="dropdown messages-menu hide"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                    @include('layouts.practice_module_stream_export', ['url' => 'api/cptfavouritereports/'])
                </li>
            @endif 
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
@include ('practice/cpt/tabs', array('data' => 'active'))
@stop
@section('practice')
<?php $year_range = array_combine(range(date("Y")+0, date("Y")-4), range(date("Y")+0, date("Y")-4));  ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info  no-shadow">
        <div class="box-header margin-b-10">

            <i class="fa fa-bars"></i><h3 class="box-title">Favorite List</h3>  
            <div class="box-tools pull-right margin-t-2">
				@if(isset($cpt_arr->count) && $cpt_arr->count == 0)
					<a href="" class="selFnCpt med-red font600 font14"><i class="fa fa-plus-circle"></i> Import CPT / HCPCS</a>
				@else	
					@if($checkpermission->check_url_permission('icd/create') == 1)
						<a href="{{ url('/cpt/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New CPT / HCPCS</a>
					@endif
				@endif
            </div>
        </div><!-- /.box-header -->
		 <div class="search_fields_container col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
			{!! Form::label('Year', 'Year', ['class'=>'control-label font600']) !!} 
			{!! Form::select('year', array('' => '-- Select --') + (array)@$year_range,null,['class'=>'form-control select2 js-multiFeeYear']) !!}
		</div>
		<div class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
			{!! Form::label('Insurance', 'Insurance', ['class'=>'control-label font600']) !!} 
			{!! Form::select('insurance', array('' => '-- Select --','0'=>'Default'),null,['class'=>'form-control select2 js-multiFeeInsurance']) !!}
		</div>
		</div>
        <div class="box-body js_cpt_favourites">
            @include('practice/cpt/cpt_favourites')
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>

<!--End-->
@include('practice/layouts/favourite_modal') 
<div id="export_csv_div"></div> 
@stop
@push('view.scripts')
<script type="text/javascript">
	var api_site_url = '{{url('/')}}';
    $(document).ready(function () {
		$(document).on('click', '.selFnCpt', function (e) {
            
			$.ajax({
				type: 'GET',
				url: api_site_url + '/getmastercpt',
				success: function (result) {
					js_alert_popup(result.message);
					window.location = api_site_url + '/listfavourites'; 
				}
			});
			e.preventDefault();
		});
		getmodifierandcpt(); 
	});
	
	$(document).on('change','.js-multiFeeYear',function(){
		if($(this).val() == '')
			year = 'undefined';
		else
			year = $(this).val();
		if($('select.js-multiFeeInsurance').val() == '')
			insurance = 'undefined';
		else
			insurance = $('select.js-multiFeeInsurance').val();
		$.ajax({
            type: "GET",
            url: api_site_url + '/yearInsurance/'+year,
            success: function (result) { 
				if(result.length == 0){
					$('select[name="insurance"]').find('option').remove().end().append('<option value="">-- Select --</option><option value="0">Default</option>').val(''); 
					$('select[name="insurance"]').select2("val", null);
				}else{
					$('select[name="insurance"]').find('option').remove().end().append('<option value="">-- Select --</option><option value="0">Default</option>').val(''); 
					$.each(result, function(key, value) {   
						 $('select[name="insurance"]').append('<option value="'+ key +'">'+ value +'</option>'); 
					});
				}
				var table = $('#js_cpt_favourites').DataTable();
				table.destroy();
				loaddatatablefavcpt(year,insurance);
            }
        });
	});
	
	$(document).on('change','.js-multiFeeInsurance',function(){ 
		var table = $('#js_cpt_favourites').DataTable();
		if($('select.js-multiFeeYear').val() == '')
			year = 'undefined';
		else
			year = $('select.js-multiFeeYear').val();
		
		if($(this).val() == '')
			insurance = 'undefined';
		else
			insurance = $(this).val();
		table.destroy();
		loaddatatablefavcpt(year,insurance);
	});

	/*$('.js_search_export_csv').click(function(){
		var baseurl = '{{url('/')}}';
		var url = baseurl+"/reports/streamcsv/export/CPTfavourites";
		form = $('form').serializeArray();
        var data_arr = [];
        $('select:visible').each(function(){
			//  data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
			data_arr.push({
				name : $(this).attr('name'), 
				value:  ($(this).select2('val'))
			});
		});       
		//  $('input.auto-generate:visible').each(function(){
		//     // data_arr += $(this).attr('name')+'='+$(this).val()+'&';
		//     data_arr.push({
		//         name : $(this).attr('name'), 
		//         value:  ($(this).val())
		//     });
		//  });
		data_arr.push({
			name : "controller_name", 
			value:  "CptController"
		});
		data_arr.push({
			name : "function_name", 
			value:  "getCptFavoritesExport"
		});
		data_arr.push({
			name : "report_name", 
			value:  "CPT_HCPCS_Favourites"
		});
		// console.log(data_arr);
		form_data = "<form id='export_csv' method='POST' action='"+url+"'>";
		 $.each(data_arr,function(index,value){	
             if($.isArray(value.value)) {
                 if(value.value.length > 0) {
					var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                 }
             } else {
                if(value.value.length > 0) {
                form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
             }
		 });
         form_data  += "<input type='hidden' name='export' value = 'xlsx'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
		 form_data += "</form>";
		//  console.log(form_data);
		 $("#export_csv_div").html(form_data);
		 $("#export_csv").submit();
		 $("#export_csv").empty();
	});	*/
</script>	
@endpush