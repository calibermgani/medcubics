@extends('admin_stat_view')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-money med-breadcrum med-green"></i> Payments </small>
        </h1>
        <ol class="breadcrumb">            
            <li><a href="javascript:void(0);" class="js-print hide"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_stream_export', ['url' => 'api/paymentsexport/'])
            </li>
            <li><a href="#js-help-modal" data-url="{{url('help/payment')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-info no-shadow">                
        <div class="box-header">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Payments</h3>                    
            <div class="box-tools pull-right hide">                        
                <a  class="font13 p-r-10" style="border-right: 1px solid #ccc;"><i class="fa fa-search"></i> </a>
                <a class="btn btn-box-tool p-l-10"><i class="fa fa-download" data-placement="bottom"  data-toggle="tooltip" data-original-title="Download ERA'S"></i></a>
            </div>                    
            <div id="popover-content" class="hide">
                <div class="col-md-12 m-b-m-12 no-padding" >
                    <div class="box box-info no-shadow no-background no-border">
                        <div class="box-body form-horizontal no-padding no-background">

                            <div class="col-lg-12 col-md-12 col-sm-4 col-xs-4 no-bottom form-horizontal"  style="">                               
                                <div class="form-group">    
                                    {!! Form::label('claim', 'Search By', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green']) !!}                             
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-10 ">
                                        {!! Form::select('employer_id',array(''=>'-- Select --','last_name'=>'Payment ID','first_name'=>'Insurance','dob'=>'Check/EFT No','check_date'=>'Check Date','created_at'=>'Posted Date','user'=>'User'),null,['class'=>'form-control', 'id' => 'PatientDetail']) !!}
                                    </div>                                                     
                                </div>                                    
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-6 col-xs-6 no-bottom form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('', 'Name', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        {!! Form::text('from',null,['class'=>'form-control']) !!}
                                    </div>
                                </div>                  
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-6 no-bottom form-horizontal">
                                <div class="form-group">                         
                                    {!! Form::label('claim', 'From', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
                                        <i class="fa fa-calendar-o form-icon-billing"></i>
                                        {!! Form::text('from',null,['maxlength'=>'15','class'=>'form-control','placeholder'=>'From']) !!}
                                    </div>
                                </div>                  
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-6 no-bottom form-horizontal">
                                <div class="form-group">      
                                    {!! Form::label('claim', 'To', ['class'=>'col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label-billing med-green']) !!}
                                    <div class="col-lg-8 col-md-8 col-sm-10 col-xs-10">
                                        <i class="fa fa-calendar-o form-icon-billing"></i>
                                        {!! Form::text('To',null,['maxlength'=>'25','class'=>'form-control','placeholder'=>'To']) !!}
                                    </div>
                                </div>                  
                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-2 no-bottom form-horizontal m-b-m-8">
                                <div class="form-group">                                                
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-10">
                                        {!! Form::submit('Search', ['class'=>'btn btn-medcubics-small pull-right', 'id' => 'js-search-patient']) !!}
                                    </div>
                                </div>                  
                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div>
            </div>                    
        </div><!-- /.box-header -->
        <?php         
        $search_fields->search_fields = preg_replace("/[\n\r]/","",@$search_fields->search_fields);        
        ?>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">        
            @include('layouts.search_fields')
            @if(Session::has('ar_claim_id'))
            {!! Form::hidden('ar_var',null, ['class' => 'js-arvar']) !!}
            @endif
        </div>
       
        @if(App\Models\Payments\ClaimInfoV1::count() >0)
        <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 margin-t-10">                                       
            <a class="js-create-claim claimdetail form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i> </a> 
            <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "{{url('payments/paymentinsurance/insurance')}}" 
               class="js-show-patientsearch js-insurance-popup js_pay_ins_key claimdetail form-cursor font600 p-l-10 p-r-10" style="border-right:1px solid #fdd4ab;"><i class="fa fa-building-o"></i> <span class="text-underline">I</span>nsurance <span class="hidden-md hidden-sm hidden-xs">Payment</span></a>
            <a href = "#" data-toggle="modal" data-tile = "Post Patient Payment" data-target="#choose_claims" data-url = "{{url('payments/paymentinsurance/patient')}}" 
               class="js-show-patientsearch js_pat_pay_key form-cursor claimotherdetail font600 p-l-10 p-r-10"style="border-right:1px solid #fdd4ab;"><i class="fa fa-user"></i> <span class="text-underline">P</span>atient <span class="hidden-md hidden-sm hidden-xs">Payment</span></a>
            <a target="_blank" href="{{url('payments/get-e-remittance')}}" class="js-create-claim claimdetail form-cursor font600 p-l-10 p-r-10" id="js_e-remittance"><i class="fa fa-television"></i> E-Remi<span class="text-underline">t</span>tance</a> 
<!--<a data-toggle="modal" data-target="#erementance_details" class="js-create-claim claimdetail form-cursor font600 p-l-10 p-r-10" style="border-right:1px solid #fdd4ab;  " ><i class="fa fa-television"></i> E-Remittance</a> -->
<!-- <a data-placement="bottom" data-toggle="popover"  data-container="body" type="button" data-html="true" href="#" class="js-create-claim claimdetail margin-l-20 form-cursor font600 p-l-10 p-r-10" style="border-right:1px solid #ccc; padding-left: 20px; border-left: 2px solid #ccc;"><i class="fa fa-search"></i> Search Check</a>
<a  class="form-cursor claimotherdetail font600 p-l-10 p-r-10 hidden-sm" style="border-right: 1px solid #fdd4ab;"><i class="fa fa-download"></i> Download E-Remittance</a>       -->
            <!-- <a href = "#" data-toggle="modal" data-target="#search_check" data-url = "{{url('payments/search')}}" class="js-modalboxopen form-cursor font600 p-l-10 p-r-10" ><i class="fa fa-search"></i> Search Check</a> -->
			<a href = "{{url('payments')}}" class="form-cursor font600 p-l-10 p-r-10 fn-searchchk-reset hide" style="border-left: 1px solid #fdd4ab;" data-tile = "Reset Search" ><i class="fa fa-refresh"></i> Reset Search </a>
        </div>
        @endif
        <div class="box-body table-responsive js-append-table">
            <div class="ajax_table_list hide"></div>
            <div class="data_table_list" id="js_ajax_part">
                <table id="search_table_payment" class="table table-bordered table-striped">	

                    <thead>
                        <tr>
                            <th>Payment ID</th>                                
                            <th>Payer</th>
                            <th>Check/EFT No</th>
                            <th>Mode</th>
                            <th>Check Date</th>
                            <th>Check Amt($)</th>
                            <th>Posted($)</th>
                            <th>Un Posted($)</th>
                            <th>Created On</th>
                            <th>User</th>
                            <th></th>                              
                        </tr>
                    </thead>
                    <tbody>     
                        <!-- AJAX content will be loaded here -->           													
                    </tbody>
                </table>  
            </div>		
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!-- Modal Payment Details ends here -->
<!--End-->
@include('payments/payments/payments_popup')
@stop

@push('view.scripts')
<!-- Server script start -->

{!! HTML::script('js/datatables_serverside.js') !!}
{!! HTML::script('js/daterangepicker_dev.js') !!}
<script type="text/javascript">
    var api_site_url = '{{url('/')}}';
    var allcolumns = [];
    var listing_page_ajax_url = api_site_url + "/payments/paymentsList";
    //search_table_payment  
    var column_length = $('#search_table_payment thead th').length;
     function accessAll() { 
            var selected_column = ['Payment ID', 'Payer', 'Check/EFT No', 'Mode', 'Check Date', 'Check Amt', 'Posted', 'Un Posted', 'Created On', 'User'];
            var allcolumns = [];
            for (var i = 0; i < column_length; i++) {
                allcolumns.push({"name": selected_column[i], "bSearchable": true});
            }
            claimSearch(allcolumns); /* Trigger datatable */
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
	
    function claimSearch(allcolumns) {
        $("#search_table_payment").DataTable({
            "createdRow": function (row, data, index) {
                if (data[1] != undefined)
                    data[1] = data[1].replace(/[\-,]/g, '');
            },
            "bDestroy": true,
            "paging": true,
            "searching"	: false,
            //"processing": true,
            "info": true,
            "aoColumns" : allcolumns,
            "columnDefs": [{orderable: false, targets: [1,2,4]}],
            "autoWidth": false,
            "lengthChange": false,
            //"searchHighlight" : true,
            "searchDelay": 450,
            "serverSide": true,
            "order": [[0, "desc"], [1, "desc"]],
            "ajax": $.fn.dataTable.pipeline({
                url: listing_page_ajax_url, 
                data:{'dataArr':dataArr},
                pages: 1, // number of pages to cache
                success: function(){
                    // Hide loader once content get loaded.
                }   
            }),
            "columns": [
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
                {"datas": "id", sDefaultContent: ""},
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(".ajax_table_list").html(aData + "</tr>");
                var get_orig_html = $(".ajax_table_list tr").html();
                var get_attr = $(".ajax_table_list tr").attr("data-url");
                var get_data_targ = $(".ajax_table_list tr").attr("data-target-new");
                var get_data_pmt_info_no = $(".ajax_table_list tr").attr("data-payment-info-number");
                var data_tog = $(".ajax_table_list tr").attr("data-toggle");
                var get_class = $(".ajax_table_list tr").attr("class");
                var get_data_id = $(".ajax_table_list tr").attr("data-id");
                $(nRow).addClass(get_class).attr({'data-url': get_attr, 'data-target-new': get_data_targ, 'data-payment-info-number': get_data_pmt_info_no, 'data-toggle': data_tog, 'data-id': get_data_id});
                $(nRow).closest('tr').html(get_orig_html);
                $(".ajax_table_list").html("");
            },
            "fnDrawCallback": function (settings) {
                //var length = settings._iDisplayStart;
                //var sorting_length = settings.aLastSort.length;
                hideLoadingImage(); // Hide loader once content get loaded.
                $(".js_search_export_csv").parent('.js_claim_export').removeClass("hide");
                $(".js_search_export_pdf").parent('.js_claim_export').removeClass("hide");
            }
        });
    }
    
</script>    
<!-- Server script end -->
@endpush