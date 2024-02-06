@extends('admin_stat_view')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.payments.payments')}} med-breadcrum med-green"></i> Payments <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>E-Remittance</span></small>
        </h1>
        <ol class="breadcrumb">            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @include('layouts.practice_module_stream_export', ['url' => 'api/paymentsE-remittance/'])
            <li><a href="#js-help-modal" data-url="{{url('help/payment')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow">                
        <div class="box-header">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">E-Remittance</h3>                    
            <div class="box-tools pull-right">                        
              <input type="button" name="post" value="Auto Post" accesskey="p" class="btn btn-medcubics-small margin-t-2" id="js-post" />
            </div>                    
		
        </div><!-- /.box-header -->	
		@include('layouts.search_fields', ['search_fields'=>$search_fields])
        @if(Session::has('ar_claim_id'))
        {!! Form::hidden('ar_var',null, ['class' => 'js-arvar']) !!}
        @endif
        <div class="btn-group col-lg-12 col-md-12 col-sm-12 col-xs-12 font13 hidden-print margin-b-4 margin-t-10">                                       				
			<a href = "javascript:void(0);" data-type="Archive" class="js_archive_era form-cursor font600 p-l-10 p-r-10" data-placement="top"  data-toggle="tooltip" data-original-title="Archive">
                <i class="fa fa-pie-chart"></i> Archive </a> |
				
			<a href = "javascript:void(0);" class="form-cursor font600 p-l-10 p-r-10" data-placement="top"  data-toggle="tooltip" data-original-title="Show Archive">
                {!! Form::checkbox("archive_list",'yes',null,["class" => "js-era-status","id"=>"SA"]) !!}&nbsp;<label for="SA" class="font600 no-bottom ">Show Archive</label> </a> |
			
			<a href = "javascript:void(0)" class="js_posted_era form-cursor font600 p-l-10 p-r-10" data-placement="top"  data-toggle="tooltip" data-original-title="Posted ERA">
               {!! Form::checkbox("posted_list",'yes',null,["class" => "js-era-status","id"=>"P"]) !!}&nbsp;<label for="P" class="font600 no-bottom ">Posted</label> </a> | 
				
			<a href = "javascript:void(0)" class="js_unposted_era form-cursor font600 p-l-10 p-r-10" data-placement="top"  data-toggle="tooltip" data-original-title="Unposted ERA">
               {!! Form::checkbox("unposted_list",'yes',null,["class" => "js-era-status","id"=>"UP"]) !!}&nbsp;<label for="UP" class="font600 no-bottom ">Unposted</label> </a>
				
			
			<span class="btn-group pull-right">   
				 <a href = "javascript:void(0)" class="js_era_download form-cursor font600 p-l-10 p-r-10" data-placement="top"  data-toggle="tooltip" data-original-title="ERA Reports">
                <i class="fa fa-pie-chart"></i> Generate Report </a>
			</span>
        </div>
        <div class="box-body table-responsive js-append-table">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                
                @if(Session::get('message')!== null) 
                <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
                @endif
            </div>
            <div class="ajax_table_list hide"></div>
            <div class="data_table_list" id="js_ajax_part">
            <table id="search_table_payment" class="table table-bordered table-striped">
                <thead>
                    <tr>  
                        <th class="td-c-3"><input type="checkbox" class="js-era-post" data-claim-count="0" id="checkAll" ><label for='checkAll' class="no-bottom">&nbsp;</label></th>
                        <th>Received Date</th>
                        <th>Insurance</th>
                        <th>Check No</th>                               
                        <th>Check Date</th>
                        <th>Check Amount ($)</th>
                        <th>Posted ($)</th>
                        <th>Un Posted ($)</th>
                        <th class="lastwidth"></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<style type="text/css">
    @media (min-width: 1460px) and (max-width: 1558px){
      .lastwidth{
        width: 90px !important;
        text-align: center; 
      }
    }
    @media (min-width: 1324px) and (max-width: 1460px){
      .lastwidth{
        width: 100px !important;
        text-align: center; 
      }
    }
    @media (min-width: 768px) and (max-width: 1323px){
      .lastwidth{
        width: 105px !important;
        font-size: 12px;
        text-align: center; 
      }
    }
</style>
<!--End-->
@include('payments/payments/payments_popup')
@stop
@push('view.scripts')
{!! HTML::script('js/daterangepicker_dev.js') !!}
{!! HTML::script('js/datatables_serverside.js') !!}
<script>
    var api_site_url = '{{url("/")}}';   
    var allcolumns = [];
    
    var type = "{{ last(Request::segments()) }}";
    var listing_page_ajax_url = api_site_url+'/payments/get-e-remittance';
    var column_length = $('#search_table_payment thead th').length;  
     dataArr = [];  

    function accessAll() {         
        var selected_column = ['Received Date','Insurance','Check No','Check Date','Check Amount','Posted','Unposted'];
        var allcolumns = [];
        for (var i = 0; i < column_length; i++) {
            allcolumns.push({"name": selected_column[i], "bSearchable": true});
        }
        login_his_search(allcolumns); /* Trigger datatable */
    }   
   

    var dataArr = {};   
    var wto = '';

    $(document).ready(function(){
        getData();
        if ( $.fn.dataTable.isDataTable( '#search_table_payment' ) ) {
            table.destroy();  // Destroy old instance
        }
    });
	$(document).on('change','.js-era-status',function(){
		getData();
	});
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
			 
			$('input.js-era-status').each(function(){
				data_arr[$(this).attr('name')] = JSON.stringify($(this).prop("checked"));
			});
             dataArr = {data:data_arr};
             accessAll();                                                                       // Calling data table server side scripting
        }, 100);
    }

    $(document).on('change','.auto-generate',function(){
        getData();
    });
    /* Onchange code for field End */ 

    function login_his_search(allcolumns) { 
        search_url = listing_page_ajax_url;
        var dtable = $("#search_table_payment").DataTable({          
            "createdRow":   function ( row, data, index ) {
            if(data[1] != undefined)
                data[1] = data[1].replace(/[\-,]/g, '');
                            },      
            "bDestroy"  :   true,
            "searching": false,
            "paging"    :   true,
            "info"      :   true,
            // "aoColumns"   :   allcolumns,
            "columnDefs":   [ { orderable: false, targets: [0,6,7,8] },
            // Maximize the width of Insurance Column 
            // Revision 1 - Ref: MR-2612 1 Augest 2019: Pugazh
                                { width: "20px", targets: [0] },
                                { width: "120px", targets: [1] },
                                { width: "75px", targets: [8] },
                                { width: "20%", targets: [2] }
                            ], 
            "autoWidth" :   false,
            "lengthChange"      : false,
            //"processing": true,
            //"searchHighlight" : true,
            "searchDelay": 50,
            "serverSide": true, 
            "order": [[1,"desc"]],
            
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
                $(".js_search_export_pdf").parent('.js_claim_export').removeClass("hide");    
            },
            "fnDrawCallback": function(settings) {
                hideLoadingImage(); // Hide loader once content get loaded.             
            }
        });
    }
</script>
@endpush