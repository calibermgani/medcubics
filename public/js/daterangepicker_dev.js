/*/////////////////////////////////////////////////////////////////////////////
 Author:    Selvakumar
 Date:      18 July 2018
 
Daterangepicker handling for Search relevant page
 ----------- INDEX -------------
 1. Common daterangepicker for medcubics search
 2. Getting data form Search fields
 3. Remove the more filter particular fields
/*/////////////////////////////////////////////////////////////////////////////


$(document).ready(function(){
/* Transaction Date Label */
	datePickerCall();
    disableAutoFill('.search_fields_container');
});
if(!(start_date && end_date)){
	var d = new Date(),
	date = d.getDate(),
	m = d.getMonth()+1,
    y = d.getFullYear();
    start_date = m+'-'+1+'-'+y;
    end_date = m+'-'+date+'-'+y;
    today = m+'-'+date+'-'+y;
}
function datePickerCall(){ 
	/* ----------------------------------------------- EDI Reports Date -----------------------------------------------*/
	$('input[name="DateCreated"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="DateCreated"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="DateCreated"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});
	/* ----------------------------------------------- ERA Check Date -----------------------------------------------*/
	$('input[name="CheckDate"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="CheckDate"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="CheckDate"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});
	/* ----------------------------------------------- ERA Received date -----------------------------------------------*/
	$('input[name="ReceivedDate"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="ReceivedDate"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="ReceivedDate"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Security Code -----------------------------------------------*/
	$('input[name="Date and Time of Attempt"]').daterangepicker({
		//autoUpdateInput: false,
		startDate: start_date,
		endDate: end_date,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="Date and Time of Attempt"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="Date and Time of Attempt"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Transaction Date -----------------------------------------------*/
	if(!$(location).attr('href').includes('reports')) {
		$('input[name="transaction_date"]').daterangepicker({
			autoUpdateInput: false,
			alwaysShowCalendars: true,
			showDropdowns: true,
			linkedCalendars:false,
			locale: {
			  cancelLabel: 'Clear'
			},
			ranges: {
			   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
			   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
			   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
			}
		});
	}else{
		$('input[name="transaction_date"]').daterangepicker({
			//autoUpdateInput: false,
			startDate: start_date,
			endDate: end_date,
			alwaysShowCalendars: true,
			showDropdowns: true,
			linkedCalendars:false,
			locale: {
			  cancelLabel: 'Clear'
			},
			ranges: {
			   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
			   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
			   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
			}
		});
		
	}

	$('input[name="transaction_date"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="transaction_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Hold release Date -----------------------------------------------*/
	$('input[name="hold_releasedate"]').daterangepicker({
		autoUpdateInput: false,
		startDate: start_date,
		endDate: end_date,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="hold_releasedate"]').on('apply.daterangepicker', function(ev, picker) {		
		$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));		
	});

	$('input[name="hold_releasedate"]').on('cancel.daterangepicker', function(ev, picker) {
		$(this).val('');
	});

	/* ----------------------------------------------- DOS Date Label -----------------------------------------------*/
	$('input[name="dos"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="dos"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="dos"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- DOS Date Label -----------------------------------------------*/
	$('input[name="date_of_service"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="date_of_service"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="date_of_service"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Field Date Label -----------------------------------------------*/
	$('input[name="filed_date"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="filed_date"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="filed_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Created Date Label -----------------------------------------------*/
	if(!$(location).attr('href').includes('reports')) {
		$('input[name="created_at"]').daterangepicker({
			autoUpdateInput: false,
			alwaysShowCalendars: true,
			showDropdowns: true,
			linkedCalendars:false,
			locale: {
			  cancelLabel: 'Clear'
			},
			ranges: {
			   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
			   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
			   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
			}
		});
	}else{ 
		$('input[name="created_at"]').daterangepicker({
			alwaysShowCalendars: true,
			showDropdowns: true,
			linkedCalendars:false,
			startDate: start_date,
			endDate: end_date,
			locale: {
			  cancelLabel: 'Clear'
			},
			ranges: {
			   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
			   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
			   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
			}
		});
	}

	$('input[name="created_at"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="created_at"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Scheduled Date Label -----------------------------------------------*/
	$('input[name="scheduled_at"]').daterangepicker({
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		startDate: end_date,
	    endDate: end_date,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="scheduled_at"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="scheduled_at"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Rejected Date Label -----------------------------------------------*/

	$('input[name="rejected_date"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="rejected_date"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="rejected_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Submitted Date Label -----------------------------------------------*/
	$('input[name="submitted_date"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="submitted_date"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="submitted_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Transaction Date Label -----------------------------------------------*/
	$('input[name="created_on"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,
		maxDate: new Date(),
		locale: {
		  cancelLabel: 'Clear'
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="created_on"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="created_on"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- DOB Date Label -----------------------------------------------*/
	$('input[name="dob_search"]').daterangepicker({
		maxDate: new Date(),
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,	
		locale: {
		  cancelLabel: 'Clear',	
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="dob_search"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="dob_search"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Check Date Label -----------------------------------------------*/
	$('input[name="check_date"]').daterangepicker({
		maxDate: new Date(),
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,	
		locale: {
		  cancelLabel: 'Clear',	
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="check_date"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="check_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Created Date Label -----------------------------------------------*/
	$('input[name="create_date"]').daterangepicker({
		maxDate: new Date(),
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,	
		locale: {
		  cancelLabel: 'Clear',	
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="create_date"]').on('apply.daterangepicker', function(ev, picker) {
		
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="create_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- FollowUp Date Label -----------------------------------------------*/
	$('input[name="followup_date"]').daterangepicker({
		autoUpdateInput: false,
		alwaysShowCalendars: true,
		showDropdowns: true,
		linkedCalendars:false,	
		locale: {
		  cancelLabel: 'Clear',	
		},
		ranges: {
		   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
		   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
		   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
		   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
		}
	});

	$('input[name="followup_date"]').on('apply.daterangepicker', function(ev, picker) {	
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="followup_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val('');
	});

	/* ----------------------------------------------- Transaction Date Label -----------------------------------------------*/
	$('input[name="select_transaction_date"]').daterangepicker({
			startDate: start_date,
			endDate: end_date,
			alwaysShowCalendars: true,
			showDropdowns: true,
			linkedCalendars:false,
			locale: {
			  cancelLabel: 'Cancel'
			},
			ranges: {
			   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
			   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
			   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
			}
		});

	$('input[name="select_transaction_date"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="select_transaction_date"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val();
	});
	
	/* ----------------------------------------------- DOS Date Label -----------------------------------------------*/
	$('input[name="select_date_of_service"]').daterangepicker({
			startDate: start_date,
			endDate: end_date,
			alwaysShowCalendars: true,
			showDropdowns: true,
			linkedCalendars:false,
			locale: {
			  cancelLabel: 'Cancel'
			},
			ranges: {
			   'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
			   'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
			   'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
			   'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
			}
		});

	$('input[name="select_date_of_service"]').on('apply.daterangepicker', function(ev, picker) {
	  $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
	});

	$('input[name="select_date_of_service"]').on('cancel.daterangepicker', function(ev, picker) {
	  $(this).val();
	});

	$('input[name="performance_date"]').daterangepicker({
        //autoUpdateInput: false,
        startDate: start_date,
        endDate: end_date,
        alwaysShowCalendars: true,
        showDropdowns: true,
        linkedCalendars:false,
        locale: {
          cancelLabel: 'Cancel'
        },
        ranges: {
           'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
           'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
           'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
           'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
        }
    });

    $('input[name="performance_date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('input[name="provider_by_location_date"]').daterangepicker({
        //autoUpdateInput: false,
        startDate: start_date,
        endDate: end_date,
        alwaysShowCalendars: true,
        showDropdowns: true,
        linkedCalendars:false,
        locale: {
          cancelLabel: 'Cancel'
        },
        ranges: {
           'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
           'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
           'This Month': [moment("'"+today+"'", "MM-DD-YYYY").startOf('month'), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last Month': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').startOf('month'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'month').endOf('month')],
           'This Year': [moment("'"+today+"'", "MM-DD-YYYY").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Last Year': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").startOf("year"), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, "y").endOf("year")]
        }
    });

    $('input[name="provider_by_location_date"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('input[name="charge_delete_transaction_date"]').daterangepicker({
    	/*startDate: start_date,
        endDate: end_date,*/
        startDate: moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'),
        endDate: today,
        minDate: moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'),
        maxDate: today,
        alwaysShowCalendars: true,
        showDropdowns: true,
        linkedCalendars:false,
        //showCustomRangeLabel:false,
        locale: {
          cancelLabel: 'Cancel'
        },
        ranges: {
           /*'Today': [moment("'"+today+"'", "MM-DD-YYYY"), moment("'"+today+"'", "MM-DD-YYYY")],
           'Yesterday': [moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days'), moment("'"+today+"'", "MM-DD-YYYY").subtract(1, 'days')],
           'Last 7 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(6, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],*/
           'Last 30 Days': [moment("'"+today+"'", "MM-DD-YYYY").subtract(29, 'days'), moment("'"+today+"'", "MM-DD-YYYY")],
        }
    });
}
/* --------------------------------------Date Picker call End--------------------------------------------------------------- */

/* Remember Search Code Start */


/*$(document).on('change','.search_fields_container input,.search_fields_container select',function(){
	page_id = $("#search_remember").attr('data-page-id');
	userpage_id = $("#search_remember").attr('data-userpage-id');
	remember_data = data_collection();
	more_data = $('select.more_generate').val();
	$.ajax({
                type: 'post',
                url: api_site_url+'/claims/store/search-data',
                data: {'_token':$('input[name=_token]').val(),'page_id':page_id,'remember_data':remember_data,'more_data':more_data,'userpage_id':userpage_id},
                success: function (data) {
					//js_sidebar_notification('success','Successfully search data added');
				}
		});
})*/


/* $(document).on('click','.js_filter_search_submit',function(){
	page_id = $("#search_remember").attr('data-page-id');
	userpage_id = $("#search_remember").attr('data-userpage-id');
	remember_data = data_collection();
	more_data = $('select.more_generate').val();
	$.ajax({
                type: 'post',
                url: api_site_url+'/claims/store/search-data',
                data: {'_token':$('input[name=_token]').val(),'page_id':page_id,'remember_data':remember_data,'more_data':more_data,'userpage_id':userpage_id},
                success: function (data) {
					//js_sidebar_notification('success','Successfully search data added');
				}
		});
}) */


$(document).on('change','#search_remember',function(){
	page_id = $(this).attr('data-page-id');
	userpage_id = $(this).attr('data-userpage-id');
	remember_data = data_collection();
	more_data = $('select.more_generate').val();
	if($(this).prop("checked") == true){
		$.ajax({
                type: 'post',
                url: api_site_url+'/claims/store/search-data',
                data: {'_token':$('input[name=_token]').val(),'page_id':page_id,'remember_data':remember_data,'more_data':more_data,'userpage_id':userpage_id},
                success: function (data) {
					js_sidebar_notification('success','Successfully search data added');
				}
		});
	}else{
		/* $.ajax({
                type: 'post',
                url: api_site_url+'/claims/store/search-data-remove',
                data: {'_token':$('input[name=_token]').val(),'page_id':page_id,'remember_data':remember_data,'more_data':more_data},
                success: function (data) {
					js_sidebar_notification('success','Successfully search data removed');
				}
		}); */
	}
});


/* Remember Search Code End */
function getMoreFieldData(){
	var moreArr = $('select.more_generate').select2('val');
	 $.each(moreArr, function(index, item) {  
		item = item.replace("[]","");
		if($('div#'+item).is(":visible") == false){
			$('div.search_fields_container').last().append("<div class='dynamic_append'>"+$('div.'+item+'_more').html()+"</div>");
			$('div#s2id_'+item).addClass('hide');
			$('select#'+item).select2();
		}	
	});																			
	// Dynamic appending process end
	
	if(moreArr.length == 1){
		var all_selected_class = "#"+moreArr[0].replace("[]","");
		$("div.dynamic_append").children().not(all_selected_class).remove();
	}else if(moreArr.length > 1){
		var all_selected_class = "#"+moreArr.join(",#");
		$("div.dynamic_append").children().not(all_selected_class).remove();
	}else if(moreArr.length == 0){
		$('div.dynamic_append').remove();
	}
	
}

/* Remove the particular fields for more filter */

$(document).on('click','.js-remove-search-field',function(){
	var remove_id = $(this).attr('data-remove-id');
	$('.'+remove_id).siblings('a').click();
	$(this).parent().parent().hide();
})


$(document).on('click','.applyBtn',function(){
	var mod = $(location).attr('href').split("/").splice(3, 1).join("/");
	//if(mod != 'reports'){// for date range picker Don't work in reports module at click apply button
	if(!$(location).attr('href').includes('reports')) {
		getMoreFieldData();
		getData();
	}
});

// Start Billed and Unbilled toggle

$(".more_generate").on('change',function(){
		var more = $(".more_generate ul.select2-choices li.select2-search-choice div").toArray();
	if ($.inArray('unbilledamt', $(this).val()) !== -1 && $.inArray('billedamt', $(this).val()) !== -1) {
		$.each(more,function(i,v){
			if($(v).text()=="Unbilled"){
				$(".unbilled").siblings('a').click();
				return false;
			}
			if($(v).text()=="Billed"){
				$(".billed").siblings('a').click();
				return false;
			}
		});
}
});

$(document).ready(function(){	
	var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == 'generate') {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
var generateStatus = getUrlParameter();
if(generateStatus == 'yes')
	$('input.js_filter_search_submit').click();
});
// End Billed and Unbilled toggle

$(document).on('click','.js-search-filter',function(){
	getData();
	rememberFilter();
});

$(document).ready(function(){
	var mod = $(location).attr('href').split("/").splice(3, 1).join("/");
	if(!$(location).attr('href').includes('reports') && !$(location).attr('href').includes('charge/delete')){
		getMoreFieldData();
		if (typeof getData !== "undefined") {
			getData();
		}
	}
});

/* Onchange code for field Start */
$('.auto-generate').keypress(function(event){ 
var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
		getData();
		rememberFilter();
	}
});
/* Onchange code for field End */ 

/* Onchange code for more field Start */
$(document).on('change','select.more_generate',function(){ 
	getMoreFieldData();
});
/* Onchange code for more field End */ 

function rememberFilter(){
	page_id = $("#search_remember").attr('data-page-id');
	userpage_id = $("#search_remember").attr('data-userpage-id');
	remember_data = data_collection();
	more_data = $('select.more_generate:visible').val();
	$.ajax({
			type: 'post',
			url: api_site_url+'/claims/store/search-data',
			data: {'_token':$('input[name=_token]').val(),'page_id':page_id,'remember_data':remember_data,'more_data':more_data,'userpage_id':userpage_id},
			success: function (data) {
			}
	});
}
