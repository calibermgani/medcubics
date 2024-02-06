<?php $current_tab = Request::segment(3); ?> 
@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.apisettings')}} font14"></i> Security Code <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span></span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#js-help-modal" data-url="{{url('help/reason_for_visit')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
	@include ('admin/userloginhistory/tabs')
@stop

@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" ><!-- Col-12 starts -->
    <div class="box box-info no-shadow"><!-- Box Starts -->
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">@if($current_tab == 'pendingApproval') Pending Approval @elseif($current_tab == 'approvedIp') Approved IP @endif </h3>
            <div class="box-tools pull-right margin-t-2">	
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">	<!-- Box Body Starts -->
            <div class="table-responsive">
            	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">
            		@include('layouts.search_fields', ['search_fields'=>$search_fields])
            		@if(Session::get('message')!== null) 
            		<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
            		@endif
            	</div>
            	<div class="ajax_table_list hide"></div>
            	<div class="data_table_list" id="js_ajax_part">
				<table id="ex1" class="table table-bordered table-striped ">
                    <thead>
                        <tr>
                            <th>User Name</th>
							<th>Email ID</th>
							<th>Customer</th>
							<th>Practice</th>
							<th>Security Code</th>
							<th>IP </th>
							<th>Approved</th>
							<th>Security Code Attempt </th>
							<th>Date and Time of Attempt</th>
							<th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    	
                    </tbody>
                </table>
			</div>
        </div><!-- /.box-body ends -->
    </div><!-- /.box  ends -->
</div><!-- Col-12 Ends -->
@stop

@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::style('css/search_fields.css') !!}
<script>
	var api_site_url = '{{url("/")}}';
    var allcolumns = [];
    var type = "{{ last(Request::segments()) }}";
    var listing_page_ajax_url = api_site_url+'/admin/userLoginHistory/'+type; 
	var column_length = $('#ex1 thead th').length; 	
	var dataArr = {};
    var wto = '';

	function accessAll() {
		var selected_column = ['User Name','Email ID','Customer','Practices','Security Code','IP','Approved','Security Code Attempt','Date and Time of Attempt','Action'];
		var allcolumns = [];
		for (var i = 0; i < column_length; i++) {
			allcolumns.push({"name": selected_column[i], "bSearchable": true});
		}
		login_his_search(allcolumns); /* Trigger datatable */
	}	
    
    $(document).ready(function(){
    	var breadcrumb = $('li.active:last').text();
    	$('h1 > small > span').text(breadcrumb);
        getData();
    });
	
    function getData(){
        clearTimeout(wto);
        var data_arr = {};
        wto = setTimeout(function() {  
            $('select.auto-generate').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
            });			// Getting all data in select fields 
            $('input.auto-generate:visible').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
            });			// Getting all data in input fields
            dataArr = {data:data_arr};
            accessAll(); // Calling data table server side scripting
        }, 100);
    }

    $(document).on('change','.auto-generate',function(){
    	getData();
	});
	
	/* Onchange code for field End */ 
	$(document).on('click','.js-block',function(){
		var user_id = $(this).attr('data-user-id');
		var status = $(this).attr('data-user-status');
		$.ajax({
            url: api_site_url + '/admin/userStatusChange',
            type: 'post',
            data: {'_token':'<?php echo csrf_token(); ?>','user_id':user_id,'status':status},
            success: function (data) {
				js_sidebar_notification(data,'Successfully user status changed');
			}
		});
		location.reload();
	});
	
	$(document).on('click','.js-reset-code',function(){
		var userip_id = $(this).attr('data-userip-id');
		$.ajax({
            url: api_site_url + '/admin/userIpSecurityCodeRest',
            type: 'post',
            data: {'_token':'<?php echo csrf_token(); ?>','userip_id':userip_id},
            success: function (data) {
				js_sidebar_notification(data,'Successfully security attempt reset');
				$('td.attempt_code_'+userip_id).html('0');
			}
		});
		location.reload();
	});
	
</script>
@endpush