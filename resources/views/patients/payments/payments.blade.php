@extends('admin')
@section('toolbar')

<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}" data-name="money"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Payments 
            <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> View </span></small>
        </h1>
        <ol class="breadcrumb">
            
            <?php $uniquepatientid = $patient_id; ?>   
            @include ('patients/layouts/swith_patien_icon')
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li class="dropdown messages-menu">
                <!--<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>-->
                @include('layouts.practice_module_stream_export', ['url' => 'api/patients/paymentsexport/'.$patient_id.'/patient/export/'])
            </li>
            <li><a accesskey="b" href={{App\Http\Helpers\Helpers::patientBackButton($patient_id)}} class="js_next_process"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li> 
            <li><a href="#js-help-modal" class="js-help hide" data-toggle="modal" data-url="{{url('help/payment')}}" ><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
<?php 
	$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
	$id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
?>
@include ('patients/layouts/tabs',['tabpatientid'=>@$patient_id,'needdecode'=>'no'])
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <!-- Tab Starts  -->
    <?php 
		$id = Route::current()->parameter('id');
		$activetab = 'payments_list'; 
		$routex = explode('.',Route::currentRouteName());           
	?>    
    <div class="med-tab nav-tabs-custom margin-t-m-13 no-bottom">
        <ul class="nav nav-tabs">
            <li class="@if($activetab == 'payments_list') active @endif"><a href="" ><i class="fa fa-bars i-font-tabs"></i> List</a></li>          
        </ul>
    </div>         
      
    @include('layouts.search_fields', ['srchFltr_patient_id' => $patient_id]) 
    
    <div class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12  @if(!empty($claims_lists)) margin-t-10 @else margin-t-15 margin-b-10 @endif">

        <a class="form-cursor med-orange font600 p-r-10"> Post <i class="fa fa-angle-double-right"></i> </a> 
        @if(!empty($claims_lists))
        <a href= "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "{{url('patients/'.$id.'/paymentinsurance/insurance')}}"class="js_pay_ins js_pay_ins_key claimdetail js_pay_dea form-cursor font600 right-border p-r-10 orange-b-c"><i class="fa {{Config::get('cssconfigs.common.insurance')}}"></i> <span class="text-underline">I</span>nsurance</a>
        @endif
        <a href= "#" data-toggle="modal" data-tile = "Post Patient Payment" data-target="#choose_claims" data-url = "{{url('patients/'.$id.'/paymentinsurance/patient')}}" class="js_pat_pay_key form-cursor claimotherdetail font600 p-r-10 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}}"></i> <span class="text-underline">P</span>atient</a>
     <!--   <a href="{{ url('patients/'.$id.'/wallet_transaction') }}" class="form-cursor claimotherdetail font600 p-l-10"><i class="fa fa-user"></i> Pat Transaction</a> -->
    </div>

    @if(Session::has('ar_claim_id'))
    {!! Form::hidden('ar_var',null, ['class' => 'js-arvar']) !!}
    {!! Form::hidden('ar_var_patient',$id, ['class' => 'js-arvar-patient']) !!}
    @endif
	
    <input type="hidden" id="js_page_name" value="patientpayment" >
    <!-- Payment Listing page details starts here-->
    <div class="no-border no-shadow "> 
        <div class="box-body table-responsive mobile-scroll">            
            <div class="ajax_table_list hide"></div>
             <div class="data_table_list" id="js_ajax_part">
				<?php            
					// Copay payment also considered
					$payment_claimed_det = App\Models\Payments\PMTInfoV1::getAllpaymentClaimDetailsByPatient('patient', $patient_id);  
					$tbl_class = (!empty($claims_lists)) ? 'search_table_payment':'';  
				?>
				<table id ="{{ $tbl_class }}" class="table table-bordered table-striped table-collapse">
					<thead>
						<tr>                        
							<th>DOS</th>
							<th>Claim No</th>
							<th>Rendering</th>
							<th>Billing</th>
							<th>Facility</th> 
							<th>Billed To</th>
							<th>Charge Amt($)</th>
							<th>Paid($)</th>
							<th>Adjustment($)</th>
							<th>Pat Bal($)</th>
							<th>Ins Bal($)</th>
							<th>AR Bal($)</th>
							<th>Status</th> 
							<th></th>           
						</tr>
					</thead>               
					<tbody>       
					</tbody>
				</table>
			</div> 
        </div>
    </div>
    <!-- Payment Listing page details ends here-->
</div>
<!-- Insurance payment posting starts here -->
<div id="choose_claims" class="modal fade in">
    <div class="modal-md-700">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Posting</h4>
            </div>
            <div class="modal-body no-padding" >
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div>
<!-- Claim transaction details popup data starts here -->
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
<div id="export_csv_div"></div> 
@stop
@push('view.scripts') 
{!! HTML::script('js/datatables_serverside.js') !!}	
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
    var api_site_url = '{{url('/')}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/patients/<?php echo $uniquepatientid;?>/paymentList"; 
    url_charges = '';
	
	var column_length = $('#search_table_payment thead th').length; 	
	dataArr = [];	
	// Initially disable links and buttons, enable once page render completed.
	$('input:submit').prop('disabled',true);				
	$("section.content a").css("pointer-events", "none");
				
	function accessAll(url_charges) {         		
		var selected_column = ['DOS','Claim No','Rendering','Billing', 'Facility', 'Billed To', 'Charge Amt', 'Paid', 'Adjustment','Pat Bal', 'Ins Bal','AR Bal', 'Status'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		claimSearch(allcolumns, url_charges); /* Trigger datatable */
	}
	
    var dataArr = {};   
    var wto = '';
        
	$(function () {
		displayLoadingImage();
	});
	
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
	
    //search_table_payment	
	$(document).ready(function(){
		/* Search function start */
         dataArr = [];		  
			
         /* Main listing page Check box option function */      
         $(document).on("ifToggled click change", ".js-check-searched", function(){          
            status_val = []; 

            /* Check box option checked here */
            if($(this).val() == "All" && $(this).is(":checked")){
                    $('input[name="is_pending"]').prop('checked', false);
                    status_val = "All";               
            } else if($(this).val() != "All" && $(this).is(":checked")){
                    $('input[name="is_pending_all"]').prop('checked', false); 
            } else if($(this).val() != "All" &&     !$(this).is(":checked") && $("input[name='is_pending']:checked").length <=0){
                   status_val = "All";            
            }
            // Set initial loader symbol.
			$.each($("input[name='is_pending']:checked"), function(){            
				status_val.push($(this).val());
			});
            
            // status_val = (status_val!='')?'/'.status_val:"";             
            setTimeout(function(){    
                allcolumns = [];                
                if(status_val =='Hold,Ready,Denied,Pending')
                    status_val='All';

                url_charges = api_site_url+'/charges/chargesList/'+status_val;
                $.get(url_charges,function(data){             
                    $('#search_table_payment').html('');
                    /* In-case Check box selected all  checked all check */
                    if((status_val== 'Hold,Ready,Denied,Pending')||(status_val== 'All')) {
                        $('input[name="is_pending"]').prop('checked', true);
                        $('input[name="is_pending_all"]').prop('checked', true); 
                     }   
                    /* In case one check box  is unchecked firsted check box checked option is removed  */ 
                    var checkBoxCount = $('input[name="is_pending"]:checkbox:not(":checked")').length;
                    if((checkBoxCount > 0) && (checkBoxCount < 4)){
                        $('input[name="is_pending_all"]').prop('checked', false); 
                    }     					
                    accessAll(url_charges); /* Trigger datatable */                              
                });
            }, 500);  
        });		
	});
    /* Selvakumar code for dynamic search */   

	/* Getting data on change event trigger */
	 
	/* Dynamic append */
 
	$(document).on('change','select.more_generate',function(){ 
		__searchMoredata();
		$("#search_table_payment").DataTable().clearPipeline().draw();                       // Trigger data table server side script
	});
	/* $(document).ready(function(){ 
		__searchMoredata();
	}); */

	/* Selvakumar code for dynamic search */

	function claimSearch(allcolumns, url_charges) { 
		search_url = listing_page_ajax_url;       
		var dtable = $("#search_table_payment").DataTable({			
			"createdRow": 	function ( row, data, index ) {
				if(data[1] != undefined)
					data[1] = data[1].replace(/[\-,]/g, '');
			},
			//"processing": true,
			"bDestroy"	:	true,
			"searching": false,
			"paging"	: 	true,
			"info"		: 	true,
			"aoColumns"	: 	allcolumns,
			//"columnDefs":   [ { orderable: false, targets: [0] } ], 
			"autoWidth"	: 	false,
			"lengthChange"		: false,
			
			//"searchHighlight"	: true,
			//"searchDelay": 450,
			"serverSide": true,	
			"order": [[0,"desc"],[1,"desc"]],
			
            "ajax": $.fn.dataTable.pipeline({
                url: search_url, 
                data:{'dataArr':dataArr},
				beforeSend: displayLoadingImage(),
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
				// Close if existing popup opened.
				$('.modal').modal('hide');
                $('input:submit').prop('disabled', false);
                $("section.content a").css("pointer-events", "");				
				//var length = settings._iDisplayStart;
				//var sorting_length = settings.aLastSort.length;
                hideLoadingImage();  // Hide loader once content get loaded.
			}
		});
	} 
	
    /*$('.js_search_export_csv').click(function(){
		current_page = window.location.pathname.split("/");
		patient_id = current_page[current_page.length - 2];
		tab = current_page[current_page.length - 3];
		var baseurl = '{{url('/')}}';
		var url = baseurl+"/reports/streamcsv/export/patient-payments-list";
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
                    value:  "PatientPaymentController"
                });
                data_arr.push({
                    name : "function_name", 
                    value:  "getPaymentExport"
                });
                data_arr.push({
                    name : "report_name", 
                    value:  "Patient_Payments_List"
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
         form_data  += "<input type='hidden' name='tab' value = '"+tab+"'><input type='hidden' name='patient_id' value = '"+patient_id+"'><input type='hidden' name='export' value = 'export'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
		 form_data += "</form>";
		//  console.log(form_data);
		 $("#export_csv_div").html(form_data);
		 $("#export_csv").submit();
		 $("#export_csv").empty();
	});	*/ 
</script>
@endpush