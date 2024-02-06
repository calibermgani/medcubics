@extends('admin')
@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
				<small class="toolbar-heading"><i class="fa {{ Config::get('cssconfigs.payments.payments') }}" data-name="users"></i> Reports </small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>

				@if($checkpermission->check_adminurl_permission('api/admin/invoice/{export}') == 1)
					<li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share-square-o" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
					@include('layouts.practice_module_export', ['url' => 'api/admin/invoice/'])
					</li>
				@endif

				@if($checkpermission->check_adminurl_permission('help/{type}') == 1)
					<li><a href="#js-help-modal" data-url="{{url('help/invoice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
				@endif
			</ol>
		</section>
	</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="box box-view no-shadow no-bottom">        
            <div class="box-body yes-border border-radius-4 border-green">
                <!-- SEARCH FIELDS FILE -->
        
		<div class="search_fields_container col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
			{!! Form::label('Customer', 'Customer', ['class'=>'control-label font600']) !!}				
			{!! Form::select('customer_id', array('' => '-- Select --')+(array)@$customers,null,['class'=>'select2 form-control',
		'id'=>'js_customer_report-change']) !!}
			
		</div>
		<div class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
			{!! Form::label('Practice', 'Practice', ['class'=>'control-label font600']) !!}
			{!! Form::select('practice_id', array('' => '-- Select --'),null,['id'=>'js_report','class'=>'select2 form-control']) !!}
		</div>
		<div class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
		 <div class="right-inner-addon">
                                <label for="select_transaction_date" class="control-label font600">Transaction Date</label>
                                <input class="date auto-generate bg-white form-control form-select js-date-range" id="transaction_date" autocomplete="off" readonly="readonly" data-label-name="Transaction Date" name="select_transaction_date" type="text" value=""><i class="fa fa-calendar-o"></i>
                            </div>
		</div>
		<div class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
		</div>
		</div>           
                
            </div>
        </div>
    </div>
</div>
<div class="js_spin_image hide">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center">
        <i class="fa fa-spinner fa-spin med-green font20"></i> Processing
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_claim_list_part hide"></div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 js_exit_part text-center hide">
    <input class="btn btn-medcubics-small" id="js_exit_part" value="Exit" type="button">
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  margin-t-10">
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
           <i class="fa fa-bars"></i><h3 class="box-title">Reports -> Customer</h3>
            <div class="box-tools pull-right margin-t-2">
              

            </div>
        </div><!-- /.box-header -->
		
        <div class="box-body table-responsive" id="js_user_activity">
            <table id="example1" class="report-insurance-admin table table-bordered table-striped">
                <thead>
                    <tr>
                       <th>Practice Name</th>                       	
                       	<th>Patient</th> 
						<th>Claims </th>	                      	
                        <th>Payments</th>
						<th>Provider</th>
                        <th>Facility</th>
                    </tr>
                </thead>
				<tbody>
				<tr data-activity="practice" data-module="" data-action="" data-url="" class="js-useractivity-click clsCursor">
				<td></td>
				<td></td>
				<td></td>
				<td></td>	
				<td></td>
				<td></td>
				</tr>				 
					
			</tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
  
@stop

@push('view.scripts')

{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::style('css/search_fields.css') !!}
{!! HTML::script('js/datatables_serverside.js') !!}	


<script type="text/javascript">
/*
     * This Function For Cutomer Report Code Updation
     * Author		: Kriti Srivastava
     * Created on	: 23August2021
	 * JIRA Id		: MEDV2-1431
     */

    var api_site_url = '{{url('/')}}';   	
	var dataArr = {};   
	 $("#js_customer_report-change").change(function () {
			selectcustomer($(this).val()); 			
		});
	
	 function selectcustomer(id) {
        $.ajax({
            type: "GET",
			dataType: 'json',
            url: api_site_url + '/admin/show_practice/' + id,            
            success: function (res) {
				 $("#js_report").empty(); 
				 $("#js_report").append(res);		// second dropdown updation 
			 if(res)
                {                                   // table updation
                    $.each(res,function(key,value){
                        $('select#js_report').append($("<option/>", {
                           value: key,
                           text: value
                        }));					
						
                    });
					$('#js_report').trigger('change'); 					
                }			
            }
        });
    }
		
	// RETRIEVING PRACTICE DATA	//
	
	 $("#js_report").change(function () {
			selectpractice($(this).val());		
	   });
			
	 function selectpractice(id) {
		 var trHTML = '';
		 var transaction_date = $('#transaction_date').val();
        $.ajax({
            type: "GET",
			dataType: 'json',
			data  : {transaction_date: transaction_date},
            url: api_site_url + '/admin/show_practice_data/' + id ,            
            success: function (res) {			
						if(res)
						{	
						 $("#example1 tbody tr").empty();					
						 trHTML += '<tr><td>' + res.customer_practice + '</td><td>' + res.patients + '</td><td>' + res.claim_cnt + '</td><td>' + res.payment + '</td><td>' + res.provider + '</td><td>' + res.facilty + '</td></tr>';	
						 $("#example1").append(trHTML);	
						}			
	
            },
			error: function (error){
			 $("#example1 tbody tr").empty();
			 trHTML += '<tr><td>' + 'No Record Found' + '</td></tr>';				
			 $("#example1").append(trHTML);				
			}		
	
        });
    }
</script>
@endpush