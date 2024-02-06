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
            <i class="fa fa-bars font14"></i><h3 class="box-title"> Ignore Security Code </h3>
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
                            <th>User Type</th>
							<th>Customer</th>
							<th>Practice</th>
							<th>User</th>
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
	 dataArr = [];	

     $(document).ready(function(){
		var breadcrumb = $('li.active:last').text();
		$('h1 > small > span').text(breadcrumb);
		$("#Users").after('<button type="submit" class="btn generate-btn margin-b-4 margin-t-20 margin-r-5 "> Set Approval </button>');
		$("#practice_user_type").addClass('col-lg-12').css("width", "").css("padding-left","0px");
		$("#s2id_practice_user_type").wrap('<div style="width:200px;"></div>');
		getData();
	});

	var data_arr = {};

	function getData(){   
		$('select.auto-generate').each(function(){
			data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));                    
		});                                                                                // Getting all data in select fields 
		$('input.auto-generate:visible').each(function(){
			data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
		});                                                                                // Getting all data in input fields
		dataArr = {data:data_arr};

		login_his_search(); /* Trigger datatable */
	}

    // ------------ select user type disable selectbox start -----------

	$(document).on('change','.selFnUserTp ',function(){
		getData();
		
		var user_type = JSON.parse(dataArr['data']['practice_user_type']);

		if(user_type.length == 0){
			$('select.selFnCust').prop("disabled", false);
			$('select.selFnPrac').prop("disabled", false);
			$('select.selFnUsr ').prop("disabled", false);
		}else{               
			$('select.selFnCust').attr('disabled', 'disabled');
			$('select.selFnPrac').attr('disabled', 'disabled');
			$('select.selFnUsr ').attr('disabled', 'disabled');
			$(".selFnCust").select2("val", "");
			$(".selFnPrac").select2("val", "");
			$(".selFnUsr").select2("val", ""); 
		}
	});

	// ------------ select user type disable selectbox end -----------
	// ------------ selectbox value appdend ajax start -----------

    $(document).on('change','.selFnCust ',function(){
        getData();
        
        sel_Cust_id = dataArr['data']['Customer'];

        $.ajax({
            data : {'_token':'<?php echo csrf_token(); ?>','sel_Cust_id':sel_Cust_id},
            type:'post',
            url: api_site_url + '/admin/usersettings',
            success: function(prac){
                if(prac != undefined ){
                    $(".selFnPrac option").not(':first').remove();
                    $(".selFnPrac").select2("val", "");
                    $(".selFnUsr").select2("val", "");  
                    $.each(prac, function(index, value){
                        var optionValue = index;
                        var optionText = value;
                        $('select.selFnPrac ').append('<option value=' + optionValue + '>' + optionText + '</option>'); 
                    });  
                } 
            }
        });
    });
    
    $(document).on('change','.selFnPrac ',function(){
        getData();

        sel_prac_id = dataArr['data']['Practice'];
        // alert(sel_prac_id);

        $.ajax({
            data : {'_token':'<?php echo csrf_token(); ?>','sel_prac_id':sel_prac_id},
            type:'post',
            url: api_site_url + '/admin/usersettings',
            success: function(user_list){
                if(user_list != undefined){
                    $(".selFnUsr  option").not(':first').remove();
                    $(".selFnUsr").select2("val", "");              
                    $.each(user_list, function(index, value){
                        var optionValue = index;
                        var optionText = value;
                        $('select.selFnUsr ').append('<option value=' + optionValue + '>' + optionText + '</option>');
                    });  
                }    
            }
        });
    });
    
    // ------------ selectbox value appdend ajax end -----------


// ------------ Datatable value ajax start -----------
    function login_his_search() {

		search_url = listing_page_ajax_url;
		var dtable = $("#ex1").DataTable({			
			"createdRow": 	function ( row, data, index ) {
			if(data[1] != undefined)
    			data[1] = data[1].replace(/[\-,]/g, '');
							},		
			"bDestroy"	:	true,
			"searching": false,
			"paging"	: 	true,
			"info"		: 	true,
			//"aoColumns"	: 	allcolumns,
			// "columnDefs":   [ { orderable: false, targets: [0] } ], 
			"autoWidth"	: 	false,
			"lengthChange"		: false,
			//"processing": true,
			//"searchHighlight"	: true,
			"searchDelay": 450,
			"serverSide": true,	
			"order": [[4,"desc"]],
			
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
				hideLoadingImage(); // Hide loader once content get loaded.				
			}
		});

        // ------------ Datatable value ajax ends -----------

        $(document).on('click','.removeApproval',function(){
            var user_id = $(this).attr('data-user-id');
            
            $.ajax({
                data : {'_token':'<?php echo csrf_token(); ?>','user_id':user_id},
                type:'post',
                url: api_site_url + '/admin/userSettingsRemoveApproval',
                success: function(data){
                    js_sidebar_notification(data,'Successfully user status changed');
                    $('.remove-row_'+user_id).remove();
                    login_his_search();
                }
            })
        });
		
	}

    $(document).on('click','.generate-btn',function(){
        getData();

        var sel_user_id = dataArr['data']['Users'];
        var sel_prac_id = dataArr['data']['Practice'];
        var user_type = dataArr['data']['practice_user_type'];

        if( Object.values(sel_prac_id).indexOf('0') > -1){
            alert(" Remove Practice All!!");            
        } else {            
            if(user_type.length > 2){               
				$.ajax({
					data : {'_token':'<?php echo csrf_token(); ?>','user_type':user_type},
					type:'post',
					url: api_site_url + '/admin/securityCodeApproval',
					success: function(daata){
						js_sidebar_notification(daata,'Successfully user status changed');
					}
				});
			}else if(sel_user_id.length > 2 || sel_prac_id.length > 2){
				$.ajax({
					data : {'_token':'<?php echo csrf_token(); ?>','sel_user_id':sel_user_id,'sel_prac_id':sel_prac_id},
					type:'post',
					url: api_site_url + '/admin/securityCodeApproval',
					success: function(daata){
						js_sidebar_notification(daata,'Successfully user status changed');
					}
				});
			}else{
				// console.log("else:"+sel_user_id.length);
			}
			login_his_search();            
        }
    });
</script>
@endpush