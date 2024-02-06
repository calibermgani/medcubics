@extends('admin')
<?php $id = Route::current()->parameters['id']; ?>
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1><small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} font14"></i>
         {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Visits <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i><span> Claims </span></small></h1>
        <ol class="breadcrumb">
            
            <?php $uniquepatientid = $id; ?>

            @include ('patients/layouts/swith_patien_icon')
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_stream_export', ['url' => 'api/patients/chargesexport/'.$id.'/'])
            </li>
            <li><a href={{App\Http\Helpers\Helpers::patientBackButton($id)}} accesskey="b" class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" class="js-help hide" data-toggle="modal" data-url="{{url('help/charges')}}" ><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice-info')
@include ('patients/layouts/tabs',['tabpatientid'=>@$id,'needdecode'=>'yes']) 
@stop
@section('practice')
@include ('patients/billing/model-inc')
<?php
	$activetab = 'charges_list'; 
	$routex = explode('.',Route::currentRouteName());
	
	if(count($routex) > 1){
		if($routex[1] == 'appointments') {
			$activetab = 'appointments';
		}
	}
?>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <!-- Tab Starts  -->          
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('scheduler/scheduler') == 1)
                @if($checkpermission->check_url_permission('patients/{id}/appointments') == 1)
                <li class="@if($activetab == 'appointents') active @endif"><a href="{{ url('patients/'.$id.'/appointments') }}" ><i class="fa fa-bars i-font-tabs"></i> Appo<span class="text-underline">i</span>ntments</a></li>
                @endif
            @endif            
            <li class="@if($activetab == 'charges_list') active @endif" accesskey="m"><a href="" ><i class="fa fa-bars i-font-tabs"></i> Clai<span class="text-underline">m</span>s</a></li>                                                 
        </ul>
    </div>    <!-- Tab Ends -->
    <input type="hidden" id="js_page_name" value="patientcharges" >	
	<?php $srchFltr_patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($uniquepatientid, 'decode'); ?>	
	@include('layouts.search_fields', ['srchFltr_patient_id' => $srchFltr_patient_id]) 
    <div class="btn-group col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 text-right hidden-print">
        @if($checkpermission->check_url_permission('patients/{id}/billing/create') == 1 && $patients->status == 'Active')        
        <a href="{{ url('patients/'.$id.'/billing/create') }}" accesskey="a" class="js-create-claim font600"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Claim</a>
        @endif  

        <a href="{{ url('patients/'.$id.'/superbill/create') }}" class="claimdetail font600 p-r-10 margin-l-10 hide"><i class="fa {{Config::get('cssconfigs.patient.superbill')}}"></i>| E-Superbill</a>
    </div>
     
    <div class="no-border no-shadow ">
        <div class="box-body table-responsive mobile-scroll">
        
         <div class="ajax_table_list hide"></div>
             <div class="data_table_list" id="js_ajax_part">
            <table id="search_table_payment" class="table table-bordered table-striped">    
             <thead>
                <tr>
                    <th>DOS</th>
                    <th>Claim No</th>
                    <th>Rendering</th>
                    <th>Billing</th> 
                    <th>Facility</th> 
                    <th>Billed To</th>
                    <th>Unbilled($)</th>
                    <th>Billed($)</th> 
                    <th>Paid($)</th>                 
                    <th>AR Bal($)</th>
                    <th>Status</th>
					<th>Sub Status</th>
                    <th class="hidden-print"></th>
                </tr>
             </thead>           
             <tbody>
                
             </tbody>
            </table>
            </div> 
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!--End-->
<div id="js-model-popup-payment" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close hidden-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim No : <span class = "js-replace"></h4>
            </div>
            <div class="modal-body no-padding" >

            </div>
        </div>
    </div>
</div>

@stop
@push('view.scripts')
{!! HTML::script('js/datatables_serverside.js') !!} 
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
    var api_site_url = '{{url('/')}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/patients/<?php echo $id;?>/chargesList"; 
    url_charges = '';
    /* Search function start */
    var column_length = $('#search_table_payment thead th').length;         

    function accessAll(url_charges) {         
		var selected_column = ['Claim No','Acc No','Patient Name', 'DOS','Facility','Rendering','Billing','Payer','Unbilled','Billed', 'Paid', 'Pat Bal', 'Ins Bal','AR Bal', 'Status'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		claimSearch(allcolumns, url_charges); /* Trigger datatable */
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
			 });                                                                                // Getting all data in select fields 
			 $('input.auto-generate:visible').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
			 });                                                                                // Getting all data in input fields
			 dataArr = {data:data_arr};
			 accessAll();                                                                       // Calling data table server side scripting
		}, 100);
	}
	/* function for get data for fields End */
        
    function claimSearch(allcolumns, url_charges) { 
        search_url = listing_page_ajax_url;
        if(url_charges != '' && typeof url_charges!= "undefined")    {
            search_url = url_charges; 
        }           
        var dtable = $("#search_table_payment").DataTable({         
            "createdRow":   function ( row, data, index ) {            
            if(data[1] != undefined)
            data[1] = data[1].replace(/[\-,]/g, '');
                            },      
            "bDestroy"  :   true,
            "searching": false,
            "paging"    :   true,
            "info"      :   true,
            //"aoColumns"   :   allcolumns,
            "columnDefs":   [ { orderable: false, targets: [6,7,8,9,11] } ], 
            "autoWidth" :   false,
            "lengthChange"      : false,
            //"searchHighlight" : true,
            "searchDelay": 450,
            "serverSide": true, 
            "order": [[0,"desc"],[1,"desc"]],            
            "ajax": $.fn.dataTable.pipeline({
                url: search_url, 
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
                {"datas": "id", sDefaultContent: "" }               
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
                //var length = settings._iDisplayStart;
                //var sorting_length = settings.aLastSort.length;
                hideLoadingImage(); // Hide loader once content get loaded.                
            },            
        });
    }
	
    $(document).ready(function(){
         $(".selholdBlk").prop("disabled", true);
        $(".selClaimStatus").trigger("change");
    });
    
	/* Charge analysis report hold fields start  */
	$(document).on("change", ".selClaimStatus", function(){
		var isHold = 0;
		$("select.selClaimStatus option:selected").each(function () {
			if($(this).text() == 'Hold')
			isHold = 1;
		});

		if(isHold) {    
			$(".selholdBlk").prop("disabled", false);
		} else {        
			$(".selholdBlk").select2('val', '').val("").prop("disabled", true); // Clear already selected hold reason and release date.
		}
	});
	/* Charge analysis report hold fields end  */

	/* Export Excel for Patient Claim list */
	/*$('.js_search_export_csv').click(function(){console.log("hello");
		current_page = window.location.pathname.split("/");
		patient_id = current_page[current_page.length - 2];
		var baseurl = '{{url('/')}}';
		var url = baseurl+"/reports/streamcsv/export/patient-claims-list";
		form = $('form').serializeArray();
        var data_arr = [];
		$('select.auto-generate:visible').each(function(){
			//  data_arr += $(this).attr('name')+'='+$(this).select2('val')+'&';
			data_arr.push({
				name : $(this).attr('name'), 
				value:  ($(this).select2('val'))
			});
		});       
		$('input.auto-generate:visible').each(function(){
			// data_arr += $(this).attr('name')+'='+$(this).val()+'&';
			data_arr.push({
				name : $(this).attr('name'), 
				value:  ($(this).val())
			});
		});
		data_arr.push({
			name : "controller_name", 
			value:  "PatientBillingController"
		});
		data_arr.push({
			name : "function_name", 
			value:  "getBillingExport"
		});
		data_arr.push({
			name : "report_name", 
			value:  "Patient_Claims_List"
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
         form_data  += "<input type='hidden' name='patient_id' value = '"+patient_id+"'><input type='hidden' name='export' value = 'xlsx'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
		 form_data += "</form>";
		//  console.log(form_data);
		 $("#export_csv_div").html(form_data);
		 $("#export_csv").submit();
		 $("#export_csv").empty();
	});	*/

    $('.js_search_export_pdf').click(function(){
        current_page = window.location.pathname.split("/");
        patient_id = current_page[current_page.length - 2];
        var baseurl = '{{url('/')}}';
        var url = baseurl+"/reports/export_pdf/patient_claims_list";
        var data_arr = [];
        form = $('form').serializeArray();
        form_data = "<form id='export_pdf' target='_blank' method='POST' action='"+url+"'>";
        
        $('select.auto-generate:visible').each(function(){
            data_arr.push({
                name : $(this).attr('name'),
                value: ($(this).select2('val'))
            });
        });
        $('input.auto-generate:visible').each(function(){
            data_arr.push({
                name : $(this).attr('name'),
                value: ($(this).val())
            });
        });
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
        form_data  += "<input type='hidden' name='exports' value='pdf'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'><input type='hidden' name='patient_id' value = '"+patient_id+"'>";
        form_data += "</form>";
        $("#export_pdf_div").html(form_data);
        $("#export_pdf").submit();
        $("#export_pdf").empty();
    });
</script>
@endpush
<!-- Server script end -->