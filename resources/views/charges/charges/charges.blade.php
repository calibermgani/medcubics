@extends('admin_stat_view')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="livicon med-breadcrum" data-name="list"></i> Charges </small>
        </h1>
        <ol class="breadcrumb">
            <!--<li><a href="{{ url('charges/create') }}"><i class="fa fa-plus-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Add"></i></a></li>          -->
            <li><a href="javascript:void(0);" class="js-print hide"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_stream_export', ['url' => 'api/chargesexport/'])
            </li>            
            <li><a href="#js-help-modal" class="js-help" data-toggle="modal" data-url="{{url('help/charges')}}"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
@include ('patients/billing/model-inc')

@if(session()->has('Claim_Edit_In_AR'))
<script type="text/javascript">window.top.close();</script>
@endif

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <div class="box box-info no-shadow">
        <div class="box-header">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
            <div class="box-tools pull-right margin-t-4">
                <a href="#" data-toggle="modal" accesskey="a" data-target="#create_charge" class="js-create-claim font600 font13"><span class="claimdetail"> <span class="claimdetail"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Claim</span></a>                    
               <!-- <a href="{{url('charges/create')}}" class="js-create-claim font600 font13"><span class="claimdetail"><i class="fa fa-pencil"></i> Create Claim</span></a> -->
            </div>
            <span class="btn-group col-lg-3 col-md-4 col-sm-5 col-xs-12 charge-listing-btns">                        
                <!--<a href="{{url('charges/create')}}" class="js-create-claim"><span class=" med-btn">Create Claim</span></a>
                <a href="" data-toggle="modal" data-target="#open_charge"><span  class="med-btn">Open Batch</span></a> 
                <a href="" data-toggle="modal" data-target="#create_charge"><span class="claimdetail form-cursor font600 font13"><i class="fa fa-pencil"></i> Create Claim</span></a>   -->            
            </span>           
        </div><!-- /.box-header -->
        <div class="box-body table-responsive charges-sm mobile-scroll">
            
            @include('layouts.search_fields')
            <?php             
                $currnet_page = \Request::segment(2); 
                $hold= $ready = $denied = $pending = '';
                if($currnet_page == "Hold") {
                    $hold= true;
                } elseif($currnet_page == "Ready") {
                    $ready= true;
                } elseif($currnet_page == "Denied") {
                    $denied= true;
                } else if($currnet_page == "Pending"){
                    $pending= true;
                } else {
                    $hold=  $pending = true;
                }
            ?>
            {!! Form::hidden('js_search_option_url',url('api/chargesexport/'),['id'=>'js_search_option_url']) !!}  

            <?php /*
            {!!Form::checkbox('is_pending_all', 'All','',['class' => 'js-check-searched','id'=>'c-all'])!!}<label for="c-all" class="cur-pointer font600 med-orange">All &nbsp; |&nbsp;</label>
            <span class="js_checkbox_count">
                {!!Form::checkbox('is_pending', 'Hold',$hold,['class' => 'js-check-searched','id'=>'c-hold'])!!}<label class="cur-pointer font600 med-orange " for="c-hold">Hold &nbsp; |&nbsp;</label>
                {!!Form::checkbox('is_pending', 'Ready',$ready,['class' => 'js-check-searched','id'=>'c-ready'])!!}<label class="cur-pointer font600 med-orange" for="c-ready">Ready &nbsp; |&nbsp;</label>
                {!!Form::checkbox('is_pending', 'Denied',$denied,['class' => 'js-check-searched','id'=>'c-denied'])!!}<label class="cur-pointer font600 med-orange" for="c-denied">Denied &nbsp; |&nbsp;</label>
                {!!Form::checkbox('is_pending', 'Pending',$pending,['class' => 'js-check-searched','id'=>'c-pending'])!!}<label class="cur-pointer font600 med-orange" for="c-pending">Pending</label>
            </span> 
            */ ?>
            
            <div id="js_table_search_listing">
                
                <div class="ajax_table_list hide"></div>
                <div class="data_table_list" id="js_ajax_part">
	                <table id="search_table_payment" class="table table-bordered table-striped mobile-width">   
	                    <thead>
	                        <tr>
	                            <th>Claim No</th>
	                            <th>Acc No</th>            
	                            <th>Patient Name</th>
	                            <th>DOS</th>
	                            <th>Facility</th> 
	                            <th>Rendering</th>
	                            <th>Billing</th>            
	                            <th>Payer</th>                      
	                            <th>Unbilled($)</th>                                                        
                                <th>Billed($)</th>
                                <th>Paid($)</th>
                                <th>Pat Bal($)</th>
                                <th>Ins Bal($)</th>
                                <th>AR Bal($)</th>
	                            <th>Status</th>
	                            <th>Sub Status</th>								
	                            <th class="hidden-print"></th>                   
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <!-- Append AJAX loaded content here --> 
	                    </tbody>
	                </table>  
                </div>

            </div>                   
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>  
<!-- Modal PAyment details starts here -->
<div id="create_charge" class="modal fade in">
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">New Claim</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">                        
                        {!! Form::open(['url'=>'charges/batch/create', 'id' => 'js-batch-submit', 'class' => 'medcubicsform']) !!}
                        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                            <div class="box-body-block"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->                           
                                    <div class="box no-border  no-shadow" ><!-- Box Starts -->
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  1st Content Starts -->

                                            <div class="box-body form-horizontal no-padding"><!-- Box Body Starts --> 
                                                <div class="form-group-billing">
                                                    {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                                        {!! Form::select('rendering_provider_id',array('' => '-- Select --')+(array)$rendering_providers,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}  
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    {!! Form::label('Billing Provider', 'Billing Provider', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                                        {!! Form::select('billing_provider_id',array('' => '-- Select --')+(array)$billing_providers,null,['class'=>'form-control select2 input-sm-modal-billing', "onchange"=>"getselecteddetails(this.id,this.value, 'Provider');"]) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group-billing">
                                                    {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">   
                                                        {!! Form::select('facility_id',array('' => '-- Select --')+(array)$facilities,null,['class'=>'form-control select2 input-sm-modal-billing', 'id'=>'facility_id', 'onChange' => 'changeselectval(this.value,\'Facility\', \'\');']) !!}
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group-billing">
                                                    {!! Form::label('POS', 'POS', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">      
                                                        {!! Form::select('pos_id', array(''=>'-- Select --')+(array)$pos, @$pos_id, ['class'=>'form-control select2 input-sm-modal-billing', 'id' => 'pos_id']) !!}
                                                    </div>
                                                </div>  

                                                <div class="form-group-billing">
                                                    {!! Form::label('DOS', 'DOS', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
                                                        {!! Form::text('dos_from','',['class'=>'form-control bg-white input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"From", 'id' => 'dos_from']) !!}
                                                    </div>
                                                    <div class="col-lg-1 col-md-1 col-sm-3 col-xs-5">
                                                        &nbsp; -
                                                    </div>
                                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
                                                        {!! Form::text('dos_to','',['class'=>'form-control bg-white input-sm-header-billing call-datepicker dm-date p-r-0','placeholder'=>"To", 'id' => 'dos_to']) !!}
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    {!! Form::label('Reference', 'Reference', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                                        {!! Form::text('reference',null,['maxlength'=>'20','class'=>'form-control']) !!}
                                                    </div>                                   
                                                </div>
                                            </div><!-- /.box-body Ends-->

                                        </div><!--  1st Content Ends -->                            
                                    </div><!--  Box Ends -->

                                </div><!-- General Details Full width Ends -->
                            </div><!-- Inner Content for full width Ends -->

                        </div><!--Background color for Inner Content Ends --> 
                        {!!Form::submit('Create Claim', ['class' => 'pull-right js-create-batch margin-b-5 margin-t-4 margin-r-20 btn btn-medcubics-small','accesskey'=>'c'])!!}
                        {!! Form::close() !!}                                           
                    </div>                     

                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<!-- Modal PAyment details starts here -->
<div id="search_charge" class="modal fade in">
    <div class="modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim Details</h4>
            </div>
            <div class="modal-body">
                <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">                        
                        <div class="js_claim_search col-lg-12 col-md-12 col-sm-12 col-xs-12">
                           <?php /* ?>
                            @include('charges/charges/search_filter_option')                                    
                            <?php */ ?>
                        </div>                                     
                    </div>    

                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
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
<?php 
$currnet_page = ($currnet_page != '')?'/'.$currnet_page:"/Hold,Pending"; 
//dd($currnet_page);?>
<script type="text/javascript">
	$('input[type="text"]').attr('autocomplete','off');
    var api_site_url = '{{url("/")}}';   
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url+"/charges/chargesList"; 
    url_charges = '';

    //search_table_payment
	/* Search function start */
	setTimeout(function(){		
		var vals = ["Hold", "Pending"];
		//$('select#status').select2().val(['Hold','Pending']).trigger("change")
	}, 1500)
	var column_length = $('#search_table_payment thead th').length; 	
	 dataArr = [];	
  
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
        
    $(document).ready(function(){
        //$(".selholdBlk").prop("disabled", true);
        $(".selClaimStatus").trigger("change");
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
             });                                                                        // Getting all data in input fields
             dataArr = {data:data_arr};                                                 
             accessAll();                                                          // Calling data table server side scripting
        }, 100);
    }
	/* function for get data for fields End */ 	
		 
	 /* Main listing page Check box option function */      
	/* $(document).on("ifToggled click", ".js-check-searched", function(){          
		status_val = []; 
	  
		if($(this).val() == "All" && $(this).is(":checked")){
				$('input[name="is_pending"]').prop('checked', false);
				status_val = "All";               
		} else if($(this).val() != "All" && $(this).is(":checked")){
				$('input[name="is_pending_all"]').prop('checked', false); 
		} else if($(this).val() != "All" &&     !$(this).is(":checked") && $("input[name='is_pending']:checked").length <=0){
			   status_val = "All";            
		}
		displayLoadingImage(); // Set initial loader symbol.
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
			   
				if((status_val== 'Hold,Ready,Denied,Pending')||(status_val== 'All')) {
					$('input[name="is_pending"]').prop('checked', true);
					$('input[name="is_pending_all"]').prop('checked', true); 
				 }   
				// In case one check box  is unchecked firsted check box checked option is removed  
				var checkBoxCount = $('input[name="is_pending"]:checkbox:not(":checked")').length;
				if((checkBoxCount > 0) && (checkBoxCount < 4)){
					$('input[name="is_pending_all"]').prop('checked', false); 
				}     
				accessAll(url_charges); //Trigger datatable 
			});
		}, 500);  
	});
	  */  

    /* Selvakumar code for dynamic search */
	function claimSearch(allcolumns, url_charges) { 
		search_url = listing_page_ajax_url;
        if(url_charges != '' && typeof url_charges!= "undefined")    {
            search_url = url_charges; 
        }   
		var dtable = $("#search_table_payment").DataTable({			
			"createdRow": 	function ( row, data, index ) {
			if(data[1] != undefined)
    			data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"searching": false,
			"paging"	: 	true,
			"info"		: 	true,
			//"aoColumns"	: 	allcolumns,
			 "columnDefs":   [ { orderable: false, targets: [8,9,10,11,12,13,15] } ], 
			"autoWidth"	: 	false,
			"lengthChange"		: false,
			//"processing": true,
			//"searchHighlight"	: true,
			"searchDelay": 450,
			"serverSide": true,	
			"order": [[0,"desc"],[1,"desc"]],
			
            "ajax": $.fn.dataTable.pipeline({
                url: search_url, 
                data:{'dataArr':dataArr},
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
			}
		});
	}

    $('.js_claim_export').removeClass("hide");

	function getselecteddetails(id, value, type)
	{
		url = api_site_url + '/patients/getproviderdetail/' + value + '/' + type;
		if (value != '') {
			$.get(url, function (result) {
				var def_facility = result.data.providers.def_facility;
				if(def_facility!=0){
					$("#facility_id").val(def_facility).change();
				}else{
					$("#facility_id").val('').change();
				}
			});   
		}
	}

	/* Charge analysis report hold fields start  */
	$(document).on("change", ".selClaimStatus", function(){
		var isHold = 0;
		$("select.selClaimStatus option:selected").each(function () {
			if($(this).text() == 'Hold')
			isHold = 1;
		});

		//if($(this).val() == 'Hold'){
		if(isHold) {    
			$(".selholdBlk").prop("disabled", false);
		} else {        
			$(".selholdBlk").select2('val', '').val("").prop("disabled", true); // Clear already selected hold reason and release date.
		}
	});
	/* Charge analysis report hold fields end  */

</script>
@endpush