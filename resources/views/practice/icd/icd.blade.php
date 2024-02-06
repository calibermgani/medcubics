@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> ICD 10 </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--<li><a href="{{url('icd')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>-->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
         <!--    <li class="dropdown messages-menu"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
            </li-->
                @include('layouts.practice_module_stream_export', ['url' => 'api/icdreports/'])
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/icd')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')

@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!-- Col Starts -->
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">ICD 10 List</h3>
            <!--<div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>-->
			
            <div class="box-tools pull-right margin-t-2">
				@if(isset($icd_arr->count) && $icd_arr->count == 0)
					<a href="" class="selFnIcd med-red font600 font14"><i class="fa fa-plus-circle"></i> Import ICD 10</a>
				@else
					@if($checkpermission->check_url_permission('icd/create') == 1)
						<a href="{{ url('/icd/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New ICD 10</a>
					@endif
				@endif
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive"><!-- Box Body Starts -->
            <table id="list-icd10" class="table table-bordered table-striped table-width-pipeline">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Code</th>
                        <th class="td-c-60">Short Description</th>
                        <th>Gender</th>
                        <th>Effective Date</th>
                        <th>Inactive Date</th>                                    	
<!-- <th>Favourites</th>  -->
                    </tr>
                </thead>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- Col Ends -->
<div id="export_csv_div"></div> 
<!--End-->
@include('practice/layouts/favourite_modal')
@stop

@push('view.scripts')
<script type="text/javascript">
	var api_site_url = '{{url("/")}}';
    $(document).ready(function () {
		$(document).on('click', '.selFnIcd', function (e) {
            
			$.ajax({
				type: 'GET',
				url: api_site_url + '/getmastericd',
				success: function (result) {
					js_alert_popup(result.message);
					 window.location = api_site_url + '/icd'; 
				}
			});
			e.preventDefault();
		});	
	});
    /*$('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/icd";
		 form = $('form').serializeArray();
         var data_arr = [];
            // $('select.auto-generate:visible').each(function(){
	        //     //  data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
            //     data_arr.push({
            //         name : $(this).attr('name'), 
            //         value:  ($(this).select2('val'))
            //     });
	        //  });       
	        //  $('input.auto-generate:visible').each(function(){
	        //     // data_arr += $(this).attr('name')+'='+$(this).val()+'&';
            //     data_arr.push({
            //         name : $(this).attr('name'), 
            //         value:  ($(this).val())
            //     });
	        //  });
             data_arr.push({
                    name : "controller_name", 
                    value:  "IcdController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "getIcdExport"
                });
                data_arr.push({
                    name : "report_name", 
                    value:  "ICD"
                });
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
	});*/
</script>	
@endpush
