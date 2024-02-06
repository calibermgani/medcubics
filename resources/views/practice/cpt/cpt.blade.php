@extends('admin')

@section('toolbar')
<style type="text/css">
    .text-right{
        text-align: right !important;
    }
</style>
<div class="row toolbar-header"><!-- Toolbar Row Starts -->
    <section class="content-header">
        <h1><small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> CPT / HCPCS Master</span></small></h1>
        <ol class="breadcrumb">
            <?php /*
            <!--li><a href="{{ url('cpt/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>-->
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <!--li class="dropdown messages-menu"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/cptreports/'])
            </li-->
			*/ ?>
            <li><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="" data-target="#js-help-modal"  data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar Row Ends -->
@stop

@section('practice-info')
@include ('practice/cpt/tabs', array('data' => 'active'))
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!-- Col starts -->
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars"></i><h3 class="box-title">CPT / HCPCS List</h3>  
			<div class="box-tools pull-right margin-t-2">			
				@if(isset($cpt_arr->count) && $cpt_arr->count == 0)
					<a href="" class="selFnCpt med-red font600 font14"><i class="fa fa-plus-circle"></i> Import CPT</a>
				@endif
			</div>
        </div><!-- /.box-header -->
        <div class="box-body table-responsive"><!-- Box Body Starts -->		 
            <table id="list-cpt" class="table table-bordered table-striped table-width-pipeline">
                <thead>
                    <tr>
                        <th>CPT / HCPCS</th>
                        <th>Short Description</th>
                        <th>Billed Amount($)</th>
                        <th>Allowed Amount($)</th>
                        <th>POS</th>
                        <th>Type of Service</th>
						<!--   <th>Work RVU</th>
						<th>Total Facility RVU</th>
						<th>Total Nonfacility RVU</th>
						<th>Created On</th>
						<th>Created By</th>
						<th>Updated On</th>
						<th>Updated By</th> -->
                        <th>Favorites</th>
                    </tr>
                </thead>
            </table>
        </div><!-- /.box-body ends-->
    </div><!-- /.box Ends -->
</div><!-- Col Ends -->

@include('practice/layouts/favourite_modal')
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
					 window.location = api_site_url + '/cpt'; 
				}
			});
			e.preventDefault();
		});	
		getmodifierandcpt(); 
        // $('#add').click(function (e) {
        //     var procedure_category =$('#add_procedure_category').val();
        //     var _token = '{{ csrf_token() }}';
        //     $.ajax({
        //         type: 'POST',
        //         data:{procedure_category,_token},
        //         url: api_site_url + '/add_procedure',
        
        //         success: function (datalist) {
        //                     console.log(datalist);
        //         // if($('#popup_procedure_category option').length){
        //             var data = datalist.reserve();
        //             $('#popup_procedure_category option').remove();
        //                 // $(datalist.reverse()).each(function(key, data) { 
        //                 $.each(data, function(key, data){
        //                 $('#popup_procedure_category').append($("<option></option>").attr("value", key).text(data));
        //             });
        //         // }
        
        //             // window.location = api_site_url + '/cpt';
        //             // console.log(data);
        //             $('.add_module').hide();
        //             // $('#popup_procedure_category').val(data);
        //             $('#js-favourite-category-update').modal();
        //             // js_alert_popup(data.message);
        //         }
        //     });
        //     e.preventDefault();
        // }); 
	});
</script>	
@endpush