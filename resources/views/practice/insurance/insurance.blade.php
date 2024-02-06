@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> {{ucfirst($selected_tab)}} </span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if($insurances>0)
            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_stream_export', ['url' => 'api/insurancereports/'])
            </li>
            @endif

            <li><a href="" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/insurance/tabs')
@stop


@section('practice')

<div class="col-lg-12">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>    

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars"></i><h3 class="box-title">Insurance List</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_url_permission('insurance/create') == 1)
                <a href="{{ url('insurance/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Insurance</a>
                @endif	
            </div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive">
            <table id="list-insurance" class="table table-bordered table-striped table-width-pipeline">
                <thead>
                    <tr>
                        <th>Short Name</th>                       	
                       	<th>Insurance Name</th>
						<th>Insurance Type</th>	
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Payer ID</th>
                        <!-- <th>Favourite</th> -->
                    </tr>
                </thead>
            </table>

        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
@include('practice/layouts/favourite_modal')
<div id="export_csv_div"></div> 
@stop   

@push('view.scripts')
<script>
/*$('.js_search_export_csv').click(function(){
	var baseurl = '{{url('/')}}';
	var url = baseurl+"/reports/streamcsv/export/insurance";
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
                    value:  "InsuranceController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "getInsuranceExport"
                });
                data_arr.push({
                    name : "report_name", 
                    value:  "Insurance_List"
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