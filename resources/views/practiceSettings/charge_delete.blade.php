@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-list"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>Charge Delete</span></small>
        </h1>
        <ol class="breadcrumb hide">
            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            
                @include('layouts.practice_module_export', ['url' => 'practice/charge/delete/export/'])
                <input type="hidden" name="report_controller_name" value="chargeDeleteController" />
                <input type="hidden" name="report_controller_func" value="" />
                <input type="hidden" name="report_name" value="Charge Analysis Detailed" />
            <li><a href="{{ url('reports/financials/list') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/charge_report')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
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
                {!! Form::open(['data-url'=>'practice/search/chargeDelete','url'=>'practice/search/chargeDelete','onsubmit'=>"event.preventDefault();",'id'=>'js-bootstrap-searchvalidator', 'name'=>'medcubicsform']) !!}
                @include('layouts.search_fields', ['search_fields'=>$search_fields])
                {!! Form::close() !!}
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


<div class="confirmation-modal modal" id="js-delete-confirmation-block" tabindex="-1" role="dialog" >
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Alert</h4>
            </div>
            <div class="modal-body text-center med-green font600">Are you sure would you like to delete claim?</div>
            <div class="modal-footer">
                <button class="width-60 confirm btn btn-medcubics-small" type="button" data-dismiss="modal">Yes</button>
                <button class="width-60 cancel btn btn-medcubics-small" type="button" data-dismiss="modal">No</button>
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
    var listing_page_ajax_url = api_site_url+"/practice/search/chargeDelete"; 

    //--------------------------------- FORM SUBMIT ----------------------
    $(".js-search-filter").on("click",function(){
        getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
    });

    $(document).on('click', '.pagination li a', function (e) {
        e.preventDefault();
        var pagination = $(this).attr("href").split('page=');
        var get_form_id = $('.js-search-filter').parents('form').attr("id");
        var dataurl = $('#' + get_form_id).attr("data-url");
        var form_data = $("#" + get_form_id).serialize();
        if (typeof dataurl != 'undefined') {
            var url = api_site_url + '/' + dataurl + '/pagination?page=' + pagination[1];
        }
        getAjaxResponse(url, form_data);
    });
    
    function getAjaxResponse(url, form_data) {
        $(".js_claim_list_part").html('');
        $(".js_exit_part").addClass("hide");
		displayLoadingImage(),
        $.ajax({
            type: 'POST',
            url: url,
            data: form_data,
            success: function (response) {
                $(".js_claim_list_part").html(response).removeClass("hide");
                $(".js_exit_part").removeClass("hide");
                table = $("#charge_del").DataTable({
                    "aaSorting": [],
                    "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": true,
                    "bInfo": false,
                    "bAutoWidth": false,
                    "responsive": true,
                    "searching": false
                });
                $(".js-search-filter").prop("disabled",false);
				hideLoadingImage();
            }
        });
    }

    $(document).on('click', '#charge_del .delete_charge', function(e){
        e.preventDefault();
        id = $(this).data('id');
        $("#js-delete-confirmation-block").modal().on('click', '.confirm', function(){
            $.ajax({
                method:'POST',
                url:"{{url('practice/charge/delete')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                },
                data:{id:id},
                success:function(result){
                    if(result.success==1){
                        js_sidebar_notification("success", "Claim - "+result.claim_id+' '+result.message);
                        getAjaxResponse(listing_page_ajax_url, $("#js-bootstrap-searchvalidator").serialize());
                    }else{
                        js_sidebar_notification("error", result.message);
                    }
                }
            });
        });
    });
    
</script>    
@endpush
<!-- Server script end -->
