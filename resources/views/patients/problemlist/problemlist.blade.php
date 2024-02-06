@extends('admin')

@section('toolbar')
<?php
	@$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id,'encode');
    if(!isset($get_default_timezone)){
        $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();        
    }
?>
<div class="row toolbar-header">
    <!--div><p class="alert alert-success hide" id="add_prm_success-alert">Added successfully</p></div-->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span>Workbench</span></small>
        </h1>
        <?php 
			$activetab = 'payments_list';
            $routex = explode('.',Route::currentRouteName());
        ?>
		<ol class="breadcrumb">
			<?php $uniquepatientid = @$patient_id; ?>
			@include('layouts.practice_module_stream_export', ['url' => 'api/problemlistreports/'.@$patient_id.'/'])
			<li><a href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
			<li><a href="#js-help-modal" data-url="{{url('help/problem_list')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
		</ol>
    </section>
</div>
@stop

@section('practice-info')
	@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'yes'])
@stop

@section('practice')

	<?php $id = Route::getCurrentRoute()->parameter('id'); ?>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<!-- Tab Starts  -->	   
		<div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
			<ul class="nav nav-tabs">
				<li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa {{Config::get('cssconfigs.common.nav')}} i-font-tabs"></i> List</a></li> 
			</ul>
		</div>
		<!-- Tab Ends -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding space20">
			<div class="box no-border no-shadow">				
				<div class="box-header">
					<i class="fa fa-bars font14"></i><h3 class="box-title">Workbench</h3>
					<div class="box-tools pull-right margin-t-4">
						@if($checkpermission->check_url_permission('patients/{patient_id}/problem/create') == 1)
                            <a class="js-new_problem_list form-cursor font13 font600" accesskey="a" data-url="{{url('patients/'.@$patient_id.'/problem/create')}}"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Workbench </a>
						@endif						
					</div>
				</div><!-- /.box-header -->
				@include('layouts.search_fields')
				<div id ="js_table_search_listing" class="box-body table-responsive js_problem_list_loop">
					<?php $listFor = 'patients'; // show for charges in patients / common. ?>
					@include ('patients/problemlist/problemlistloop') 
				</div><!-- /.box-body -->
			</div><!-- /.box -->
		</div>
	</div>

	<!-- New Problem details starts here -->
	<div id="create_problem_list" class="modal fade in"></div><!-- /.modal-dialog -->
	<!-- Modal New Problem Details ends here -->
	<!-- Show Problem list start-->
	<div id="show_problem_list" class="modal fade in"></div><!-- /.modal-dialog -->
	<!-- Show Problem list end--> 	 
	<!--End-->
@stop

<!-- Server script start -->
@push('view.scripts') 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
	var api_site_url = '{{url('')}}';   
    var allcolumns = [];
	var listing_page_ajax_url = api_site_url+"/patients/<?php echo $uniquepatientid;?>/problemlistAjax"; 
	url_charges = '';
	var get_practice_timzone = '{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), "m/d/Y H:i:s") }}';
	/* Search function start */
	var column_length = $('#search_table_WB thead th').length;  
	dataArr = [];
	
	function accessAll() {                      
		var selected_column = ['','DOS','Claim No','Patient Name','Provider','Facility','Billed To','Billed Amt','Paid','AR Due','Status','Sub Status','Followup Dt','Assigned To','Priority'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		prbListSearch(allcolumns); /* Trigger datatable */
	}
	
	var dataArr = {};	
	var wto = '';
	
	
	/* function for get data for fields Start */
	function getData(){
		clearTimeout(wto);
		var data_arr = {};
		wto = setTimeout(function() {  
			 $('select.auto-generate').each(function(){
				 data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
			 });																				// Getting all data in select fields 
			 $('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			 });																				// Getting all data in input fields
			 //data_arr['type'] = "<?php  echo (Request::segment(2) == 'problemlist') ? '' : 'assigned'; ?>";
			 dataArr = {data:data_arr};
			 accessAll();																		// Calling data table server side scripting
		}, 100);
	}
	/* function for get data for fields End */
	
	 
	$(document).ready(function(){ 
		__searchMoredata();
	});
	
	function prbListSearch(allcolumns) {
		$("#search_table_WB").DataTable({          
            "createdRow":   function ( row, data, index ) {
                                if(data[1] != undefined)
                                    data[1] = data[1].replace(/[\-,]/g, '');
                            },      
            "bDestroy"  :   true,
            "paging"    :   true,
			"searching"	: 	false,
            "info"      :   true,
			//"processing": true,
            //"aoColumns"   :   allcolumns,
            "columnDefs":   [ { orderable: false, targets: [0,12,13] } ],
            "autoWidth" :   false,
            "lengthChange"      : false,
            //"searchHighlight" : true,
            "searchDelay": 450,
            "serverSide": true, 
            "order": [[1,"desc"],[2,"desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url, 
				data:{'dataArr':dataArr},
                beforeSend:displayLoadingImage(),   
                pages: 1, // number of pages to cache
                success: function(){
                    // Hide loader once content get loaded.
                }   
            }),
            "columns": [
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
                {"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
				{"datas": "id", sDefaultContent: "" },
            ],  
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) { 
                $(".ajax_table_list").html(aData+"</tr>");
                var get_orig_html = $(".ajax_table_list tr").html();
                var get_attr = $(".ajax_table_list tr").attr("data-url");
                var get_class = $(".ajax_table_list tr").attr("class");
                $(nRow).addClass(get_class).attr('data-url', get_attr);
                $(nRow).closest('tr').html(get_orig_html);
                $(".ajax_table_list").html("");             
            },
            "fnDrawCallback": function(settings) {
                $('#js-select-all').prop('checked',false); // uncheck select all option while paginating
                hideLoadingImage(); // Hide loader once content get loaded.
            }
        });
    }
<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>
@endpush
<!-- Server script end -->