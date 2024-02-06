if($('input[name="report_name"]').length>0){
	var name = ($('input[name="report_name"]').val()).replace(/-/g, '').toLowerCase();
	var report_name = name.replace(/\s+/g, '-').toLowerCase();
	var function_name = $('input[name="report_controller_func"]').val();
	var controller_name = $('input[name="report_controller_name"]').val();

	$('.js_search_export_pdf').click(function(){
        $('.pdf_report_export_spinner').removeClass('hide');
        $('.js_search_export_pdf').addClass('hide');
        var append = $('#append_report_list_pdf').html('<div class="col-md-9" id="alert-notes-msg">'+report_name+'.pdf</div><div class="col-md-3 no-padding"><span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span></div>');
        var url = api_site_url+"/reports/export_pdf/"+report_name.replace(/_/g, '-').toLowerCase();
        var data_arr = [];
        form = $('form').serializeArray();
        form_data = "<form id='export_pdf'>";

        $('select.auto-generate').each(function(){
            data_arr.push({
                name : $(this).attr('name'),
                value: ($(this).select2('val'))
            });
        });
        $('input.auto-generate:visible').each(function(){
            data_arr.push({
				name : $(this).attr('name'),
				value:  $(this).val()
			});
		});
		data_arr.push({
			name : "controller_name",
			value:  controller_name
		});
		data_arr.push({
			name : "function_name",
			value:  function_name
		});
		data_arr.push({
			name : "report_name",
			value:  report_name
		});
		data_arr.push({
			name : "practice_id",
			value:  $("input[name='practice_id']").val()
		});
        $.each(data_arr,function(index,value){
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
                    var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                }
            } else {
                if(value.value != "") {
                    form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
            }
        });
        form_data  += "<input type='hidden' name='export' value='pdf'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
        });
        $.ajax({
            url: url,
            type: 'POST',
            data : $(form_data).serialize(),
            success: function(data) {
                generate_report();
            }
        });
    });
    /*Ajax call end*/

    $('.js_search_export_csv').click(function(){
        $('.xlsx_report_export_spinner').removeClass('hide');
        $('.js_search_export_csv').addClass('hide');
        $('#append_report_list_xlsx').removeClass('hide');
        var append = $('#append_report_list_xlsx').html('<div class="col-md-9" id="alert-notes-msg">'+report_name+'.xlsx</div><div class="col-md-3 no-padding"><span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span></div>');
        var url = api_site_url+"/reports/streamcsv/export/"+report_name.replace(/_/g, '-').toLowerCase();
        var data_arr = [];
        form = $('form').serializeArray();
        form_data = "<form id='export_csv'>";

        $('select.auto-generate').each(function(){
            data_arr.push({
                name : $(this).attr('name'),
                value: ($(this).select2('val'))
            });
        });
        $('input.auto-generate:visible').each(function(){
            data_arr.push({
				name : $(this).attr('name'),
				value:  $(this).val()
			});
		});
		data_arr.push({
			name : "controller_name",
			value:  controller_name
		});
		data_arr.push({
			name : "function_name",
			value:  function_name
		});
		data_arr.push({
			name : "report_name",
			value:  report_name
		});
		data_arr.push({
			name : "practice_id",
			value:  $("input[name='practice_id']").val()
		});
        $.each(data_arr,function(index,value){
            if($.isArray(value.value)) {
                if(value.value.length > 0) {
                    var avoid ="[]"
                    form_data += "<input type='text' name='"+value.name.replace(avoid, '')+"' value='"+value.value+"'>";
                }
            } else {
                if(value.value != "") {
                    form_data += "<input type='text' name='"+value.name+"' value='"+value.value+"'>";
                }
            }
        });
        form_data  += "<input type='hidden' name='export' value='xlsx'><input type='hidden' name='_token' value = '"+$('input[name=_token]').val()+"'>";
        form_data += "</form>";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: url,
            type: 'POST',
            data : $(form_data).serialize(),
            success: function(data) {
                generate_report();
            }
        });
    });

    /*Generate report start*/
    function generate_report(){
        var url = api_site_url+"/reports/export_pdf/generate_report/"+report_name.replace(/_/g, '-').toLowerCase();

        $.ajaxSetup({
            error: function (x, status, error) {
                console.log(x);
                if (x.status == 403) {
                    console.log("Sorry, your session has expired. Please login again to continue");
                } else {
                    console.log("An error occurred: " + status + "nError: " + error);
                }
            }
        });
        $.ajax({
            url: url,
            type: 'GET',
            success: function(data){
                if (data.length !== 0) {
                    $.each(data, function(key, value){
                        var file_path = value.report_file_name;
						var download_link = value.download_link;

                        if (value.status == "Inprocess") {
                            if (value.report_type == "xlsx") {
                                if ($(".js_search_export_csv").is(":visible")) {
                                    $('.xlsx_report_export_spinner').addClass('hide');
                                } else{
                                    $('.xlsx_report_export_spinner').removeClass('hide');
                                }
                            }else if(value.report_type == "pdf"){
                                if ($(".js_search_export_pdf").is(":visible")) {
                                    $('.pdf_report_export_spinner').addClass('hide');
                                }else{
                                    $('.pdf_report_export_spinner').removeClass('hide');
                                }
                            }
                            var status = '<span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span>'
                        }else if(value.status == "Pending"){
                            if (value.report_type == "xlsx") {
                                $('.xlsx_report_export_spinner').addClass('hide');
                                if ($(".js_search_export_csv").is(":visible")) {
                                    $('.xlsx_report_export_download').addClass('hide');
                                }else{
                                    $('.xlsx_report_export_download').removeClass('hide');
                                }
                                js_sidebar_notification('success','Excel Ready to Download');
                                //$('#xlsx_report_export_download').attr({href:""+api_site_url+'/'+file_path+"", data_id: ""+value.id+"", data_report_type:""+value.report_type+""});
								$('#xlsx_report_export_download').attr({href:""+download_link+"", data_id: ""+value.id+"", data_report_type:""+value.report_type+"", target: "_blank"});
                            }else if(value.report_type == "pdf"){
                                $('.pdf_report_export_spinner').addClass('hide');
                                if ($(".js_search_export_pdf").is(":visible")) {
                                    $('.pdf_report_export_download').addClass('hide');
                                }else{
                                    $('.pdf_report_export_download').removeClass('hide');
                                }
                                js_sidebar_notification('success','PDF Ready to Download');
                                //$('#pdf_report_export_download').attr({href:""+api_site_url+'/'+file_path+"", data_id: ""+value.id+"", data_report_type:""+value.report_type+"", target: "_blank"});
								$('#pdf_report_export_download').attr({href:""+download_link+"", data_id: ""+value.id+"", data_report_type:""+value.report_type+"", target: "_blank"});
                            }
                        }
                        else{
                            $('.js_search_export_pdf').removeClass('hide');
                            $('.js_search_export_csv').removeClass('hide');
                        }
                    });
                }else{
                    $('.js_search_export_pdf').removeClass('hide');
                    $('.js_search_export_csv').removeClass('hide');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                console.log('Error occured');
                console.log(JSON.stringify(jqXHR));
                console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        });
    };
} else {

	current_page = window.location.pathname.split("/").pop();
	var baseurl = api_site_url;
	var url = baseurl+"/streamcsv/export/claims/"+current_page;
	var controllerName = '';
	var functionName = '';
        var file_name = '';

	if(current_page == 'electronic'){
        var url_pdf = baseurl+"/reports/export_pdf/electronic_claims";
		file_name = 'Electronic_Claims_list';
		controllerName = "ClaimControllerV1";
		functionName = "ClaimsDataSearchExport";
	}else if(current_page == 'paper'){
        var url_pdf = baseurl+"/reports/export_pdf/paper_claims";
		file_name = 'Paper_Claims_list';
		controllerName = "ClaimControllerV1";
		functionName = "ClaimsDataSearchExport";
	}else if(current_page == 'error'){
        var url_pdf = baseurl+"/reports/export_pdf/claim_edits";
		file_name = 'Error_Claims_list';
		controllerName = "ClaimControllerV1";
		functionName = "ClaimsDataSearchExport";
	}else if(current_page == 'submitted'){
        var url_pdf = baseurl+"/reports/export_pdf/submitted_claims";
		file_name = 'Submitted_Claims_list';
		controllerName = "ClaimControllerV1";
		functionName = "ClaimsDataSearchExport";
	}else if(current_page == 'rejected'){
        var url_pdf = baseurl+"/reports/export_pdf/rejection_claims";
		file_name = 'Rejected_Claims_list';
		controllerName = "ClaimControllerV1";
		functionName = "ClaimsDataSearchExport";
	}else if(current_page == "myproblemlist") {
		var url = baseurl+"/streamcsv/export/my-problem-list";
		file_name = 'Assigned_Workbench';
		controllerName = "ProblemListController";
		functionName = "getWorkbenchListExport";
	} else if(current_page == 'problemlist'){
            var current_url = $(location).attr('href').split('/');
            if($.inArray("patients", current_url) != -1) {
                var url = baseurl+"/streamcsv/export/patient-workbench-list";
                file_name = 'Patient_Workbench_List';
                controllerName = "ProblemListController";
                functionName = "getProblemListExport";
            }else{
                file_name = 'Total_Workbench';
		var url = baseurl+"/streamcsv/export/problem-list";
		controllerName = "ProblemListController";
		functionName = "getWorkbenchListExport";
            }
	}else if(current_page == 'armanagementlist'){
		var url = baseurl+"/streamcsv/export/armanagement/arManagementList";
		file_name = 'AR_Management_List';
		controllerName = "ArmanagementController";
		functionName = "arManagementListExport";
	}else if(current_page == 'summary'){
		var url = baseurl+"/streamcsv/export/armanagement/denials";
		file_name = 'AR_Denial_List';
		controllerName = "ArmanagementController";
		functionName = "arDenialListExport";
	} else if(current_page == 'denials'){
		var url = baseurl+"/streamcsv/export/armanagement/denials";
		file_name = 'AR_Denial_List';
		controllerName = "ArmanagementController";
		functionName = "arDenialListExport";
	} else if(current_page == 'payments'){
        var current_url = $(location).attr('href').split('/');
        if($.inArray("patients", current_url) != -1) {
            var url = baseurl+"/streamcsv/export/patient-payments-list";
            var url_pdf = baseurl+"/reports/export_pdf/payments";
            file_name = 'patient-payments';
            controllerName = "PatientPaymentController";
            functionName = "getPaymentExport";
        } else {
            var url = baseurl+"/streamcsv/export/payments";
            var url_pdf = baseurl+"/reports/export_pdf/payments";
            file_name = 'Payments';
            controllerName = "PaymentController";
            functionName = "paymentsExport";
        }
	}else if(current_page == 'get-e-remittance'){
        var url = baseurl+"/streamcsv/export/paymentsE-remittance";
        var url_pdf = baseurl+"/reports/export_pdf/paymentsE-remittance";
        file_name = 'Payments_E_Remittance';
        controllerName = "PaymentController";
        functionName = "export_e_remittance";
    } else if(current_page == 'archiveinsurance'){
        var current_url = $(location).attr('href').split('/');
        if($.inArray("patients", current_url) != -1) {
            var url = baseurl+"/streamcsv/export/patient_insurance_archive";
            file_name = 'Patient_ArchiveInsurance_List';
            controllerName = "PatientsController";
            functionName = "archiveInsuranceExport";
        }
    }else if(current_page == 'appointments'){
        var current_url = $(location).attr('href').split('/');
        if($.inArray("patients", current_url) != -1) {
            var url = baseurl+"/streamcsv/export/patient-appointment-list";
            file_name = 'Patient_Appointments_List';
            controllerName = "PatientAppointmentController";
            functionName = "getAppointmentExport";
        }
    }else if(current_page == 'billing'){
        var current_url = $(location).attr('href').split('/');
        if($.inArray("patients", current_url) != -1) {
            var url = baseurl+"/streamcsv/export/patient-claims-list";
            file_name = 'Patient_Claims_List';
            controllerName = "PatientBillingController";
            functionName = "getBillingExport";
        }
    }else if(current_page == 'charges'){
        var url = baseurl+"/streamcsv/export/charges";
		var url_pdf = baseurl+"/reports/export_pdf/charges";
		file_name = 'Charges';
		controllerName = "ChargeController";
		functionName = "chargesExport";
	}else if(current_page == 'patients'){
		var url = baseurl+"/streamcsv/export/patients-export";
        var url_pdf = baseurl+"/reports/export_pdf/patients_list";
		file_name = 'Patients_list';
		controllerName = "PatientsController";
		functionName = "getPatientExport";
	} else if(current_page == 'appointmentlist'){
            var url = baseurl+"/streamcsv/export/schedulerAppointmentlist";
            var file_name = 'schedulerAppointmentlist';
            controllerName = "AppointmentListController";
            functionName = "schedulerTableDataExport";
    } else if(current_page == 'insurance'){
            var url = baseurl+"/streamcsv/export/insurance";
            var file_name = 'Insurance_List';
            controllerName = "InsuranceController";
            functionName = "getInsuranceExport";
    }else if(current_page == 'icd'){
            var url = baseurl+"/streamcsv/export/icd";
            var file_name = 'ICD';
            controllerName = "IcdController";
            functionName = "getIcdExport";
    }else if(current_page == 'listfavourites'){
            var url = baseurl+"/streamcsv/export/CPTfavourites";
            var file_name = 'CPT_HCPCS_Favourites';
            controllerName = "CptController";
            functionName = "getCptFavoritesExport";
    }else if(current_page == 'employer'){
            var url = baseurl+"/streamcsv/export/employers";
            var file_name = 'Employers_List';
            controllerName = "EmployerController";
            functionName = "getEmployerExport";
    }else if(current_page == 'feeschedule'){
            var url = baseurl+"/streamcsv/export/feeschedule";
            var file_name = 'Fee_Schedule';
            controllerName = "FeescheduleController";
            functionName = "getReport";
    }else if(current_page == 'patientpayment'){
            var url = baseurl+"/streamcsv/export/patient_payment_wallet";
            var file_name = 'Patient_Payment_Wallet';
            controllerName = "PatientWalletHistoryController";
            functionName = "paymentWalletExport";
    }else if(current_page == 'bulkstatement'){
            var url = baseurl+"/streamcsv/export/patient_bulkstatement";
            var file_name = 'Patients_BulkStatement_list';
            controllerName = "PatientbulkstatementController";
            functionName = "getPatientBulkStatementExport";
    }
	if(file_name!=""){
    	$('.js_search_export_csv').click(function(){
    		$('.xlsx_report_export_spinner').removeClass('hide');
            $('.js_search_export_csv').addClass('hide');
            $('#append_report_list_xlsx').removeClass('hide');
            var append = $('#append_report_list_xlsx').html('<div class="col-md-9" id="alert-notes-msg">'+file_name+'.xlsx</div><div class="col-md-3 no-padding"><span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span></div>');
    		form = $('#export_csv').serializeArray();
            var dataArr = {};
            var data_arr = {};

            $('select.auto-generate').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
            });                                                                                // Getting all data in select fields

			$('input.auto-generate:visible').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
            });
			if(file_name == 'Payments_E_Remittance'){
				$('input.js-era-status').each(function(){
					data_arr[$(this).attr('name')] = JSON.stringify($(this).prop("checked"));
				});
			}
			data_arr["controller_name"] = JSON.stringify(controllerName);
			data_arr["function_name"] = JSON.stringify(functionName);
			data_arr["report_name"] = JSON.stringify(file_name);                 // Getting all data in input fields
			data_arr["export"] = JSON.stringify('xlsx');
			var current_url = $(location).attr('href').split('/');
			if($.inArray("patients", current_url) != -1) {
				var current =$(location).attr('href').split('/').length; var t = current-1;
				var patient_id = $(location).attr('href').split('/')[t-1];
				data_arr["patient_id"] = JSON.stringify(patient_id);
			}
			dataArr = {data:data_arr};

			$.ajax({
				url: url,
				type: 'POST',
				data : {'dataArr':dataArr,"_token":$('input[name=_token]').val()} ,
				success: function(data) {
					listingDownload();
				}
			});
    	});

        $('.js_search_export_pdf').click(function(){
            $('.pdf_report_export_spinner').removeClass('hide');
            $('.js_search_export_pdf').addClass('hide');
            $('#append_report_list_pdf').removeClass('hide');
            var append = $('#append_report_list_pdf').html('<div class="col-md-9" id="alert-notes-msg">'+file_name+'.pdf</div><div class="col-md-3 no-padding"><span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span></div>');
            form = $('#export_pdf').serializeArray();
            var dataArr = {};
            var data_arr = {};

            $('select.auto-generate').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).select2('val'));
            });                                                                                // Getting all data in select fields
            $('input.auto-generate:visible').each(function(){
                data_arr[$(this).attr('name')] = JSON.stringify($(this).val());
            });
            data_arr["controller_name"] = controllerName;
            data_arr["function_name"] = functionName;
            data_arr["report_name"] = file_name;                 // Getting all data in input fields
            data_arr["export"] = 'pdf';
            var current_url = $(location).attr('href').split('/');
            if($.inArray("patients", current_url) != -1) {
                var current =$(location).attr('href').split('/').length; var t = current-1;
                var patient_id = $(location).attr('href').split('/')[t-1];
                data_arr["patient_id"] = JSON.stringify(patient_id);
            }
            dataArr = {data:data_arr};

            $.ajax({
                url: url_pdf,
                type: 'POST',
                data : {'dataArr':dataArr,"_token":$('input[name=_token]').val()} ,
                success: function(data) {
                    listingDownload();
                }
            });

        });

		function listingDownload(){
			var url = api_site_url+"/reports/export_pdf/generate_report/"+file_name.replace(/_/g, '_').toLowerCase();
			$.ajax({
				url: url,
				type: 'GET',
				success: function(data){
					if (data.length !== 0) {
						$.each(data, function(key, value){
							var file_path = value.report_file_name;
							if (value.status == "Inprocess") {
								if (value.report_type == "xlsx") {
									if ($(".js_search_export_csv").is(":visible")) {
										$('.xlsx_report_export_spinner').addClass('hide');
									} else{
										$('.xlsx_report_export_spinner').removeClass('hide');
									}
								}else if(value.report_type == "pdf"){
									if ($(".js_search_export_pdf").is(":visible")) {
										$('.pdf_report_export_spinner').addClass('hide');
									}else{
										$('.pdf_report_export_spinner').removeClass('hide');
									}
								}
								var status = '<span class="progress col-md-12 no-padding" style="float: right;padding: 2px 30px; font-size:12px; border-radius : 20px;"><p class="progress-bar progress-bar-striped progress-bar-animated active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></p></span>'
							}else if(value.status == "Pending"){
								if (value.report_type == "xlsx") {
									$('.xlsx_report_export_spinner').addClass('hide');
									if ($(".js_search_export_csv").is(":visible")) {
										$('.xlsx_report_export_download').addClass('hide');
									}else{
										$('.xlsx_report_export_download').removeClass('hide');
									}
									js_sidebar_notification('success','Excel Ready to Download');
									$('#xlsx_report_export_download').attr({href:""+api_site_url+'/'+file_path+"", data_id: ""+value.id+"", data_report_type:""+value.report_type+""});
								}else if(value.report_type == "pdf"){
									$('.pdf_report_export_spinner').addClass('hide');
									if ($(".js_search_export_pdf").is(":visible")) {
										$('.pdf_report_export_download').addClass('hide');
									}else{
										$('.pdf_report_export_download').removeClass('hide');
									}
									js_sidebar_notification('success','PDF Ready to Download');
									$('#pdf_report_export_download').attr({href:""+api_site_url+'/'+file_path+"", data_id: ""+value.id+"", data_report_type:""+value.report_type+"", target: "_blank"});
								}
							} else {
								$('.js_search_export_pdf').removeClass('hide');
								$('.js_search_export_csv').removeClass('hide');
							}
						});
					}else{
						$('.js_search_export_pdf').removeClass('hide');
						$('.js_search_export_csv').removeClass('hide');
					}
				}
			});
		};
	}
}

$('#xlsx_report_export_download').on('click',function(){
    $('.js_search_export_csv').removeClass('hide');
    $('.xlsx_report_export_download').addClass('hide');
    var id = $(this).attr("data_id");
    var report_type = $(this).attr("data_report_type");
    var url = api_site_url+"/reports/export_pdf/generate_report/"+report_name+"/"+id;
    $.ajax({
        url: url,
        type: 'GET',
        success:function(data){
			//
        }
    });
});

$('#pdf_report_export_download').on('click',function(){
    $('.js_search_export_pdf').removeClass('hide');
    $('.pdf_report_export_download').addClass('hide');
    var id = $(this).attr("data_id");
    var report_type = $(this).attr("data_report_type");
    var url = api_site_url+"/reports/export_pdf/generate_report/"+report_name+"/"+id;
    $.ajax({
        url: url,
        type: 'GET',
        success:function(data){
			//
        }
    });
});