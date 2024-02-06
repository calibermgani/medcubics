/*/////////////////////////////////////////////////////////////////////////////
 ----------- INDEX -------------
 1. Common throughout Medcubics
 2. USPS Address operations
 3. Practice Settings

 Author: 	Kannan
 Date: 		23 May 2018
 Updated:	Kannan
 
Admin page related functions
 
/*/////////////////////////////////////////////////////////////////////////////

// 
$(".js-provider-change").change(function () {
    var current_provider_id = $(this).val();
    if (current_provider_id != '' && $('#enumeration_type').val() != 'NPI-2') {
        $('.js-other-provider-options .js-other-provider-span').removeClass('hide');
        $('#js-provider_type_' + current_provider_id).addClass('hide');
        $('#js-provider_type_' + current_provider_id).find('input[type=checkbox]:checked').removeAttr('checked');
        $('#js-provider_type_' + current_provider_id).find('input[type=checkbox]').iCheck('update');
        $('.js-other-provider-options').removeClass('hide');
        $('.bottom-space-15').addClass('hide');
    } else {
        $('.bottom-space-15').removeClass('hide');
        $('.js-other-provider-options').addClass('hide');
        $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
        $('.js-other-provider-options').find('input[type=checkbox]').iCheck('update');
    }
});

$(".js-provider-change-master").change(function () {
    var current_provider_id = $(this).val();
    if (current_provider_id != '') {
        if ($('#enumeration_type').val() == 'NPI-2' && current_provider_id != '1' && current_provider_id != '5') {
            js_alert_popup("You can't choose this option");
            $("#provider_types_id").select2("val", "");
            $('.bottom-space-15').removeClass('hide');
            $('.js-other-provider-options').addClass('hide');
            $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
            $('.js-other-provider-options').find('input[type=checkbox]').iCheck('update');
        } else {
            $('.js-other-provider-options .js-other-provider-span').removeClass('hide');
            $('#js-provider_type_' + current_provider_id).addClass('hide');
            $('#js-provider_type_' + current_provider_id).find('input[type=checkbox]:checked').removeAttr('checked');
            $('#js-provider_type_' + current_provider_id).find('input[type=checkbox]').iCheck('update');
            $('.js-other-provider-options').removeClass('hide');
            if(current_provider_id !='5')
            $('.bottom-space-15').addClass('hide');
            else
            $('.bottom-space-15').removeClass('hide');

            if ($('#enumeration_type').val() == 'NPI-2') {
                $('#js-provider_type_2').addClass('hide');
                $('#js-provider_type_3').addClass('hide');
                $('#js-provider_type_4').addClass('hide');
            }
        }
    } else {
        $('.bottom-space-15').removeClass('hide');
        $('.js-other-provider-options').addClass('hide');
        $('.js-other-provider-options').find('input[type=checkbox]:checked').removeAttr('checked');
        $('.js-other-provider-options').find('input[type=checkbox]').iCheck('update');
    }
    //bottom-space-15
});

function getAjaxResponse(url, form_data) {
    //$(".js_spin_image").removeClass("hide");
    //$(".js_claim_list_part").html('');
    //processingImageShow("#js_ajax_part","show");
    $(".js_claim_list_part").html('');
    $(".js_exit_part").addClass("hide");
	displayLoadingImage(),
        $.ajax({
            type: 'POST',
            url: url,
            data: form_data,
            success: function (response) {
                // console.log(response);
                //$(".js_spin_image").addClass("hide");
                //processingImageShow("#js_ajax_part","hide");
                $(".js_claim_list_part").html(response).removeClass("hide");
                //$("#js_ajax_part").addClass("hide");
                $(".js_exit_part").removeClass("hide");
                var page_name = url.split("/").pop();
                if (page_name == "payments")
                    $("#list_noorder").dataTable({ "paging": true, "iDisplayLength": 25, "info": true, "lengthChange": false, "searching": false, "aaSorting": [], "scrollX": true });
                else if (page_name == "financial")
                    $("#example").dataTable({ "paging": false, "info": false, "lengthChange": false, "searching": false, "ordering": false });
                else
                    $("#list_noorder").dataTable({ "paging": true, "iDisplayLength": 25, "info": true, "lengthChange": false, "searching": false, "aaSorting": [] });

                $("#sort_list_noorder").DataTable({
                    "aaSorting": [],
                    "bPaginate": false,
                    "bLengthChange": false,
                    "bFilter": true,
                    "bInfo": false,
                    "bAutoWidth": false,
                    //"scrollX": true,
                    "responsive": true,
                    "searching": false
                });
                $(".js_filter_search_submit").prop("disabled", false);
                checkTableListForExport();
				hideLoadingImage();
                //  $.AdminLTE.boxWidget.activate();                
                //openNewReportTab();
            }
        });
}