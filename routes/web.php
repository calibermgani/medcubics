<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// This will be the route that checks expiration!
Route::post('forgot_password/create','UsersController@send_mail');
Route::get('static/help/{type}', 'Medcubics\StaticPageController@getHelpContent');

Route::get('hl7', 'HL7\HL7Controller@index');

// Get error log view purpose - No login required.
Route::get('api/getErrorLog', 'Medcubics\DashboardController@getRecentErrorLog'); 

//Route::any('era', 'Payments\EraController@index');
Route::get('/createmigrationfile', 'Medcubics\Api\DBConnectionController@createmigrationfile');
Route::get('/', 'HomeController@index');
Route::post('getActiveUserList','HomeController@getActiveUserList');

Route::get('/login', 'Auth\LoginController@getLogin');
Route::post('login', 'Auth\LoginController@postLogin');
Route::get('import-notes', 'Api\FeescheduleApiController@notes');

// For got Password and Rest Password 
Route::get('password/email', 'Auth\LoginController@getEmail');
Route::post('api/password/email', 'Auth\LoginController@postEmail');
Route::get('resetpassword/{email}/{token}', 'Auth\LoginController@getReset');
Route::post('resetpassword', 'Auth\LoginController@postReset');

Route::get('auth/login', 'Auth\LoginController@getLogin')->name('login');
Route::post('auth/login', 'Auth\LoginController@postLogin');
Route::any('auth/logout', ['as' => 'logout', 'uses'=>'Auth\LoginController@logout']);

Route::get('auth/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('auth/practiceCreation', 'Auth\RegisterController@practiceCreation');
Route::get('verification/{email}', 'Auth\RegisterController@emailVerification');

Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


//Route::get('dbcheck', 'DbcheckController@dbcreate');
Route::get('admin/dashboard', 'Medcubics\DashboardController@index');
//Route::get('permissionerror', 'HomeController@Permissionerror');
Route::any('sessioncheck', 'SessionController@sessionCheck'); //session Check no need to authenticate
Route::post('onlineStatus','SessionController@onlineStatus'); //chceking online status 
//cron process starts
Route::get('docronseeding', 'CronController@doseedingcron');
Route::get('getxmlcontent', 'CronController@getXmlcontent');
Route::get('provideralert', 'CronController@getProvideralert');
Route::get('patientbudget', 'CronController@getPatientbudget');
Route::get('crondocuments', 'CronController@documentcron'); //cron process delete temporary saved documents
Route::get('cronuserlogin', 'CronController@userLogginCron'); //cron process delete userLoggedin
Route::get('appointmentstatus', 'CronController@AppointmentStatusUpdate'); 
Route::get('stmtholdrelease', 'CronController@stmtHoldReleaseUpdate'); //cron process update statement status  

Route::get('cmsform/{claim_id}', "Patients\Api\BillingApiController@generatecms1500");
Route::get('deletecmsform/{claim_id}', "Patients\Api\BillingApiController@deleteExistingdocument");
Route::get('cronticketstatus', "CronController@ticketNotificationSend");



Route::get('storeReportFile', "CronController@storeReportFile");
Route::get('deleteClearingHouseFile', "CronController@deleteClearingHouseFile");
Route::post('generateReportExport', "Reports\Financials\FinancialController@generateReportExport");
Route::get('showGenerateReport', "Reports\Financials\FinancialController@showGenerateReport");
Route::get('exportDownload/{id}', "Reports\Financials\FinancialController@exportDownload");
Route::post('exportDelete/{id}', "Reports\Financials\FinancialController@exportDelete");

Route::get('filechk', "AttachmentController@index");

Route::group(['prefix' => 'download'], function() {
    // Category = reports, id = #, file_name = target name
    Route::get('/{category}/{id}/{file_name}', 'AttachmentController@downloadResourceFile');
});
// temp adding for re uploading routes
Route::get('gettaxanomies', 'PracticesController@taxanomies');

Route::post('userEmailValidate', 'Medcubics\AdminuserController@userEmailValidate');
Route::post('practiceNameValidate', 'Medcubics\AdminuserController@practiceNameValidate');
//cron process ends

// To remove old reports which are old more than 30 days
Route::get('removeOldReports', 'AttachmentController@removeOldAttachements');

//Static content gets in login page
Route::get('static/help/{type}', 'StaticPageController@getStaticpageContent');

Route::group(array('middleware' => [ 'csrf', 'auth', 'session']), function() {
    Route::any('sessioninsert', 'SessionController@sessionInsert'); //session Check no need to authenticate 
    
    Route::any('analytics/financials', 'FinancialsDashboardController@dashboard');
    
   // Route::get('dashboard1', 'DashboardController@dashboard1');  
   // Route::get('dashboard1', 'Dashboard\Api\DashboardChargeApiController@getChargeAnalyticsApi');  
    Route::group(array('prefix' => 'dashboard'), function() {
        Route::get('/', 'DashboardController@index');
        Route::post('refresh', 'ProviderDashboardController@refreshStats');
        // Refresh dashbaord statistics
        Route::post('refreshstats', 'DashboardController@refreshStats');

        Route::get('charges', 'Dashboard\DashboardChargeController@getChargeAnalytics');  
        Route::get('charges/', 'Dashboard\DashboardChargeController@getChargeAnalytics');
        Route::get('charges/performance/{type}', 'Dashboard\DashboardChargeController@getPerformanceManagement');
        Route::get('getcharges/{facility_id?}', 'Dashboard\DashboardChargeController@getChargeAnalyticsAjax');
    });
    Route::get('analytics/practice', 'DashboardController@index');
    Route::any('analytics/providers', 'ProviderDashboardController@index');    
    Route::get('dashboard_payments', 'Dashboard\Api\DashboardPaymentApiController@getPaymentAnalyticsApi');
    Route::get('scheduling', 'DashboardController@scheduling_dashboard');
    Route::get('paymentdashboard', 'DashboardController@payment_dashboard');
    Route::get('ardashboard', 'DashboardController@ar_dashboard');
    Route::get('admin/dashboard', 'Medcubics\DashboardController@index');
    Route::get('admin/practicestatistics', 'Medcubics\DashboardController@practiceStatsDashboard');
    Route::get('admin/get_recent_errors', 'Medcubics\DashboardController@get_recent_errors');    
    Route::post('admin/dashboard/updateuser', 'Medcubics\DashboardController@updateUserLogout');
    
	/* Adding rule engine URL */
	
	Route::post('codes/setRuleEngine','RuleEngine\RuleEngineContoller@updateCodeRuleEngine');
    Route::get('getRuleEngine','RuleEngine\RuleEngineContoller@getRuleEngine');
	
	/* Adding rule engine URL */
	
	
    Route::get('practicelist', 'PracticesController@listpractice');
    // submited date tupdate timezone for previous records
    ### after updated this sql need hide this submited date timezone
    Route::get('practice/{id}/submited-date-timezone-update', 'PracticesController@updatetimesubmiteddate');
    Route::get('practice/{id}/filed-date-timezone-update', 'PracticesController@updatetimefileddate');
    ### Notification message and Notes
    Route::get('practice/Notification/msg-notes', 'PracticesController@messageNotes');
    Route::get('practice/Notification/wishlist', 'PracticesController@getWishlist');

    Route::post('advancedtablesearch', 'Api\CommonApiController@advancedTableSearch');
    Route::resource('patients/ledger1', 'Patients\LedgerController@ledger');
    Route::get('changedate/{type}', "ChangedateController@changecreatedDate");
    Route::get('postchangedate/{type}/{id}/{dataval}', 'ChangedateController@postCreatedDateApi');
    Route::get('charges1', 'Charges\ChargeController@charge1');
    Route::get('updatepatientinsurance', 'Charges\Api\ChargeV1ApiController@GetandChangePatientInsuranceID'); 
    //temporary URL to update patient insuranceID
    
    //Route::resource('claims','Claims\ClaimController');
    //Route::resource('claims1','Claims\ClaimController@claim1');
    //Route::get('reports','Reports\ReportController@index');
    ### REPORTS MODULE START ###
    Route::get('/reports', 'Reports\ReportController@schedulingreport');
    Route::any('forgot_password/create','UsersController@send_mail');
    Route::post('/check_report_progress', 'Reports\ReportController@getProgress');

    Route::group(array('prefix' => 'streamcsv'), function() {
        
        // Listing Page Excel Stream Download Starts
        Route::post('/export/charges', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/payments', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/paymentsE-remittance', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/icd', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/insurance', 'Api\CommonExportExcelController@execute');
        Route::post('/export/CPTfavourites', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/my-problem-list', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/problem-list', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/schedulerAppointmentlist', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/patients-export', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/claims/electronic', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/claims/paper', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/claims/error', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/claims/submitted', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/claims/rejected', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/patient-appointment-list', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/patient-claims-list', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/patient-payments-list', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/patient-workbench-list', 'Api\CommonExportExcelController@execute'); 
        Route::post('/export/patient_payment_wallet', 'Api\CommonExportExcelController@execute');
        Route::post('/export/patient_insurance_archive', 'Api\CommonExportExcelController@execute');
        Route::post('/export/practiceManagedCare', 'Api\CommonExportExcelController@execute');
        Route::post('/export/employers', 'Api\CommonExportExcelController@execute');
        Route::post('/export/feeschedule', 'Api\CommonExportExcelController@execute');
        Route::post('/export/providerScheduler', 'Api\CommonExportExcelController@execute');
        Route::post('/export/providerScheduledList', 'Api\CommonExportExcelController@execute');
        Route::post('/export/facilityScheduler', 'Api\CommonExportExcelController@execute');
        Route::post('/export/facilityScheduledList', 'Api\CommonExportExcelController@execute');
        Route::post('/export/armanagement/denials', 'Api\CommonExportExcelController@execute');
        Route::post('/export/armanagement/arManagementList', 'Api\CommonExportExcelController@execute');
        Route::post('/export/patient_bulkstatement', 'Api\CommonExportExcelController@execute');
    });
    
    Route::group(array('prefix' => 'reports'), function() {
        
        Route::group(array('prefix' => 'streamcsv'), function() {
            // Reports Module Excel Stream Download Starts
            Route::post('/export/revenue-analysis-report', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/charge-analysis-detailed-js', 'Api\SheetJSController@execute');
            Route::post('/export/Charge_Category_Report_js', 'Api\SheetJSController@execute');
            Route::post('/export/adjustment-analysis-detailed-js', 'Api\SheetJSController@execute');
            Route::post('/export/aging-analysis-detailed-js', 'Api\SheetJSController@execute');
            Route::post('/export/aging-summary-js', 'Api\SheetJSController@execute');
            Route::post('/export/unbilled-claims-analysis-js', 'Api\SheetJSController@execute');
            Route::post('/export/end-of-the-day-totals-js', 'Api\SheetJSController@execute');
            Route::post('/export/year-end-financials-js', 'Api\SheetJSController@execute');
            Route::post('/export/work-RVU-report-js', 'Api\SheetJSController@execute');
            Route::post('/export/denial-trend-analysis-js', 'Api\SheetJSController@execute');
            Route::post('/export/procedure-collection-report-js', 'Api\SheetJSController@execute');
            Route::post('/export/insurance-over-payment-js', 'Api\SheetJSController@execute');
            Route::post('/export/patient-insurance-payment-js', 'Api\SheetJSController@execute');
            Route::post('/export/demographic-sheet-js', 'Api\SheetJSController@execute');
            Route::post('/export/ar-workbench-report-js', 'Api\SheetJSController@execute');

            Route::post('/export/charge-analysis-detailed', 'Api\CommonExportExcelController@execute');
            Route::post('/export/charges-payments-summary', 'Api\CommonExportExcelController@execute');
            Route::post('/export/unbilled-claims-analysis', 'Api\CommonExportExcelController@execute');
            Route::post('/export/end-of-the-day-totals', 'Api\CommonExportExcelController@execute');
            Route::post('/export/year-end-financials', 'Api\CommonExportExcelController@execute');
            Route::post('/export/work-rvu-report', 'Api\CommonExportExcelController@execute');
            Route::post('/export/charge-category-report', 'Api\CommonExportExcelController@execute');  
            Route::post('/export/payment-analysis-detailed-report', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/refund-analysis-detailed', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/adjustment-analysis-detailed', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/procedure-collection-report-insurance-only', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/insurance-over-payment', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/patient-and-insurance-payment', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/appointment-analysis-report', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/icd-worksheet', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/statement-history-detailed', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/demographic-sheet', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/address-listing', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/wallet-history-detailed', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/statement-status-detailed', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/wallet-balance', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/patient-itemized-bill', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/aging-summary', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/aging-analysis-detailed', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/ar-work-bench-report', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/denial-trend-analysis', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/facility-summary', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/cpt-hcpcs-summary', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/provider-summary', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/month-end-performance-summary-report', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/provider-summary-by-location', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/payer-summary', 'Api\CommonExportExcelController@execute'); 
            Route::post('/export/weekly-billing-report', 'Api\CommonExportExcelController@execute'); 
            
            // Reports Module Excel Stream Download Ends

        });
        //Common Export PDF
        // Route::get('export/pdf', 'Api\CommonExportPdfController@index');
        // Route::get('export/export_pdf', 'Api\ExportPdfController@index');
        // Route::post('export/export_tcpdf', 'Api\TCPDFController@index');
            ### Empty REPORTS MODULE START ###
        Route::get('demo/list1', 'Reports\ReportController@emptylist');
        Route::get('demo/outstandingardemo', 'Reports\ReportController@outstandingardemo');
        Route::get('demo/pendingclaimdemo', 'Reports\ReportController@pendingclaimdemo');
        Route::get('demo/monthend', 'Reports\ReportController@monthendperformance');
        Route::get('demo/list', 'Reports\ReportController@demoreport');
        
        Route::get('appointments/list', 'Reports\ReportController@schedulingreport');
        Route::get('financials/list', 'Reports\ReportController@financialsreport');
        Route::get('collections/list', 'Reports\ReportController@collectionsreport');
        Route::get('edi/list', 'Reports\ReportController@edisreport');
        Route::get('patients/list', 'Reports\ReportController@patientsreport');
        Route::get('management/list', 'Reports\ReportController@managementreport');
        Route::get('practicesettings/list', 'Reports\ReportController@practicereport');
        Route::get('ar/list', 'Reports\ReportController@arreport');
        ### APPOINTMENT REPORT START ###
        Route::get('scheduling/appointments', 'Reports\ReportController@appointmentreport');
        Route::post('scheduling/search/appointment', 'Reports\ReportController@appointmentSearch');
        Route::post('scheduling/search/appointment/pagination', 'Reports\ReportController@appointmentSearch');
        //Route::get('/appointment/export/{type}', 'Reports\Api\ReportApiController@getAppointmentSearchApi');
        Route::get('/appointment/export/{type}', 'Reports\ReportController@appointmentSearchExport');
        ### APPOINTMENT REPORT END ###
        ### CLAIM REPORTS MODULE START ###
        Route::get('financials/outstandingar', 'Reports\ReportController@claimlist');
        Route::post('/search/claims', 'Reports\ReportController@claimsearch');
        Route::post('/search/claims/pagination', 'Reports\ReportController@claimsearch');
        //Route::get('claims/export/{type}', 'Reports\Api\ReportApiController@getClaimsearchApi');
        Route::get('claims/export/{type}', 'Reports\ReportController@claimSearchExport');
        ### CLAIM REPORTS MODULE END ###
        //Route::any('aginganalysisdetails/{export}', 'Reports\\Api\BillingApiController@getIndexApi');
        #### ADJUSTMENT REPORT START ###        
        Route::get('collections/adjustments', 'Reports\ReportController@adjustmentreport');
        Route::get('/reasontype/{reason_type}', 'Reports\ReportController@reasontype');
        Route::post('/search/adjustment', 'Reports\ReportController@adjustmentSearch');
        Route::post('setreportsessiondata', 'Reports\ReportController@setReportSessionData');
        Route::post('/search/adjustment/pagination', 'Reports\ReportController@adjustmentSearch');
        //Route::get('adjustment/export/{type}', 'Reports\Api\ReportApiController@getAdjustmentsearchApi');
        //Route::get('adjustment/export/{type}', 'Reports\ReportController@adjustmentSearchexport');
        Route::post('adjustment/export', 'Reports\ReportController@adjustmentSearchexport');
        Route::get('adjustment/export_pdf', 'Api\CommonExportPdfController@index');
        #### ADJUSTMENT REPORT END ###
        #### PROCEDURE REPORT START ###        
        Route::get('collections/procedurereport', 'Reports\ReportController@procedurelist');
        Route::post('collections/procedurereport/export', 'Reports\ReportController@proceduresearchExport');
        Route::post('/search/procedurereport', 'Reports\ReportController@proceduresearch');
        Route::post('/search/procedurereport/pagination', 'Reports\ReportController@proceduresearch');
          // Rote For Procedure Export
        // Route::get('procedure/export/{type}', 'Reports\ReportController@proceduresearchExport');
        #### PROCEDURE REPORT END ###  
         #### APPOINTMENT ANALYSIS REPORT START ###        
        Route::get('appointments/appointmentanalysis', 'Reports\Appointment\AppointmentController@appointmentanalysis');
        Route::post('/search/appointmentreport', 'Reports\Appointment\AppointmentController@appointmentreport');
        Route::post('/search/appointmentreport/pagination', 'Reports\Appointment\AppointmentController@appointmentreport');
        // Route::get('appointmentanalysis/export/{type}', 'Reports\Appointment\AppointmentController@appointmentanalysisExport');
        Route::post('appointments/appointmentanalysis/export', 'Reports\Appointment\AppointmentController@appointmentanalysisExport');//Export
        #### APPOINTMENT ANALYSIS REPORT END ###  
        ### CHARGE REPORTS MODULE START ###
        Route::get('financials/charges', 'Reports\ReportController@chargelist');
        Route::post('/search/charges', 'Reports\ReportController@chargesearch');
        Route::get('financials/charges-payments', 'Reports\ReportController@chargesPaymentslist');
        Route::post('/search/charges_payments', 'Reports\ReportController@chargepaymentsearch');
        // Route::get('/search/charges_payments/export/{type}', 'Reports\ReportController@chargepaymentsearch');
        Route::post('/financials/charges_payments/export', 'Reports\ReportController@chargepaymentsearch');
        Route::post('/search/charges/pagination', 'Reports\ReportController@chargesearch');
        //Route::get('charges/export/{type}', 'Reports\Api\ReportApiController@getChargesearchApi');
        // Route::get('charges/export/{type}', 'Reports\ReportController@chargesearchexport');
        Route::post('charges/export', 'Reports\ExportController@execute');
        Route::post('/charges/export_pdf', 'Reports\ExportPDFController@index');
        Route::get('/insurancetype/{type_id}/insurancelist', 'Reports\ReportController@insuranceList');
        ### CHARGE REPORTS MODULE END ###
        ### PAYMENTS REPORTS MODULE START ###
        Route::get('collections/payments', 'Reports\ReportController@paymentlist');
        Route::post('/search/payments', 'Reports\ReportController@paymentsearch');
        Route::post('/search/payments/pagination', 'Reports\ReportController@paymentsearch');
        Route::get('payments/export/download', 'Reports\Api\ReportApiController@download');
        //Route::get('payments/export/{type}', 'Reports\ReportController@paymentsearchexport');
        Route::post('payments/export', 'Reports\ReportController@paymentsearchexport');
        ### PAYMENTS REPORTS MODULE END ###
        ### Refund REPORTS MODULE START ###
        Route::get('collections/refunds', 'Reports\ReportController@refundlist');
        Route::post('/search/refunds', 'Reports\ReportController@refundsearch');
        Route::post('/search/refunds/pagination', 'Reports\ReportController@refundsearch');
        // Route::get('refunds/export/{type}', 'Reports\ReportController@refundsearchexport');
        Route::post('collections/refunds/export', 'Reports\ReportController@refundsearchexport');
        ### Refund REPORTS MODULE START ###
        ### FINANICAL REPORT START ###
        Route::get('financials/yearend', 'Reports\ReportController@yearendReport');
        Route::post('financials/yearend/export', 'Reports\ReportController@financialSearchExport');
        Route::post('/search/financial', 'Reports\ReportController@financialSearch');
        // Route::get('/financial/export/{type}', 'Reports\ReportController@financialSearchExport');
        Route::get('/getParameter/{id}', 'Reports\Financials\FinancialController@getParameter');
        ### FINANICAL REPORT END ###
        ### CUSTOM REPORT START ###
        Route::get('/customs', 'Reports\ReportController@customReport');
        Route::post('/search/customs', 'Reports\ReportController@financialSearch');
        Route::get('/customs/export/{type}', 'Reports\Api\ReportApiController@getFinancialSearchApi');
        ### CUSTOM REPORT END ###
        ### EDI REPORT START ###
        Route::get('/edi', 'Reports\ReportController@ediReport');
        Route::post('/search/edi', 'Reports\ReportController@ediSearch');
        Route::get('/edi/export/{type}', 'Reports\Api\ReportApiController@getFinancialSearchApi');

        Route::get('/edi/claimsubmissions', 'Reports\ReportController@ediReport');
        Route::get('/edi/rejection', 'Reports\ReportController@ediReport');
        Route::get('/edi/hold', 'Reports\ReportController@ediReport');
        ### EDI REPORT END ###
        ### MISCELLENOUS REPORT START ###
        Route::group(array('prefix' => 'miscellenous'), function() {
            Route::get('/', 'Reports\ReportController@miscellenousReport');
            Route::get('/export/{type}', 'Reports\Api\ReportApiController@getFinancialSearchApi');

            Route::get('/cpt', 'Reports\ReportController@miscellenousReport');
            Route::get('/icd', 'Reports\ReportController@miscellenousReport');
            Route::get('/insurance', 'Reports\ReportController@miscellenousReport');
            Route::get('/facility', 'Reports\ReportController@miscellenousReport');
            Route::get('/provider', 'Reports\ReportController@miscellenousReport');
            Route::get('/useractivity', 'Reports\ReportController@miscellenousReport');
        });
        
        Route::post('/search/miscellenous', 'Reports\ReportController@miscellenousSearch');
        
        ### MISCELLENOUS REPORT END ###
        ### PATIENT REPORT START ###
        Route::group(array('prefix' => 'patients'), function() {
            Route::get('/', 'Reports\ReportController@patientReport');
            Route::get('/export/{type}', 'Reports\Api\ReportApiController@getFinancialSearchApi');
            Route::get('/eligibility', 'Reports\ReportController@patientReport');
            Route::get('/inactive', 'Reports\ReportController@patientReport');
            Route::get('/deductible', 'Reports\ReportController@patientReport');
            Route::get('/copay', 'Reports\ReportController@patientReport');
            Route::get('/patientlist', 'Reports\ReportController@patientReport');
            Route::get('/patientbalance', 'Reports\ReportController@patientReport');
            Route::get('/statementdates', 'Reports\ReportController@patientReport');
        });
        Route::post('/search/patients', 'Reports\ReportController@patientSearch');
        

        # Patient wallet report start
        Route::group(array('prefix' => 'patientwallethistory'), function() {
            Route::get('/', 'Reports\ReportController@getPatientWalletHistory');
            Route::post('/filter', 'Reports\ReportController@getPatientWalletHistoryFilter');
            Route::post('/filter/pagination', 'Reports\ReportController@getPatientWalletHistoryFilter');
            // Route::get('/export/{type}', 'Reports\ReportController@patientWalletHistoryExport');
            Route::post('/export', 'Reports\ReportController@patientWalletHistoryExport');
            Route::get('/export_pdf', 'Api\CommonExportPdfController@index');
        });
        # Patient wallet report ends

        # Patient statement report start
        Route::group(array('prefix' => 'patientstatementhistory'), function() {
            Route::get('/', 'Reports\ReportController@getPatientStatementHistory');
            Route::post('/filter', 'Reports\ReportController@getPatientStatementHistoryFilter');
            Route::post('/filter/pagination', 'Reports\ReportController@getPatientStatementHistoryFilter');
            // Route::get('/export/{type}', 'Reports\ReportController@patientStatementHistoryExport');
            Route::post('/export', 'Reports\ReportController@patientStatementHistoryExport');
            Route::get('/export_pdf', 'Api\CommonExportPdfController@index');
        });
        # Patient statement report ends 

        # Patient statement report start
        Route::group(array('prefix' => 'patientstatementstatus'), function() {
            Route::get('/', 'Reports\ReportController@getPatientStatementStatus');
            Route::post('/filter', 'Reports\ReportController@getPatientStatementStatusFilter');
            Route::post('/filter/pagination', 'Reports\ReportController@getPatientStatementStatusFilter');
            // Route::get('/export/{type}', 'Reports\ReportController@patientStatementStatusExport');
            Route::post('/export', 'Reports\ReportController@patientStatementStatusExport');
            Route::get('/export_pdf', 'Api\CommonExportPdfController@index');
        });
        # Patient statement report ends 

        ### PATIENT REPORT END ###
        ### PATIENT REPORT START ###
        Route::get('/users', 'Reports\ReportController@userReport');
        Route::post('/search/users', 'Reports\ReportController@userSearch');
        Route::get('/users/export/{type}', 'Reports\Api\ReportApiController@getFinancialSearchApi');
        ### PATIENT REPORT END ###
        ### PATIENT REPORT START-PATIENT ADDRESS LIST ###
        Route::group(array('prefix' => 'patientaddresslist'), function() {
            Route::get('/', 'Reports\ReportController@getPatientAddressList');
            Route::post('/filter', 'Reports\ReportController@getPatientAddressListFilter');
            Route::post('/filter/pagination', 'Reports\ReportController@getPatientAddressListFilter');
            Route::post('/filter/export', 'Reports\ReportController@patientAddressListExport');
            Route::get('/filter/export_pdf', 'Api\CommonExportPdfController@index');
        });
        //Route::get('/filter/export/{type}', 'Reports\ReportController@getPatientAddressListExportApi');
        //New Export Function
        // Route::get('/filter/export/{type}', 'Reports\ReportController@patientAddressListExport');
        ### PATIENT ADDRESS LIST END ###

        ### PATIENT DEMOGRAPHICS START ###
        Route::group(array('prefix' => 'patientdemographics'), function() {
            Route::get('/', 'Reports\ReportController@getPatientDemographics');
            //Route::post('/patientdemographicsList', 'Reports\ReportController@getPatientDemographicsFilterAjax');
            Route::post('/filter_demo', 'Reports\ReportController@getPatientDemographicsFilter');
            Route::post('/filter_demo/pagination', 'Reports\ReportController@getPatientDemographicsFilter');
            Route::post('/filter_demo/export', 'Reports\ReportController@patientDemographicsExport');
            Route::get('/filter_demo/export_pdf', 'Api\ExportPdfController@index');
        });
        //Route::get('/filter_demo/export/{type}', 'Reports\ReportController@getPatientDemographicsExportApi');
        // Route::get('/filter_demo/export/{type}', 'Reports\ReportController@patientDemographicsExport');
		//Route::get('/filter_demo/export/{type}', 'Reports\PatientDemographicsController@patientDemographicsExport');        ### PATIENT DEMOGRAPHICS END ###
        ### PATIENT ICD WORKSHEET START ###
        Route::get('/patienticdworksheet', 'Reports\ReportController@getPatientIcdWorksheet');
        Route::post('/patienticdworksheet/export', 'Reports\ReportController@patientIcdWorksheetExport');
        Route::post('/search/patienticdworksheet', 'Reports\ReportController@getPatientIcdWorksheetReport');
        Route::post('/search/patienticdworksheet/pagination', 'Reports\ReportController@getPatientIcdWorksheetReport');
        //Route::get('/filter_icdworksheet/export/{type}', 'Reports\ReportController@getFilterIcdWorksheetExportApi');
        // Route::get('/filter_icdworksheet/export/{type}', 'Reports\ReportController@patientIcdWorksheetExport');
        ### PATIENT REPORTS END-PATIENT ICD WORKSHEET END ###   
        ### PATIENT AR REPORT START ###
        Route::get('/patientarreport', 'Reports\ReportController@getPatientArReportList');
        Route::post('/patientarlist/filter_ar', 'Reports\ReportController@getPatientArReport');
        Route::post('/patientarlist/filter_ar/pagination', 'Reports\ReportController@getPatientArReport');
        //Route::get('/filter_ar/export/{type}', 'Reports\ReportController@getPatientArExportApi');
        Route::get('/filter_ar/export/{type}', 'Reports\ReportController@patientArExport');
        ### PATIENT AR REPORT END ###
        ### PRACTICE SETTING REPORTS START-EMPLOYER LIST ###    
        Route::get('/employerlist', 'Reports\ReportController@getEmployerList');        
        Route::post('/employerlist/filter_employerlist', 'Reports\ReportController@getEmployerListReport');
        Route::post('/employerlist/filter_employerlist/pagination', 'Reports\ReportController@getEmployerListReport');
        //Route::get('/filter_employerlist/export/{type}', 'Reports\ReportController@getEmployerListExportApi');
        Route::get('/filter_employerlist/export/{type}', 'Reports\ReportController@employerListExport');
        ### PRACTICE SETTING REPORTS END-EMPLOYER LIST ###  
        ### VSELVAKUMAR - END OF THE DAY TOTALS START ###

        Route::group(array('prefix' => 'financials'), function() {
            ### VSELVAKUMAR - END OF THE DAY TOTALS START   ###
            Route::get('/enddaytotal', 'Reports\Financials\FinancialController@enddaytotal');
            Route::post('/filter_result', 'Reports\Financials\FinancialController@filter_result');
            Route::post('/filter_result/pagination', 'Reports\Financials\FinancialController@filter_result');
            // Route::get('/enddayexport/{type}', 'Reports\Financials\FinancialController@endDayExport');
            Route::post('/enddayexport/export', 'Reports\Financials\FinancialController@endDayExport');
            ### VSELVAKUMAR - END OF THE DAY TOTALS END   ###

            #### WORK RVU REPORT START Thilagavathy ###   
            Route::get('workrvu', 'Reports\Financials\FinancialController@workrvulist');
            Route::post('workrvureport', 'Reports\Financials\FinancialController@workrvureport');
            Route::post('workrvureport/pagination', 'Reports\Financials\FinancialController@workrvureport');
            // Route::get('/workrvuexport/{type}', 'Reports\Financials\FinancialController@workrvusearchExport');
            Route::post('/workrvureport/export', 'Reports\Financials\FinancialController@workrvusearchExport');
            Route::get('/workrvureport/export/pdf', 'Api\CommonExportPdfController@index');
            #### WORK RVU REPORT END ###   

            #### CHARGE CATEGORY REPORT START Thilagavathy ###   
            Route::get('chargecategory', 'Reports\Financials\FinancialController@chargecategory');
            Route::post('chargecategoryreport', 'Reports\Financials\FinancialController@chargecategoryreport');
            Route::post('chargecategoryreport/pagination', 'Reports\Financials\FinancialController@chargecategoryreport');
            // Route::get('/chargecategoryexport/{type}', 'Reports\Financials\FinancialController@chargecategorysearchExport');
            Route::post('/chargecategory/export', 'Reports\Financials\FinancialController@chargecategorysearchExport');
            Route::get('/chargecategory/export_pdf', 'Api\CommonExportPdfController@index');
            Route::get('chargelist', 'Reports\Billing\BillingController@chargelist');
            Route::get('chargelistv2', 'Reports\Billing\BillingController@chargelistV2');
            Route::post('chargelistreport', 'Reports\Billing\BillingController@chargelistreport');
            Route::post('chargelistreport/pagination', 'Reports\Billing\BillingController@chargelistreport');
            Route::post('/chargelist/export', 'Reports\Billing\BillingController@chargeListSearchExport');
            Route::get('/chargelist/export_pdf', 'Api\CommonExportPdfController@index');
            #### CHARGE CATEGORY REPORT REPORT END ###   

            ###  Unbilled Reports Revamp : VSELVAKUMAR ###        
            Route::get('/unbilledreports', 'Reports\Financials\FinancialController@getUnbilledreportsfilter');
            Route::post('/unbilledreports/filter_unbilled','Reports\Financials\FinancialController@getUnbilledreports');
            // Route::get('/unbilledreports/filter_unbilled/{type}','Reports\Financials\FinancialController@unbilledexport');
            Route::post('/unbilledreports/filter_unbilled/export','Reports\Financials\FinancialController@unbilledexport');
            Route::post('/unbilledreports/filter_unbilled/pagination', 'Reports\Financials\FinancialController@getUnbilledreports');
            ###  Unbilled Reports Revamp : VSELVAKUMAR ###

            ### VSELVAKUMAR - Payer Analysis START ###
            Route::get('/payeranalysis', 'Reports\ReportController@payeranalysis');
            Route::get('/changeinsuranceid', 'Reports\ReportController@updatesample');
            Route::post('/payerfilter', 'Reports\ReportController@payerfilter');
            Route::post('/payerfilter/pagination', 'Reports\ReportController@payerfilter');
            //Route::get('/financials/payerexport/{export}', 'Reports\Api\ReportApiController@getPayerexportApi');
            Route::get('/payerexport/{export}', 'Reports\ReportController@payerfilterexport');
            ### VSELVAKUMAR - Payer Analysis END ###

            ### VSELVAKUMAR - provider_reimbursement_filter START ###
            Route::get('/provider-reimbursement', 'Reports\ReportController@provider_reimbursement');
            Route::post('/provider-reimbursement-filter', 'Reports\ReportController@provider_reimbursement_filter');
            //Route::get('/financials/provider-reimbursement-export/{export}', 'Reports\Api\ReportApiController@getProviderFilterExportApi');
            Route::get('/provider-reimbursement-export/{export}', 'Reports\ReportController@getProviderFilterExport');
            ### VSELVAKUMAR - provider_reimbursement_filter END ###

            Route::post('search/aginganalysis', 'Reports\ReportController@aginganalysissearch');
            Route::post('search/aginganalysis/pagination', 'Reports\ReportController@aginganalysissearch');
        });
        
        //Route::get('/filter_unbilled/export/{type}', 'Reports\Financials\Api\FinancialApiController@getUnbilledClaimexportApi');
        //Route::get('/filter_unbilled/export/{type}', 'Reports\Financials\FinancialController@unbilledexport');
        //Route::get('/financials/enddayexport/{type}', 'Reports\Financials\FinancialController@getEnddayexport');
       
        ### AGING ANALYSIS REPORT START ###
        Route::get('ar/aginganalysis', 'Reports\ReportController@aginganalysislist');
        // Route::get('/aginganalysis/export/{type}', 'Reports\ReportController@getAgingReportSearchExport');
        Route::post('/aginganalysis/export/', 'Reports\ReportController@getAgingReportSearchExport');//Export
        ### AGING ANALYSIS REPORT END ###

        ###   CSELVAKUMAR - Provider List   ### 
        Route::group(array('prefix' => 'practicesettings'), function() {
            Route::get('providerlist', 'Reports\Practicesettings\ProviderlistController@providerlist');
            Route::get('providerSummary', 'Reports\Practicesettings\ProviderlistController@providerSummary');
            Route::post('filter_resultProvider', 'Reports\Practicesettings\ProviderlistController@filter_resultProvider');
            Route::post('filter_resultProvider/pagination', 'Reports\Practicesettings\ProviderlistController@filter_resultProvider');
            Route::post('filter_group_resultProvider', 'Reports\Practicesettings\ProviderlistController@filter_group_resultProvider');
            
            ###   CSELVAKUMAR - Facility List   ### 
            Route::get('facilitylist', 'Reports\Practicesettings\FacilitylistController@facilitylist');
            Route::get('facilitySummary', 'Reports\Practicesettings\FacilitylistController@facilitySummary');
            Route::post('filter_result', 'Reports\Practicesettings\FacilitylistController@filter_result');
            Route::post('filter_result_summary', 'Reports\Practicesettings\FacilitylistController@filter_result_summary');
            Route::post('filter_result_summary/pagination', 'Reports\Practicesettings\FacilitylistController@filter_result_summary');
            Route::post('filter_result/pagination', 'Reports\Practicesettings\FacilitylistController@filter_result');
            Route::post('filter_group_result', 'Reports\Practicesettings\FacilitylistController@filter_group_result');

            ###   CSELVAKUMAR - Insurance List   ### 
            Route::get('insurancelist', 'Reports\Practicesettings\InsurancelistController@insurancelist');
            Route::post('filter_insurance_result', 'Reports\Practicesettings\InsurancelistController@filter_insurance_result');
            Route::post('filter_insurance_result/pagination', 'Reports\Practicesettings\InsurancelistController@filter_insurance_result');
            Route::post('filter_insurance_result/export', 'Reports\Practicesettings\InsurancelistController@insuranceListExport');

            ###   CSELVAKUMAR - CPT List   ### 
            Route::get('cptlist', 'Reports\Practicesettings\CptlistController@cptlist');
            Route::post('filter_cpt_result', 'Reports\Practicesettings\CptlistController@filter_cpt_result');
            Route::post('filter_cpt_result/pagination', 'Reports\Practicesettings\CptlistController@filter_cpt_result');
            //Route::get('/filter_cpt_result/export/{type}', 'Reports\Practicesettings\Api\CptlistApiController@getCptListExportApi');

        });
        //Route::get('/filter_resultProvider/export/{type}', 'Reports\Practicesettings\Api\ProviderlistApiController@getProviderListExportApi');
        // Route::get('/filter_resultProvider/export/{type}', 'Reports\Practicesettings\ProviderlistController@providerListExport');
        Route::post('/filter_resultProvider/export', 'Reports\Practicesettings\ProviderlistController@providerListExport');
        Route::get('/filter_resultProvider/export_pdf', 'Api\CommonExportPdfController@index');

        //Route::get('/filter_result/export/{type}', 'Reports\Practicesettings\Api\FacilitylistApiController@getFacilityListExportApi');
        Route::get('/filter_result/export/{type}', 'Reports\Practicesettings\FacilitylistController@facilityListExport');
        // Route::get('/filter_result_summary/export/{type}', 'Reports\Practicesettings\FacilitylistController@facilityListSummaryExport');
        Route::post('/filter_result_summary/export', 'Reports\Practicesettings\FacilitylistController@facilityListSummaryExport');
        Route::get('/filter_result_summary/export_pdf', 'Api\CommonExportPdfController@index');
        
        // Route::get('/filter_insurance_result/export/{type}', 'Reports\Practicesettings\InsurancelistController@insuranceListExport');
        
        // Route::get('/filter_cpt_result/export/{type}', 'Reports\Practicesettings\CptlistController@cptListExport');
        Route::post('/filter_cpt_result/export', 'Reports\Practicesettings\CptlistController@cptListExport');
        Route::get('/filter_cpt_result/export_pdf', 'Api\CommonExportPdfController@index');
        
        Route::group(array('prefix' => 'ar'), function() {
            Route::get('aginganalysisdetails', 'Reports\Financials\FinancialController@aginganalysislist');
            
            # AR Workbench report start
            Route::group(array('prefix' => 'workbench'), function() {
                Route::get('/', 'Reports\Financials\FinancialController@workbenchList');
                Route::post('/', 'Reports\Financials\FinancialController@workbenchReport');
                Route::post('/pagination', 'Reports\Financials\FinancialController@workbenchReport');
                //Route::get('/export/{type}', 'Reports\Financials\FinancialController@workbenchSearchExport');
                Route::post('/export', 'Reports\Financials\FinancialController@workbenchSearchExport');
            });
            # AR Workbench report ends 
            
            # Denial Trend Analysis report start
            Route::group(array('prefix' => 'denialtrendanalysis'), function() {
                Route::get('/', 'Reports\Financials\FinancialController@denialAnalysisList');
                Route::post('/', 'Reports\Financials\FinancialController@denialAnalysisReport');
                Route::post('/pagination', 'Reports\Financials\FinancialController@denialAnalysisReport');
                // Route::get('/export/{type}', 'Reports\Financials\FinancialController@denialAnalysisSearchExport');
                Route::post('/export', 'Reports\Financials\FinancialController@denialAnalysisSearchExport');
                Route::get('/export_pdf', 'Api\CommonExportPdfController@index');
            });
            # Denial Trend Analysis report ends 
            
        });

        Route::group(array('prefix' => 'financials'), function() {
            //Route::get('aginganalysisdetails', 'Reports\Financials\FinancialController@aginganalysislist');
            Route::any('search/aginganalysisdetails', 'Reports\Financials\FinancialController@aginganalysissearch');
            Route::post('search/aginganalysisdetails/pagination', 'Reports\Financials\FinancialController@aginganalysissearch');
        });
        //Route::get('aginganalysisdetails/export/{type}', 'Reports\Financials\FinancialController@agingDetailsReportExport');
        Route::post('aginganalysisdetails/export', 'Reports\Financials\FinancialController@agingDetailsReportExport');
        //Generated Reports
        Route::get('generated_reports', 'Reports\ReportController@generated_reports');
        Route::get('generated_reports_view', 'Reports\ReportController@generated_reports_view');
    });
    ### REPORTS MODULE END ###
    
    Route::group(array('prefix' => 'armanagement'), function() {
        Route::get('/insurance', 'Armanagement\ArmanagementController@insurance');
        Route::post('/insurancewise', 'Armanagement\ArmanagementController@insurancewise');
        Route::post('/statuswise', 'Armanagement\ArmanagementController@statuswise');
        Route::get('/insclaims', 'Armanagement\ArmanagementController@insclaims');
        Route::get('/insurance1', 'Armanagement\ArmanagementController@insurance1');
        Route::get('/1', 'Armanagement\ArmanagementController@index1');
        Route::get('/', 'Armanagement\ArmanagementController@index');

        // For claims list server script call
        Route::get('/claimsList', 'Armanagement\ArmanagementController@indexTableData');
        Route::get('/armanagementlist', 'Armanagement\ArmanagementController@armanagementlist');
        Route::post('/armanagementlist', 'Armanagement\ArmanagementController@armanagementlist');

        Route::get('/denials', 'Armanagement\ArmanagementController@armanagementDenialList');
        Route::post('/denials', 'Armanagement\ArmanagementController@armanagementDenialList');
        Route::get('/deniallistAjax', 'Armanagement\ArmanagementController@getARDenialListAjax');
        Route::get('/denials/{export}', 'Armanagement\ArmanagementController@arDenialListExport');//For AR Denial List Export

        Route::get('/myfollowup', 'Armanagement\ArmanagementController@myfollowup');
        Route::get('/otherfollowup', 'Armanagement\ArmanagementController@otherfollowup');
        Route::get('/problemlist', 'Patients\ProblemListController@getProblemList');
        Route::get('/myproblemlist', 'Patients\ProblemListController@getProblemList');
        Route::get('/problemlistAjax', 'Patients\ProblemListController@getProblemListAjax');
		Route::get('/problemlistAjax/{type}/{export}', 'Patients\ProblemListController@getWorkbenchListExport');//For Assigned and Total Workbench List Export

        Route::post('/dynamic', 'Armanagement\ArmanagementController@getdynamicdocument');  

        Route::get('/summary', 'Armanagement\ArmanagementController@armanagementDenialSummary');
    });

    /*dashboard AR Analytics*/
    Route::get('analytics/armanagement', 'Armanagement\ArmanagementController@arAnalytics');

	Route::post('/documentTitle', 'Documents\DocumentController@getDocumentTitle');
    ### DOCUMENTS MODULE START ###
    Route::resource('documents', 'Documents\DocumentController');

    Route::group(array('prefix' => 'documents'), function() {
        Route::get('/statsdetail/list', 'Documents\DocumentController@getStatsDetail');
        Route::post('/module/addform', 'Documents\DocumentController@store');
        Route::get('/list/{module}', 'Documents\DocumentController@getList');
        Route::get('/getcategorylist/{category}', 'Documents\DocumentController@getCategory');
        Route::post('/paymentPostingUpload', 'Documents\DocumentController@paymentPostingUpload');
        Route::get('/getpatientclaim/{patient_id}', 'Documents\Api\DocumentApiController@getPatientClaimsApi');        
        Route::get('/delete/{cust_id}', 'Documents\DocumentController@destroy');        
        /* selvakumar document url for main document */
        
        Route::post('/dynamic', 'Documents\DocumentController@getdynamicdocument');     
        Route::post('/dynamic/filter', 'Documents\DocumentController@getdynamicfilterdocument');    
        Route::post('/api/common_delete', 'Api\DocumentApiController@document_common_delete');  
        Route::post('/api/common_title_check', 'Api\DocumentApiController@document_common_title');
        Route::get('/bulkdownload/{document_ids}', 'Documents\Api\DocumentApiController@downloadBulkDocument');
        Route::get('/getpatient_document/{patient_id}', 'Patients\PatientDocumentsController@patient_document');
    });
    Route::post('patientdocuments/dynamic/filter/{patient_id}', 'Patients\PatientDocumentsController@search_patient_document');
    /* selvakumar document url for main document */
    
    
    ### DOCUMENTS MODULE END ###
    #### User list Start ####
    Route::resource('users', 'UserlistController');
    #### User list End ####

    Route::get('practices/useractivity', 'UserActivityController@index');
    Route::post('practices/useractivitylist', 'UserActivityController@index');
    Route::get('api/admin/history/{export}', 'Medcubics\Api\UserHistoryApiController@getIndexApi');
    
	///*** Starts - Medcubics Admin Url's ***///
    Route::group(array('prefix' => 'admin'), function() {
        Route::get('customers/{id}/delete/{customers}', 'Medcubics\CustomerController@customerAvatar');
        Route::get('customer/setpractice/{id}', 'Medcubics\CustomerController@setPractice');
        Route::resource('customer', 'Medcubics\CustomerController');
        Route::get('customer/delete/{cust_id}', 'Medcubics\CustomerController@destroy');

        ### Admin Users ###
		Route::post('adminuser/updateSecurityCode', 'Medcubics\AdminuserController@updateSecurityCode');
        Route::resource('adminuser', 'Medcubics\AdminuserController');

        Route::group(array('prefix' => 'adminuser'), function() {
            Route::get('/delete/{id}', 'Medcubics\AdminuserController@destroy');
            Route::post('/updateLoginAttempt', 'Medcubics\AdminuserController@updateLoginAttempt');
            Route::post('/updateLoggedin', 'Medcubics\AdminuserController@logoutUser');
            Route::post('/updateUserStatus', 'Medcubics\AdminuserController@updateUserStatus');
            Route::post('/userEmailValidate', 'Medcubics\AdminuserController@userEmailValidate');
            Route::post('/userShortNameValidate', 'Medcubics\AdminuserController@userShortNameValidate');
			
			
        });
        Route::get('api/customer/{id}', 'Medcubics\AdminuserController@customerPractice');
        Route::get('api/practice/{id}', 'Medcubics\AdminuserController@PracticeProviders');
        Route::resource('adminpermission', 'Medcubics\AdminpermissionController');

        //ADmin Password
        Route::resource('userpassword', 'Medcubics\AdminPasswordController');
        Route::post('userpassword/changepassword', 'Medcubics\AdminPasswordController@updatepassword');

        ### Starts - Users ###
        Route::resource('customer/{id}/customerusers', 'Medcubics\CustomerUsersController');
        Route::get('api/useraccess/{id}', 'Medcubics\Api\CustomerUsersApiController@userAccess');
        Route::get('getrolespermissions', 'Medcubics\Api\SetPracticeforUsersApiController@getrolespermissions');
        Route::get('getuserapi', 'Medcubics\Api\SetPracticeforUsersApiController@getUserAPI');
        Route::get('customer/{cust_id}/customerusers/delete/{customerusers_id}', 'Medcubics\CustomerUsersController@destroy');
        
        ### Ends - Users ###
        ####
        Route::resource('customer/{customer_id}/customerusers/{practice_id}/setusersforpractice/{customerusers_id}/user', 'Medcubics\SetUsersforPracticeController');
        Route::get('customer/{customer_id}/customerusers/setpracticeforusers/{practice_id}/user/{user_id}/edit', 'Medcubics\SetUsersforPracticeController@edit');
        Route::any('customer/{customer_id}/customerusers/setpracticeforusers/{practice_id}/user/{user_id}/updates', 'Medcubics\SetUsersforPracticeController@update');
        Route::get('{customer_id}/setpracticeforusers/{user_id}/delete/{practice_id}', 'Medcubics\SetUsersforPracticeController@destroy');
        ####
        ### Starts - set practice for customer users ###
        Route::resource('customer/{customer_id}/customerusers/{customerusers_id}/setpracticeforusers', 'Medcubics\SetPracticeforUsersController');
        Route::get('setpracticeforusers/{user_id}/delete/{practice_id}/{customer_id}', 'Medcubics\SetPracticeforUsersController@destroy');
        Route::get('customerusersmedcubics/{id}/{export}', 'Medcubics\Api\CustomerUsersApiController@getIndexApi');
        ### Ends - set practice for customer users ###

        Route::resource('customer/{id}/customernotes', 'Medcubics\CustomerNotesController');
        Route::get('customer/{cust_id}/customernotes/delete/{customernotes_id}', 'Medcubics\CustomerNotesController@destroy');
        Route::get('customernotesmedcubics/{id}/{export}', 'Medcubics\Api\CustomerNotesApiController@getIndexApi');

        ### Starts - Admin customer providers ###
        Route::resource('customer/{id}/customerproviders', 'Medcubics\CustomerProvidersController');
        Route::get('customer/{cust_id}/customerproviders/delete/{customerproviders_id}', 'Medcubics\CustomerProvidersController@destroy');
        Route::get('providerreports/{id}/{export}', 'Medcubics\Api\PracticeProvidersApiController@getIndexApi');
        ### Ends - Admin customer providers ###
        ### Start - Admin customer practice user ###
        Route::get('practiceuserreports/customer/{customer_id}/{practice_id}/{export}', 'Medcubics\Api\SetPracticeforUsersApiController@practiceUserApi');
        ### Ends - Admin customer practice user ###
        // Starts Roles
        Route::resource('setpagepermissions', 'Medcubics\SetPagepermissionsController');
        Route::resource('modulepermissions', 'Medcubics\ModulePermissionsController');
        ### Ends - Roles ###
        ## Starts - Admin Practices ##
        /* Route::get('admin/customerusers', 'Medcubics\Api\CustomerPracticesApiController@getIndexApi');
          Route::get('admin/customerusers/create', 'Medcubics\Api\CustomerPracticesApiController@getCreateApi'); */
        /* Route::post('admin/customerusers/store', 'Medcubics\Api\CustomerPracticesApiController@getStoreApi');
          Route::get('admin/customerusers/edit/{id}', 'Medcubics\Api\CustomerPracticesApiController@getEditApi');
          Route::post('admin/customerusers/update/{id}', 'Medcubics\Api\CustomerPracticesApiController@getUpdateApi');
          Route::get('admin/customerusers/delete/{id}', 'Medcubics\Api\CustomerPracticesApiController@getDeleteApi'); */

        Route::get('customerpracticesmedcubics/{id}/{export}', 'Medcubics\Api\CustomerPracticesApiController@getIndexApi');
        Route::get('customer/{id}/delete/{avatar_name}', 'Medcubics\CustomerPracticesController@avatarProvider');
        Route::resource('customer/{id}/customerpractices', 'Medcubics\CustomerPracticesController');
        Route::get('customer/{customer_id}/practice/{practice_id}/provider/{provider_id}/delete/{avatar_name}', 'Medcubics\PracticeProvidersController@avatarProvider');
        Route::resource('customer/{customer_id}/practice/{practice_id}/providers', 'Medcubics\PracticeProvidersController');

        //Route::get('customer/{customer_id}/practice/{practice_id}/providers/create','Medcubics\PracticeProvidersController@create');
        Route::get('customer/{customer_id}/practice/{practice_id}/providers/{id}/delete', 'Medcubics\PracticeProvidersController@destroy');
        Route::get('customer/{customer_id}/practice/{practice_id}/users', 'Medcubics\SetPracticeforUsersController@practiceUser');
        #Customer -> Practice -> Users
        Route::resource('customer/{customer_id}/practice/{practice_id}/practiceusers', 'Medcubics\PracticeUserController');
        Route::get('customer/{customer_id}/practice/{practice_id}/practiceusers/show/{practice_user_id}', 'Medcubics\PracticeUserController@show');
        Route::get('customer/{customer_id}/practice/{practice_id}/practiceusers/{practice_user_id}/delete', 'Medcubics\PracticeUserController@destroy');
        Route::resource('customer/{customer_id}/practiceusers/{customerusers_id}/setpracticeforusers', 'Medcubics\SetPracticeforUsersController');
        // Route::get('customer/{customer_id}/practice/{practice_id}/practiceusers/create', 'Medcubics\PracticeUserController@index');

        /* Route::get('export', function()
          {
          DbExportHandler::migrate();
          });
          Route::get('customer/{id}/customerpractices/newdb', function()
          {
          DbExportHandler::migrate('newpractice');
          }); */
        ## Ends - Admin Practices ##
        ### Starts - Fee Schedule ###
        Route::resource('feeschedule', 'Medcubics\FeescheduleController');
        Route::get('feeschedule/{feeschedule_id}/delete', 'Medcubics\FeescheduleController@destroy');

        // Import Fee schedule
        Route::get('importfeeschedules', 'Medcubics\FeescheduleController@getImport');
        Route::post('importfeeschedules', 'Medcubics\FeescheduleController@postImport');
        ### Ends - Fee Schedule ###
        ### Starts - Codes ###    
        Route::resource('code', 'Medcubics\CodeController');
        Route::get('code/delete/{code_id}', 'Medcubics\CodeController@destroy');

        // Codes Import
        Route::get('importcodes', 'Medcubics\CodeController@getImport');
        Route::post('importcodes', 'Medcubics\CodeController@postImport');
        ### Ends - Fee Schedule ###
        
        Route::post('codes/setRuleEngine','RuleEngine\RuleEngineContoller@updateCodeRuleEngine');
        Route::get('getRuleEngine','RuleEngine\RuleEngineContoller@getRuleEngine');

        ### Starts - Modifiers ###
        Route::resource('modifierlevel1', 'Medcubics\ModifierController');
        Route::get('modifierlevel1/delete/{id}', 'Medcubics\ModifierController@destroy');

        // Modifiers level 2
        Route::resource('modifierlevel2', 'Medcubics\ModifierLevelController');
        Route::get('modifierlevel2/delete/{id}', 'Medcubics\ModifierLevelController@destroy');
        ### Ends - Modifiers ###
        // Modifiers Import
        Route::get('importmodifiers', 'Medcubics\ModifierController@getImport');
        Route::post('importmodifiers', 'Medcubics\ModifierController@postImport');
        ### Ends - Modifiers ###
        ### Starts - Qualifiers ###
        Route::resource('qualifiers', 'Medcubics\QualifierController');
        Route::get('qualifiers/{qualifier_id}/delete', 'Medcubics\QualifierController@destroy');
        ### Ends - Qualifiers ###
        ### Starts - Provider Degree ###
        Route::resource('providerdegree', 'Medcubics\ProviderDegreeController');
        Route::get('providerdegree/{degree_id}/delete', 'Medcubics\ProviderDegreeController@destroy');
        ### Ends - Provider Degree ###
        ### Starts - CPT ###
        Route::resource('cpt', 'Medcubics\CptController');
        Route::get('cpt/{cptid}/delete', 'Medcubics\CptController@destroy');
        Route::get('searchcpt', 'Medcubics\CptController@searchIndex');
        // CPT Import
        Route::get('importcpt', 'Medcubics\CptController@getImport');
        Route::post('importcpt', 'Medcubics\CptController@postImport');
        ### Ends - CPT ###
        ### Starts - ICD ###
        // ICD-10
        Route::resource('icd', 'Medcubics\IcdController');
        Route::get('icd/{icd_id}/delete', 'Medcubics\IcdController@destroy');
        Route::get('searchicd', 'Medcubics\IcdController@searchIndex');
        // ICD10 Import
        Route::get('importicd10', 'Medcubics\IcdController@getImport');
        Route::post('importicd10', 'Medcubics\IcdController@postImport');
        ### Ends - Fee Schedule ###  
        ### Starts - Speciality ###
        Route::resource('speciality', 'Medcubics\SpecialityController');
        Route::get('speciality/delete/{id}', 'Medcubics\SpecialityController@destroy');

        ### Ends - Speciality ###
        ### Starts - Taxanomy ###
        Route::resource('taxanomy', 'Medcubics\TaxanomyController');
        Route::get('taxanomy/delete/{id}', 'Medcubics\TaxanomyController@destroy');

        ### Starts - Error log ###
        Route::resource('errorlog', 'Medcubics\LogController');
        Route::get('viewlog/{file_name}', 'Medcubics\LogController@view_log');

        ### Starts - Updates log ###
        Route::get('updates/getattachment/{id}', 'Medcubics\Api\UpdatesApiController@getBlogdocumentApi');
        Route::resource('updates', 'Medcubics\UpdatesController');        
        Route::get('updates/{order?}/{keyword?}', 'Medcubics\UpdatesController@index');
        Route::get('updatesorder/{order?}/{keyword?}', 'Medcubics\UpdatesController@index');
        
        ### Ends - Taxanomy ###
        ### Starts - POS ###
        Route::resource('placeofservice', 'Medcubics\PosController');
        Route::get('placeofservice/delete/{id}', 'Medcubics\PosController@destroy');
        ### Ends - POS ###
        ### Starts - Insurance Types ###
        Route::resource('insurancetypes', 'Medcubics\InsuranceTypesController');
        Route::get('insurancetypes/delete/{id}', 'Medcubics\InsuranceTypesController@destroy');


        ### Ends - Insurance Types ###
        ### Starts - Insurance ###
        Route::get('insurance/deletepicture/{id}', 'Medcubics\InsuranceController@avatarinsurance');
        Route::resource('insurance', 'Medcubics\InsuranceController');
        Route::get('insurance/delete/{id}', 'Medcubics\InsuranceController@destroy');
         ### Starts - Admin Reports  ###
		Route::post('report/customer', 'Medcubics\ReportController@index');
        Route::resource('report/customer', 'Medcubics\ReportController');
		
		Route::get('show_practice/{id}', 'Medcubics\ReportController@show_practice');
		Route::get('show_practice_data/{id}', 'Medcubics\ReportController@show_practice_data');
	
		
        ### Starts - User Login History ###
        Route::post('usersettings', 'Medcubics\UserLoginHistoryController@userSettingFillter');
        Route::get('userLoginHistory/{pageType}', 'Medcubics\UserLoginHistoryController@index');
        Route::post('userStatusChange', 'Medcubics\UserLoginHistoryController@userStatusChange');
        Route::post('userIpSecurityCodeRest', 'Medcubics\UserLoginHistoryController@userIpSecurityCodeRest');
        Route::post('securityCodeApproval','Medcubics\UserLoginHistoryController@givApproval');
        Route::post('userSettingsRemoveApproval','Medcubics\UserLoginHistoryController@removeApproval');
        ### Ends - User Login History ###
        
        // Insurance Overrides
        Route::resource('insurance/{insurance_id}/insuranceoverrides', 'Medcubics\InsuranceOverridesController');
        Route::get('insurance/{insuranceid}/insuranceoverrides/delete/{id}', 'Medcubics\InsuranceOverridesController@destroy');

        // Insurance Master
        Route::resource('insurance', 'Medcubics\InsuranceController');
        Route::get('insurance/delete/{id}', 'Medcubics\InsuranceController@destroy');
        Route::get('addnewselect', 'InsuranceController@addnewselect');

        // Insurance Master Overrides
        Route::resource('insurance/{insurance_id}/insuranceoverrides', 'Medcubics\InsuranceOverridesController');
        Route::get('insurance/{insuranceid}/insuranceoverrides/delete/{id}', 'Medcubics\InsuranceOverridesController@destroy');
        Route::any('insuranceappealaddress/{id}/{export}', 'Medcubics\Api\InsuranceAppealAddressApiController@getIndexApi');

        // Insurance Master Appeal address
        Route::resource('insurance/{insurance_id}/insuranceappealaddress', 'Medcubics\InsuranceAppealAddressController');
        Route::get('insurance/{insuranceid}/insuranceappealaddress/delete/{id}', 'Medcubics\InsuranceAppealAddressController@destroy');
        ### Ends - Insurance ###
        // Roles Master Overrides
        Route::resource('role', 'Medcubics\RoleController');
        Route::get('role/delete/{id}', 'Medcubics\RoleController@destroy');

        // Practice Role
        Route::get('practicerole', 'Medcubics\RoleController@practice_permission');
        Route::resource('medcubicsrole', 'Medcubics\RoleController');

        // User Activity
        //Route::resource('useractivity', 'Medcubics\UserActivityController');
        Route::get('useractivity', 'Medcubics\UserActivityController@index');
        Route::post('useractivitylist', 'Medcubics\UserActivityController@store');

        Route::get('getuseractivitylist', 'Medcubics\Api\UserActivityApiController@get_activitylist');
        Route::get('setuserpractice/{id}', 'Medcubics\Api\CustomerApiController@setPracticeApi');

        ### User History Start ###  
        #Route::resource('userhistory', 'Medcubics\UserHistoryController');
        Route::get('userhistory', 'Medcubics\UserHistoryController@index');
        Route::any('userhistorylist', 'Medcubics\UserHistoryController@getList');

        ### User History End ###    
        /// Admin Help Start//
        Route::resource('staticpage', 'Medcubics\StaticPageController');
        Route::get('staticpage/delete/{staticpage_id}', 'Medcubics\StaticPageController@destroy');

        /// API Start//
        Route::resource('apilist', 'Medcubics\ApiListController');
        Route::get('apilist/delete/{id}', 'Medcubics\ApiListController@destroy');


        // Common help page
        Route::get('admin/help/{type}', 'StaticPageController@getHelpContent');
        /// Admin Help End ///  
        ### Starts - FAQ ###
        Route::resource('faq', 'Medcubics\FaqController');
        Route::get('faq/delete/{id}', 'Medcubics\FaqController@destroy');

        ### End FAQ end###
        ### Starts - Manage Ticket ###
        Route::resource('manageticket', 'Medcubics\ManageticketController');
        Route::post('manageticket/update/{id}', 'Medcubics\ManageticketController@store');
        Route::get('assignticket/{ticketid}/{userid}', 'Medcubics\ManageticketController@assignticket');
        Route::get('getmedcubicsuserlist/{ticketid}/{id?}', 'Medcubics\ManageticketController@getUserList');
        Route::resource('managemyticket', 'Medcubics\ManageticketController');        
        Route::resource('createnewticket', 'Medcubics\AdminTicketController');

        Route::get('api/manageticketreports/{export}', 'Medcubics\Api\ManageticketApiController@getIndexApi');
        //Route::get('manageticket/delete/{id}', 'Medcubics\ManageticketController@destroy');
        ### End Manage Ticket end###
        ### Starts - EDI ###
        Route::resource('edi', 'Medcubics\ClearingHouseController');
        Route::get('edi/delete/{id}', 'Medcubics\ClearingHouseController@destroy');
        Route::get('api/edireports/{export}', 'Medcubics\Api\ClearingHouseApiController@getIndexApi');
        ### Ends - EDI ###      

        Route::get('metrics', 'Medcubics\MetricsController@index');
        Route::post('metrics/search_customers', 'Medcubics\MetricsController@getcustomers');

        ### Starts - Invoice ###
        Route::get('reports', 'Medcubics\InvoiceController@index');

        Route::group(array('prefix' => 'invoice'), function() {
            Route::get('/', 'Medcubics\InvoiceController@index');
            Route::get('/create', 'Medcubics\InvoiceController@create');
            Route::post('/generateInvoice', 'Medcubics\InvoiceController@generateInvoice');
            Route::post('/store', 'Medcubics\InvoiceController@store');
            Route::post('/report', 'Medcubics\InvoiceController@getReport');
            Route::get('/report/{id}', 'Medcubics\InvoiceController@getReport');
        });
        // Route::get('edi/delete/{id}', 'Medcubics\ClearingHouseController@destroy');
        ### Ends - Invoice ###      

    });
    ///*** Ends - Medcubics Admin Url's ***///
    
    ///*** Starts - Scheduler Url's ***///
    //Route::get('scheduler','SchedulerController');

    Route::get('scheduler1', 'Scheduler\SchedulerController@index1');
    Route::get('scheduler2', 'Scheduler\SchedulerController@index2');
    
    /* Patients ssn Validation for already avb our application or not : START */
        Route::post('ssn-validation', "Patients\PatientController@patientSsnValidation");
    /* Patients ssn Validation for already avb our application or not : END */
    
    /* Provider Short Name Validation for already avb our application or not : START */
        Route::post('providerShortNameValidation', "Patients\BillingController@providerShortNameValidation");
    /* Provider Short Name Validation for already avb our application or not : END */
    
        Route::get('er_pdf', 'Payments\PaymentController@payments_er_pdf');
		
        Route::get('ClearingRespChange/{claimNo}', 'Payments\PaymentController@ClearingRespChange');
        
        Route::get('eraPDFData/{id}/{filename}/{type}', 'Payments\Era835Controller@pdf_generation');

        Route::get('eraRsponseFile/{checkno}', 'Payments\Era835Controller@EraResponseFile');
        
        Route::post('autoPostData', 'Payments\Era835Controller@autoPostData');
        
        Route::get('autoPostStatus/{checkno}/{id?}', 'Payments\Era835Controller@getAutoPostStatus');
		
		
        Route::get('835SegmentData/{checkno}/{id}', 'Payments\Era835Controller@get835Data');
                
    /* Patients name and dob for already avb our application or not : END */
        Route::post('patient-check', "Patients\PatientController@patient_check");
    // Patients name and dob for already avb our application or not : END 
    
    
    /* sample function for Check the Edi ftp Connection  */
    Route::get('ediconnection', 'Api\EdiApiController@edi_connection_check');
    Route::get('check277Segment', 'Api\EdiApiController@check277Segment');
    /* sample function for Check the Edi ftp Connection  */
    
    // Route::get('scheduler2','SchedulerMainController@index3');
    Route::group(array('prefix' => 'scheduler', 'namespace' => 'Scheduler'), function() {
        Route::resource('', 'SchedulerController@index');
        Route::resource('scheduler', 'SchedulerController');
        Route::get('setdefaultandresourcelist/{defaultview}', 'SchedulerController@setDefaultAndResourceList');
        Route::get('getresourceslisting/{type}/{default_view}/{default_view_list_id}/{resource_ids?}', 'SchedulerController@getCalendarResourcesListing');
        Route::get('getresourceslistingcalendar/{type}/{default_view}/{default_view_list_id}/{resource_ids?}', 'SchedulerController@getCalendarResourcesApi');
        Route::get('getresourceevents/{default_view_list_id}/{resource_ids?}', 'SchedulerController@getCalendarEventsApi');

        Route::get('getappointment', 'SchedulerController@getAppointment');
        Route::get('mail_test', 'SchedulerController@mail_test');
        Route::get('addnewpatient', 'SchedulerController@getNewPatient');
        Route::get('getavailabletimeslot', 'SchedulerController@getAvailableSlotTimeByDateApi');
        Route::get('searchpatient/{patient_search_category?}', 'SchedulerController@getPatientSearchResults');
        Route::get('getresourcesbydefaultviewlistid', 'Api\SchedulerApiController@getResourcesByDefaultViewListId');
        Route::get('getscheduledatesbyresourceid', 'Api\SchedulerApiController@getScheduleDatesByResourceId');
        Route::get('storeappointment', 'Api\SchedulerApiController@storeAppointment');
        Route::get('updateappointment', 'Api\SchedulerApiController@updateAppointment');
        Route::get('getappointmentbyevent', 'SchedulerController@getAppointmentDetails');
        Route::get('getappointmentbyeventreschedule', 'SchedulerController@getAppointmentDetailsReschedule');
        Route::get('appointmentdeletecancelprocess/{event_str_arr}', 'Api\SchedulerApiController@appointmentDeleteCancelProcess');
		Route::get('documentsearchpatient/{patient_search_category?}', 'SchedulerController@getDocumentPatientSearchResults');
        Route::get('rescheduleappintmentdrag', 'Api\SchedulerApiController@rescheduleAppointmentWithDrag');
        Route::get('list/{pro_id?}/{fac_id?}/{curdate?}/{patient?}', 'ListingController@index'); //App.List page 
        Route::post('/keywordsearch', 'ListingController@index'); //App.List search page
        Route::get('addnewselect', 'SchedulerController@addnewselect');
        Route::get('appointmentStatsdynamic_count/{scheduler_calendar_val}/{default_view_option_val}/{default_view_list_option_val}/{resource_option_val}/{view_option}', 'SchedulerController@getappointmentStatsdynamic_count');
        Route::get('geteventschedulardate/{event_id}', 'Api\SchedulerApiController@geteventschedulardate');
        Route::get('check_no/{check_no}/{patientId}', 'Api\SchedulerApiController@checknoApiUnique');
        Route::get('appointmentlist', 'AppointmentListController@index');
        Route::get('appointmentlist/ajax/{type?}', 'AppointmentListController@getapointmentajax');        
    });

    Route::get('api/schedulerlistreports/{option}/{pro_id?}/{fac_id?}/{date?}/{pat_id?}/{status?}', 'Scheduler\ListingController@getExport');

    ///*** Starts - Profile Events and its related Url's  ***///
    Route::get('profile/newfeauture-model', 'Medcubics\UpdatesController@newfeautureModel');
    Route::group(array('prefix' => 'profile', 'namespace' => 'Profile'), function() {
        Route::resource('', 'ProfileController');
        Route::get('/changepassword', 'ProfileController@getchangepassword');
        Route::post('/changepassword', 'ProfileController@postchangepassword');

        Route::resource('/personal-notes', 'DashboardController');
        Route::resource('/task', 'TaskController');

        //Personal details
        Route::get('/personaldetails/{user_id}', 'PersonaldetailController@personaldetail');
        Route::get('/personaldetailsview/{user_id}', 'PersonaldetailController@personaldetailview');
        Route::any('/personaldetails/{id}/delete/{picture_name}', 'PersonaldetailController@avatarpersonal');

        Route::any('/updatepersonal/{id}', 'PersonaldetailController@updatedetail');

        Route::group(array('prefix' => 'calendar'), function() {
            Route::get('/', 'EventController@getCalendarshow');
            Route::post('event/create', 'EventController@getEventCreate');
            Route::post('event/update/{id}', 'EventController@getEventCreate');
            Route::get('event/delete/{id}', 'EventController@getEventDelete');
            //Route::get('/calendar', 'EventController@index');            
            Route::get('events/add', 'EventController@getCalendarAdd');
            Route::get('event/edit/{id}', 'EventController@getCalendaredit');
            Route::get('event/show/{timestamp}', 'EventController@getCalendarshowTimestamp');
        });
        

        // Blog        
        Route::get('blogs/{order?}/{keyword?}', 'BlogController@bloglisting');
        Route::get('filteruser/{keyword?}', 'BlogController@filteruser');
        Route::get('userblog/{order?}/{keyword?}', 'BlogController@bloglisting');

        Route::resource('blog', 'BlogController');

        Route::group(array('prefix' => 'blog'), function() {
            Route::get('favourite', 'BlogController@favourite');
            Route::get('getblog', 'BlogController@getblog');
            Route::post('comments', 'BlogController@comments');
            Route::post('replycomments', 'BlogController@replycomments');
            Route::get('getreplycomments', 'BlogController@getMoreReplyComments');
            Route::get('getcomments', 'BlogController@getcomments');
            Route::get('deletecomments/{id}/{blogid}', 'BlogController@deletecomments');
            Route::get('delreplycomments/{replyid}/{parentid}', 'BlogController@deletereplycomments');
            Route::get('commentsfavourite', 'BlogController@commentsfavourite');
            Route::get('delete/{id}', 'BlogController@destroy');
            Route::get('group/delete/{id}', 'BlogGroupController@destroy');
        });
        
        
        ## BlogGroupController
        Route::resource('bloggroup', 'BlogGroupController');        

        ## CoverPhoto
        Route::post('addcover', 'BlogController@addCoverPhoto');
        Route::get('removecover', 'BlogController@removeCoverPhoto');

        Route::group(array('prefix' => 'maillist'), function() {
            Route::get('/', 'MailboxController@getMaillist');
            Route::get('/sent', 'MailboxController@getSendMaillist');
            Route::get('/draft', 'MailboxController@getDraftMaillist');
            Route::get('/trash', 'MailboxController@getTrashMaillist');
            Route::get('/settings', 'MailboxController@MailSettings');
            Route::post('/settings/store', 'MailboxController@MailSettingsstore');
            Route::get('/category/{label_id?}', 'MailboxController@getotherLabelMaillist');
            Route::get('/sent/{mail_id?}', 'MailboxController@getSendMailViewDet');
            Route::get('/inbox/{mail_id?}', 'MailboxController@getInboxMailViewDet');
            Route::get('/draft/{mail_id?}', 'MailboxController@getDraftMailViewDet');
            Route::get('/label/{mail_id?}', 'MailboxController@getLabelMailViewDet');
            Route::get('/trash/{mail_id?}', 'MailboxController@getTrashMailViewDet');

            Route::get('/composemail', 'MailboxController@getComposemail');
            Route::get('/replymailprocess/{mail_id}/{reply_all_type?}', 'MailboxController@getReplymail');
            Route::get('/show/{mail_id}', 'MailboxController@getShowmail');
            Route::post('/keywordsearch', 'MailboxController@getKeywordsearch');
            Route::post('/keywordfilter', 'MailboxController@getKeywordfilter');
            Route::get('/{status_read}/{page}/{order}/{order_by}/{labe_id?}', 'MailboxController@getUnreadmail');
        });

        Route::post('getMessageData', 'MessageController@getMessageData');
        Route::post('getInboxCount', 'MessageController@getInboxCount');
        Route::post('getSetTrash', 'MessageController@getSetTrash');
        Route::post('setLabel', 'MessageController@setLabel');
        Route::post('searchmessage', 'MessageController@searchmessage');
        Route::post('getMessageTypeData', 'MessageController@getMessageTypeData');

        Route::group(array('prefix' => 'message'), function() {
            Route::get('/composemail', 'MessageController@getComposemail');
            Route::get('/replaymail/{id}', 'MessageController@getreplaymail');
            Route::get('/forwardmail/{id}', 'MessageController@getforwardmail');
            Route::get('/draftmail/{id}', 'MessageController@getdraftmail');
        });
        Route::resource('/message', 'MessageController');
    });
    ///*** Ends - Profile Events and its related Url's ***///
    //For practice to switch to next practice
    Route::get('practice/switchuser', 'PracticesController@switchuser');
    ///*** Ends - Medcubics Admin Url's ***///
    ///*** Starts - Patients Url's ***///
    // Route::resource('patients',  'Patients\PatientController');
    /* Route::get('patients1/{patient_id}', 'Patients\PatientController@show1');
      Route::get('patients2/{patient_id}',  'Patients\PatientController@show2');
      Route::resource('patientsupdate', 'Patients\PatientController@update');
     */

    Route::get('get_sel_provider_type_display/{sel_provider_id?}', 'ProviderController@get_sel_provider_type_display'); // dropdown provider select type will display   
    Route::get('sel_patientinsurance_address/{sel_insurance_id?}', 'Patients\PatientController@sel_patientinsurance_address'); // dropdown insurance select address will display    
    Route::post('patientsprofile/{patient_id?}', 'Patients\PatientController@patientsprofile');
    Route::post('patientinsurance/{patient_id?}', 'Patients\PatientController@patientinsurance');

    Route::get('patientinsurance/{id}/delete/{p_id}', 'Patients\PatientController@insurancedelete');
    Route::get('patientauthorization/{id}/delete/{p_id}', 'Patients\PatientController@authorizationdelete');
    Route::get('patientcontact/{id}/delete/{p_id}', 'Patients\PatientController@contactdelete');
    Route::get('api/patientsreports/{export}/{all?}/{app?}',  'Patients\PatientController@getPatientExport');
    //Route::get('api/patientsreports/{export}', 'Patients\Api\PatientApiController@getIndexApi');
    // For patient list server script call
    Route::get('patients/patientsList/{args?}', 'Patients\PatientController@indexTableData');
    Route::get('patients/patientsList/{all?}/{app?}', 'Patients\PatientController@indexTableData');
    Route::get('patients/key_serach/{type?}', 'Patients\PatientController@indexSerachOption');
    // Correspondense satrts here
    Route::group(array('prefix' => 'patients/{id}'), function() {        
        Route::any('ledger/ajax/pagination', 'Patients\LedgerController@ajaxclaimlist');
        Route::post('ledger/claim/search', 'Patients\LedgerController@ajaxclaimlist');
        Route::get('ledger1', 'Patients\LedgerController@index1');
        Route::get('ledger2', 'Patients\LedgerController@index2');
        Route::resource('ledger', 'Patients\LedgerController');

        Route::get('/correspondence', 'Patients\CorrespondenceController@templateList');
        Route::get('/correspondence/{temp_id}/edit', 'Patients\CorrespondenceController@create');
        Route::post('/correspondence/{temp_id}/edit', 'Patients\CorrespondenceController@update');
        Route::post('/correspondence/send', 'Patients\CorrespondenceController@send');
        Route::get('/correspondencehistory', 'Patients\CorrespondenceController@index');
        Route::get('/correspondencehistory/{cor_id}', 'Patients\CorrespondenceController@show');

        Route::get('/patientstatements', 'PatientindividualstatementController@patientstatements');
        Route::get('/eligibilitytemplate', 'Patients\PatientEligibilityController@eligibilitytemplate');
        Route::resource('/reports', 'Patients\PatientReportController');
        Route::resource('/reports1', 'Patients\PatientReportController');
    });
    
    //Route::get('patients',    'Patients\PatientController@index');
    //Route::get('patients/{id}',   'Patients\PatientController@show');
    //Route::get('patients/{id}/edit',  'Patients\PatientController@edit');
    Route::get('appt-email-send/{id}', 'Patients\AppointmentController@appointmentMailSend');
    Route::get('patients/{id}/appointments', 'Patients\AppointmentController@index');
    Route::get('api/patientappointmentreports/{id}/{export}', 'Patients\AppointmentController@getAppointmentExport');//For Patient Appointment list export
    //Route::get('api/patientappointmentreports/{id}/{export}', 'Patients\Api\AppointmentApiController@getIndexApi');
    Route::post('patients/{id}/appointments/type', 'Patients\AppointmentController@searchIndexlist');
    
    
    //Route::get('api/problemlistreports/{id}/{export}', 'Patients\Api\ProblemListApiController@getIndexApi');
    Route::get('api/problemlistreports/{id}/{export}', 'Patients\ProblemListController@getProblemListExport');//For Workbench List Export
    Route::get('patients/eligibility', 'Patients\EligibilityController@index');
    
    Route::get('patient_eligibility', 'Patients\PatientController@patient_egbty');
    Route::get('patient_eligibility_show', 'Patients\PatientController@patient_egbty_show');

    //Provider Detail popup for patients
    Route::get('getproviderdetail/{value}/{type}', 'Patients\BillingController@getproviderdetail');
    Route::get('imosearch', 'Patients\BillingController@getImoSearch');
    Route::get('cptsearch/{search_key?}', 'Patients\BillingController@searchCpt');
    Route::get('getcptmodifier/{cpt_code?}/{year?}/{insuranceId?}', 'Patients\BillingController@getCptModifier');
    Route::get('getcmsform/{id}/{type?}', 'Charges\ChargeController@getcmsform1');
    ///*** Starts - Patients Url's ***///  
    
    ### Patient statment category details populate start ###
    Route::get('GetPatSmtCatDetails/{cat_id}', 'Patients\PatientController@getPatientStmtCategoryDetails');
    ### Patient statment category details populate ends ###

    Route::group(array('prefix' => 'patients'), function() {

        ### Start patient Employer Auto search####
        Route::any('/emp_serach/{key}', 'Patients\Api\PatientApiController@employerSearchApi');
        Route::any('/emp_result/{key}', 'Patients\Api\PatientApiController@employerAddressApi');
        ### END patient Employer Auto search####
        ### Starts - Registration ###
        Route::get('', 'Patients\PatientController@index');
        Route::get('/create', 'Patients\PatientController@create');
        Route::get('/GetAuthTokenPverifyPatient', 'Patients\PatientEligibilityController@GetAuthTokenPverifyPatient');
        Route::post('/store/{id?}', 'Patients\PatientController@store');
        Route::get('/{id}/delete', 'Patients\PatientController@destroy');
        Route::post('/insurance/checktypeid', 'Patients\PatientController@checkInsurancetype');

        Route::get('/{id}/edit/{tab?}/{more?}', 'Patients\PatientController@edit');
        Route::post('/{id}/edit/{tab?}', 'Patients\PatientController@updatePatientOtherTabs');
        Route::get('/delete/{patient_id}/{type}/{div_id}/{typeid?}', 'Patients\Api\PatientApiController@getDeletePatientApi');
        Route::get('/checkEligibility', 'Patients\Api\PatientEligibilityApiController@checkPatientEligibility');
        Route::get('/getEligibility', 'Patients\Api\PatientEligibilityApiController@getPatientEligibility');
        Route::get('/getEligibilityWaystar', 'Patients\Api\PatientEligibilityApiController@getPatientEligibilityWaystar');
        Route::get('/getEligibilityWaystarHistory', 'Patients\Api\PatientEligibilityApiController@getPatientEligibilityWaystarHistory');
        Route::get('/getEligibilityMoreInfo/{id}/{filename}', 'Patients\Api\PatientEligibilityApiController@getEligibilityMoreInfo');
        Route::get('/delete_patient_picture/{id}', 'Patients\PatientController@delete_patient_picture');
        Route::get('paymentsexport/{patient_id}/{tab}/{claim_id?}/{export}', 'Patients\PatientPaymentController@getPaymentExport');
        Route::get('/ajax_loading_demographics/{id}', 'Patients\PatientController@ajax_loading_demographics');

        Route::get('/checkpatientnote/{id}', 'Patients\Api\NotesApiController@getPatientNoteApi');
        Route::get('/checkinsurance/{patientid}/{insid}/{policyid}', 'Patients\Api\PatientApiController@checkInsuranceApi');

        Route::post('/contact_module', 'Patients\PatientController@ContactModuleProcess');
        Route::get('/getcontact_deatils/{contact_id}/{patient_id}', 'Patients\Api\PatientApiController@getPatientContactDeatilsApi');

        Route::post('/insurance_module', 'Patients\PatientController@insuranceModuleProcess');
        Route::post('/authorization_module', 'Patients\PatientController@authorizationModuleProcess');

        /// Add more
        Route::get('/addmore/{addmore_type}/{addmore_count}/{patient_id?}', 'Patients\PatientController@getAddMoreFields');
        Route::get('/contact/{category}/{count}', 'Patients\PatientController@getContactCategory');

        ### Ends - Registration ###

        Route::get('/{id}', 'Patients\LedgerController@index');

        ### Start - ClinicalNotes  ###
        Route::resource('/{id}/clinicalnotes', 'Patients\ClinicalNotesController');
        Route::get('/{pid}/clinicalnotes/{id}/claimdetail', 'Patients\ClinicalNotesController@claimdetails');
        Route::get('/{pat_id}/clinicalnotes/delete/{id}', 'Patients\ClinicalNotesController@destroy');
        Route::get('/{pat_id}/clinicalnotes/export/{export}', 'Patients\Api\ClinicalNotesApiController@getIndexApi');
        ### End - ClinicalNotes  ###

       // Route::get('/{id}/report', 'Patients\ReportsController@index');

        // Billing routes starts here     
        Route::resource('/{id}/billing', 'Patients\BillingController');
        Route::get('/{id}/billing/create/{claim_id?}', 'Patients\BillingController@create');
        Route::get('/{id}/billing/edit/{claim_id?}', 'Patients\BillingController@edit');
        // / Route::get('/billing/{id}/{edit}',  'Patients\BillingController@edit');
        Route::get('/billing/delete/{id}', 'Patients\BillingController@destroy');
        Route::post('billing', 'Patients\BillingController@store');
        Route::post('billing/update', 'Patients\BillingController@update');
        Route::get('{patient_id}/billing_authorization/{type?}', 'Patients\BillingController@popupauthorization');
        Route::post('billing_authorization/add_auth', 'Patients\BillingController@storeauthorization');
        Route::get('popuppayment/{id}/', 'Patients\BillingController@paymentdetail');
        Route::get('cms/{id}/', 'Patients\BillingController@cmsdetail');
        Route::get('{id}/chargesList', 'Patients\BillingController@indexTableData');
        Route::get('popupemployer/{patient_id}', 'Patients\BillingController@getPopupEmployer');

        Route::post('billing_employer/', 'Patients\BillingController@storepopupemployer');
        Route::get('referingprovider/{type}', 'Patients\BillingController@addprovider');
        Route::post('billing_provider/', 'Patients\BillingController@storepopupprovider');
        Route::get('addmoredosrow/{i}', 'Patients\BillingController@getaddmoredos');
        Route::get('getselectbasedvalues/{select_id}/{model}/{category?}/{patient_id?}', 'Patients\BillingController@getselectbasedvalues');
        //Provider Detail popup for patients
        Route::get('getproviderdetail/{value}/{type}', 'Patients\BillingController@getproviderdetail');
        Route::post('/{id}/charges/search', 'Patients\BillingController@index');
        Route::get('chargesexport/{patient_id}/{export}', 'Patients\BillingController@getBillingExport');

        Route::group(array('prefix' => 'claimdetail'), function() {
            Route::get('/create/{patient_id}', "Patients\ClaimDetailController@create");
            Route::post('/', "Patients\ClaimDetailController@store");
            Route::get('/{id}/edit', "Patients\ClaimDetailController@edit");
            Route::post('update/{id}', "Patients\ClaimDetailController@update");
        });

        Route::group(array('prefix' => 'claimbilling'), function() {
            Route::get('/create/{patient_id}', "Patients\ClaimAmbulanceBillingController@create");
            Route::post('/', "Patients\ClaimAmbulanceBillingController@store");
            Route::get('/{id}/edit', "Patients\ClaimAmbulanceBillingController@edit");
            Route::post('update/{id}', "Patients\ClaimAmbulanceBillingController@update");
        });

        Route::group(array('prefix' => 'claimotherdetail'), function() {
            Route::get('/create/{patient_id}', "Patients\ClaimOtherDetailController@create");
            Route::post('/', "Patients\ClaimOtherDetailController@store");
            Route::get('/{id}/edit', "Patients\ClaimOtherDetailController@edit");
            Route::post('update/{id}', "Patients\ClaimOtherDetailController@update");
        });
        // Billing routes ends here
        //patient Payment
        Route::resource('/{id}/patientpayment', 'Patients\PatientWalletHistoryController');
        Route::post('/{id}/patientpayment/{number}', 'Patients\PatientWalletHistoryController@show');
        Route::get('/{id}/patientpayment/export/{export}', 'Patients\PatientWalletHistoryController@paymentWalletExport');

        // CheckReturnController
        Route::resource('/{id}/returncheck', 'Patients\CheckReturnController');
        Route::get('/{id}/returncheck/{number}/delete', 'Patients\CheckReturnController@destroy');
        Route::get('/{id}/returncheck/export/{export}', 'Patients\Api\CheckReturnApiController@getIndexApi');
        //Route::get('/returncheck/export/{export}','Patients\Api\CheckReturnApiController@getIndexApi');
        // Eligibility starts here
        Route::resource('/{id}/eligibility', 'Patients\PatientEligibilityController');
        Route::get('/{id}/eligibility/create/{tempate_id}', 'Patients\PatientEligibilityController@create');
        Route::get('/{id}/eligibility/{eligibility_id}/delete', 'Patients\PatientEligibilityController@destroy');
        Route::get('/{id}/edi_eligibility', 'Patients\Api\PatientEligibilityApiController@getindexedi_eligibilityApi');
        Route::get('eligibility/showpdf/{type}/{id}', 'Patients\PatientEligibilityController@showpdf');
        Route::get('gettemplates/{id}', 'Patients\Api\PatientEligibilityApiController@gettemplates');
        Route::post('/{id}/edi/eligibility/verification', 'Patients\Api\PatientEligibilityApiController@storeEdiApi');
        // Eligibility Ends here
        //Superbill routes starts here
        Route::get('/{id}/superbill/create', 'Patients\SuperBillController@create');
        Route::post('/superbill/store', 'Patients\SuperBillController@store');
        //Superbill routes ends here  
        Route::resource('{id}/documents', 'Patients\PatientDocumentsController');
        Route::get('{id}/documentsummary', 'Patients\PatientDocumentsController@documentsummary');
        Route::post('/document/add/{id}', 'Patients\PatientDocumentsController@addDocument');
        Route::get('/documents/delete/{documentid}/{id}', 'Patients\PatientDocumentsController@destroy');
        Route::get('{id}/document/get/{type}/{filename}', 'Api\DocumentApiController@getdocumentmodalApi');
        Route::get('/{id}/document/create', 'Patients\PatientDocumentsController@create');
        
        // Selvakumar added url for document upload
        
        Route::get('/show_document_listing/{id}', 'Patients\PatientDocumentsController@show_document_listing');
        Route::get('{patient_id}/document-assigned/{id}/show', 'Patients\PatientDocumentsController@document_assigned_show');
        Route::post('document-assigned/{id}/store', 'Patients\PatientDocumentsController@document_assigned_store');

        //Route::get('documentmodal/get/{id}/{type}/{filename}', 'Api\DocumentApiController@getdocumentmodalApi');
        Route::resource('{id}/notes', 'Patients\PatientNotesController');
        Route::get('/{id}/notes/delete/{noteid}', 'Patients\PatientNotesController@deleteNotes');
        Route::post('/notes/status', 'Patients\PatientNotesController@statusNotes');

        // Patient Budget Plan starts
        Route::resource('{id}/budgetplan', 'Patients\PatientBudgetController');
        Route::get('/{id}/budgetplan/{budgetid}/delete', 'Patients\PatientBudgetController@deletebudget');
        
        // Patient Statements

        // Patient Medical History starts
        Route::get('/{id}/medicalhistory', 'Patients\PatientController@getquestionnaires');
        Route::get('api/questionnairesreport/{id}/{export}', 'Patients\Api\PatientApiController@getQuestionnairesApi');

        // Patient archiveinsurance starts
        Route::get('/{id}/archiveinsurance', 'Patients\PatientController@getarchiveinsurance');
        Route::get('/{id}/movearchiveinsurance/{arcid}', 'Patients\PatientController@getMoveArcInsuranceForm');
        //Route::get('/{id}/archiveinsurance/move/insurance/{arc_id}', 'Patients\PatientController@moveArchchivetoInsurance');
        //Route::get('api/archiveinsurancereport/{id}/{export}', 'Patients\Api\PatientApiController@getarchiveinsuranceApi');
        Route::get('api/archiveinsurancereport/{id}/{export}', 'Patients\PatientController@archiveInsuranceExport');

        //patient status change via function.js
        Route::get('/status/{id}/{status_value}', 'Patients\PatientController@changeStatus');

        //Patient data import from xls
        Route::get('/imports/xls-import', 'Patients\PatientController@import_xls_data');

        //Patient inside Payment Routes starts here
        Route::group(array('prefix' => '{id}/payments'), function() {
            Route::get('/', 'Patients\PatientPaymentController@index');            
            Route::get('edit', 'Patients\PatientPaymentController@create');
            Route::get('edit01', 'Patients\PatientPaymentController@create1');
            Route::post('search', 'Patients\PatientPaymentController@searchPayment');
            Route::post('insurancecreate', 'Patients\InsurancePaymentController@create');
            Route::post('insurancepost', 'Patients\InsurancePaymentController@store');
            Route::post('addtowallet/{type?}', 'Patients\PatientPaymentController@addamounttowallet');
            Route::post('create', 'Patients\PatientPaymentController@create');
            Route::post('insurancecreate', 'Patients\InsurancePaymentController@create');
            Route::get('create', 'Patients\InsurancePaymentController@create');
            Route::get('create', 'Patients\PatientPaymentController@create');
            Route::get('patientnotes', 'Patients\PatientPaymentController@addPatientNote');                
        });

        Route::get('{id}/paymentinsurance/{tab}/{claim_id?}', 'Patients\PatientPaymentController@index');
        Route::post('payments', 'Patients\PatientPaymentController@store');
        
        Route::get('payments/getclaimtransaction/{claim_id}', 'Patients\Api\PaymentApiController@listClaimTransactionHistories');
        /* Route::get('{id}/payments/insurancecreate', function($id){ // Payment create should be done with post so here restricted get method as because need to provide some data for payment posting
          return Redirect::to('patients/'.$id.'/payments');
          }); */
        Route::get('{id}/paymentList', 'Patients\PatientPaymentController@indexTableData');
        Route::get('{id}/payments/insurancecreate', 'Patients\InsurancePaymentController@create');
        Route::get('payment/popuppayment/{claim_id}/{type?}', 'Patients\PatientPaymentController@getpopuppaymentdata');
        #Export paymentpopup transaction
        Route::get('payment/popuppayment/{claim_id}/{type?}/{export}', 'Patients\PatientPaymentController@getPopupPaymentDataExport');

        //Patient inside Routes ends here   
        //ProblemList Routes starts here
        Route::get('{id}/problemlist', 'Patients\ProblemListController@index');
        Route::get('{id}/problemlistAjax', 'Patients\ProblemListController@indexTableData');
        
        Route::get('{id}/ajaxupdate/problemlist', 'Patients\ProblemListController@ajaxUpdate');
        Route::get('{patient_id}/problem/{claimid}/show', 'Patients\ProblemListController@show');
        Route::get('/autocomplete/claim_lists', 'Patients\ProblemListController@claim_lists');
        Route::get('{patient_id}/problem/create', 'Patients\ProblemListController@create');
        Route::post('{patient_id}/problem/store/{claim_id}', 'Patients\ProblemListController@problemstore');
        Route::post('{patient_id}/problem/createstore/{claim_id}', 'Patients\ProblemListController@problemcreatestore');
        Route::post('{id}/problemlist/type', 'Patients\ProblemListController@filteroption');

        //AR Management Routes starts here
        Route::group(array('prefix' => '{id}/armanagement'), function() {
            Route::get('armanagement', 'Patients\ArManagementController@index');
            Route::get('arsummary', 'Patients\ArManagementController@arsummary');
            Route::get('list', 'Patients\ArManagementController@lists');
            Route::get('followup/list', 'Patients\ArManagementController@followup_list');
            Route::post('list', 'Patients\ArManagementController@lists');
            Route::get('view1', 'Patients\ArManagementController@view');
            Route::get('chargepayment', 'Patients\ArManagementController@create');
        });

        Route::group(array('prefix' => 'armanagement'), function() {
            Route::post('/getclaimtabdetails', 'Patients\ArManagementController@getclaimtabdetails');
            Route::post('/changeClaimResponsibility', 'Patients\ArManagementController@changeTheClaimResponsibility');
            Route::post('/getclaimnotesadded', 'Patients\ArManagementController@getclaimnotesadded');
            Route::post('/getaddedfollowupdetails', 'Patients\ArManagementController@getaddedfollowupdetails');
            Route::post('/getclaimdenailnotesadded', 'Patients\ArManagementController@getclaimdenailnotesadded');
            Route::post('/getclaimassignadded', 'Patients\ArManagementController@getclaimassignadded');
            Route::post('/getclaimstatusnotesadded', 'Patients\ArManagementController@getclaimstatusnotesadded');
            Route::post('/getclaimstatusfinalnotesadded', 'Patients\ArManagementController@getclaimstatusfinalnotesadded');
            Route::post('/getclaimchargeeditprocess', 'Patients\ArManagementController@getclaimchargeeditprocess');
            Route::get('/getclaimchargeeditdetails/{claim_id}', 'Patients\ArManagementController@getclaimchargeeditdetails');
            Route::get('/getclaimpatientinsurance/{patient_id}', 'Patients\ArManagementController@getclaimpatientinsurance');
            Route::get('/getpatientinsurance/{patient_id}', 'Patients\ArManagementController@getpatientinsurance');
            Route::get('/setpatienthold/{patient_id}', 'Patients\ArManagementController@setpatienthold');
            Route::get('/setpatientunhold/{patient_id}', 'Patients\ArManagementController@setpatientunhold');
            Route::post('/getdenialsearchlist', 'Patients\ArManagementController@getdenialsearchlist');
            Route::post('/claimholdprocess', 'Patients\ArManagementController@claimholdprocess');
        });

        //Wallet Routes starts here
        Route::get('{id}/wallet', 'Patients\WalletController@index');
        Route::get('{id}/wallet_transaction', 'Patients\WalletController@lists');
        Route::get('{id}/view', 'Patients\WalletController@view');
        
        /* Armanagement followup history ajax url */
        Route::get('armanagement/followup/history/{claim_no}', 'Patients\ArManagementController@getFolloupHistoryPopup');
        /* Armanagement followup history ajax url */
        
    });
    
    /* Patient Upload  */
    Route::get('/uploadedpatients', 'Patients\PatientController@getUploadedPatient');
    Route::get('/getUploadedPatientAjax', 'Patients\PatientController@getUploadedPatientAjax');
    Route::get('/downloaduploadedfile/{id}', 'Patients\PatientController@getUploadedFile');
    Route::get('/downloaduploadedrespfile/{id}', 'Patients\PatientController@getUploadedResponseFile');
    Route::post('/uploaded_patient', 'Patients\PatientController@uploaded_patient');
    Route::get('/processupload/{id}', 'Patients\PatientController@processUploadedFile');
    Route::get('/getUploadStatus', 'Patients\PatientController@getUploadStatus');
    Route::any('/downloadTemplate', 'Patients\PatientController@getDownloadTemplateFile');
    /* Patient Upload  */

    Route::get('getremarkcode/{id}', 'Payments\Api\PaymentApiController@getremarkcodeApi');
    Route::get('getcms/{id}/{type?}', 'Patients\Api\BillingApiController@getcms1500Api');
    //Route::get('getcms/{id}/{type?}', 'Patients\BillingController@getcms1500');
    Route::get('checkcptexist/{cpt_hcpcs}', 'Patients\Api\BillingApiController@checkCPTexistApi');

    Route::group(array('prefix' => 'charges'), function() {
        Route::get('/chargesList/{status?}', 'Charges\ChargeController@indexTableData');
        Route::get('/create/{id}', 'Patients\BillingController@create');
        Route::get('/create', 'Charges\ChargeController@create');
        Route::get('/newcharge/{type?}', 'Charges\ChargeController@create');
        //Route::get('/create_batch/{id}/{type}','Charges\ChargeController@create'); 
        Route::get('/{claim_id}/edit/', 'Charges\ChargeController@edit');
        Route::get('/{claim_id}/charge_edit/{value?}', 'Charges\ChargeController@edit');
        Route::post('/update', 'Charges\ChargeController@update');
        Route::post('/', 'Charges\ChargeController@store');        // Update existing record
        Route::get('/searchpatient/{type}/{key}', 'Charges\ChargeController@searchpatient');
        Route::get('/{status?}', 'Charges\ChargeController@index');   //Removed batch related routes on 12 Jul 2016       
        Route::get('/delete/{id}', 'Charges\ChargeController@destroy');
        Route::post('/batch/create', 'Charges\ChargeController@saveprovider');
        Route::post('/search', 'Charges\ChargeController@index');
        Route::get('chargesexport/{export}', 'Charges\ChargeController@chargesExport');
    });

    // Get modifier while loading
    Route::get('getmodifier', 'Patients\Api\BillingApiController@getmodifier');

    // Payment posting routes starts here

    //Route::resource('payments', 'Payments\PaymentController');
    Route::group(array('prefix' => 'payments'), function() {
        Route::get('/', 'Payments\PaymentController@index');
        Route::get('paymentsList', 'Payments\PaymentController@indexTableData');
        Route::get('/search', 'Payments\PaymentController@searchcheck');
        Route::get('/file_response', 'Payments\PaymentController@file_response');
        // Route::resource('payments',    'Payments\PaymentController');
        Route::get('/searchclaims/{insurace_id}/{patient_id}', 'Payments\PaymentController@searchclaim');
        Route::post('/insurancecreate', 'Payments\PaymentController@create');
        Route::get('/insurancecreate', 'Payments\PaymentController@create');
        Route::get('/get-e-remittance', 'Payments\PaymentController@download_e_remittance');
        Route::get('/manual-download-e-remittance', 'Payments\PaymentController@manual_download_e_remittance');
        Route::get('/pdf-generation/{id?}', 'Payments\PaymentController@pdf_generation');
        Route::post('/auto-post', 'Payments\PaymentController@auto_post');
        Route::get('/pdf-generation/{id?}/{cheque?}', 'Payments\PaymentController@pdf_generation');
        Route::get('erapopup/{id?}/{cheque?}', 'Payments\PatientPaymentController@geterapopup');
         // payment listing server script 

        Route::get('paymentinsurance/{tab}/{payment_detail_id?}', 'Patients\PatientPaymentController@getPaymentpopup'); // To reopn check the same payment id has been passed

        Route::get('getpaymentdata/{id}', 'Payments\PaymentController@getPaymentdetail');
        Route::get('/searchpatient/{type}/{value}/{paymenttype?}/{status?}', 'Payments\PaymentController@searchpatient');
        Route::post('/search', 'Payments\PaymentController@searchCheckInfo');
        Route::post('/insurancepost', 'Payments\PaymentController@store');
        Route::get('/paymentadd/{payment_id}', 'Payments\PaymentController@paymentadd');
        Route::post('/create', 'Payments\PatientPaymentController@create'); //For patient payment links at Main payment posting 
        Route::get('/create', 'Payments\PatientPaymentController@create'); //For patient payment links at Main payment posting 
        Route::post('/patientpost', 'Payments\PatientPaymentController@store'); //For patient payment links at Main payment posting    
        Route::get('/editcheck/{id}', 'Payments\PaymentController@editcheck'); // To edit payment check data information.
        Route::post('/editcheck', 'Payments\PaymentController@posteditcheck'); // To edit payment check data information.
        Route::get('/checkexist/{type}/{checkno}/{check_type?}/{patient_id?}', 'Payments\Api\PaymentApiController@checkexistApi'); // To edit payment check data information.
        Route::post('/paymentinfo', 'Payments\Api\PaymentApiController@savePaymentdataonCancelApi');
        Route::get('/armanagement/{claim_id}/{type?}', 'Payments\PaymentController@editpayment');
        Route::get('/searchpatient/{patient_id}', 'Payments\PatientPaymentController@listPatientInsurance');
        Route::get('/getclaimdata/{claim_id}', 'Payments\PaymentController@getClaimdata');
        Route::get('/getpaidclaimdata/{payment_id}', 'Payments\PatientPaymentController@getPaymentcheckdata');
        Route::get('/delete/{id}/{type?}', 'Payments\PaymentController@delete');
        Route::get('/voidpatientpayment/{payment_id}', 'Payments\PatientPaymentController@voidPaymentcheckdata');
        Route::post('/checkexist', 'Payments\Api\PaymentApiController@checkexistMoneyApi');    
        // Payment posting routes ends here
        
        Route::post('/noteAdd', 'Payments\Api\PaymentApiController@noteAdd');
        Route::get('paymentsexport/{export}', 'Payments\PaymentController@paymentsExport');
		Route::get('paymentsE-remittance/{export}', 'Payments\PaymentController@export_e_remittance');    
		Route::get('updateArchiveStatus', 'Payments\PaymentController@updateArchiveStatus');    
    });


    Route::get('clearing-house-response', 'Payments\PaymentController@clearing_house_response');
    Route::get('clearing-house-edi-response', 'Payments\PaymentController@clearing_house_edi_response');
    Route::get('download-response-file/{name}', 'Payments\PaymentController@download_response_file');
    Route::get('get_practice_session_id', 'Payments\PaymentController@get_practice_session_id');
    Route::get('/analytics/claims', 'Claims\ClaimControllerV1@summary');
    Route::get('{id}/document/get/{type}/{filename}', 'Api\DocumentApiController@getdocumentmodalApi');
    ///*** Starts - Claims Url's ***///
    // For claims list server script call
    Route::get('claims/claimsList/{type?}', 'Claims\ClaimControllerV1@indexTableData');
    Route::group(array('prefix' => 'claims'), function() {
        
        /* ClaimControllerV1 Url Start */        
        Route::get('/', 'Claims\ClaimControllerV1@summary');
        
        Route::any('/initialediscrubbing', 'Claims\ClaimControllerV1@checkAndSubmitEdiClaim');
        Route::get('/initialpaperscrubbing', 'Claims\ClaimControllerV1@checkAndSubmitPaperClaim');
        Route::get('/pendingclaims', 'Claims\ClaimControllerV1@updatePendingClaims');
        Route::get('/paperclaims', 'Claims\ClaimControllerV1@updatePaperClaims');
        Route::get('/electronicclaims', 'Claims\ClaimControllerV1@updateElectronicClaims');
        Route::get('/holdreason', 'Claims\ClaimControllerV1@getHoldReason');
        Route::post('/updateholdclaims', 'Claims\ClaimControllerV1@postHoldClaims');
        Route::get('/transmission', 'Claims\ClaimControllerV1@listClaimTransmission');
        Route::get('/transmission/{id}', 'Claims\ClaimControllerV1@viewClaimTransmission');
        Route::get('/download/{type}/{id}', 'Claims\ClaimControllerV1@downloadClaim837And835Api');
        Route::get('/edireports', 'Claims\ClaimControllerV1@getEdiReports');
        Route::get('/edi_report/{id}/show', 'Claims\ClaimControllerV1@viewEdiReport');
        Route::get('/status_edireports', 'Claims\ClaimControllerV1@getStatusEdiReports');
        Route::post('/getedireporttabdetails', 'Claims\ClaimControllerV1@getedireporttabdetails');
        Route::get('/generateedireports', 'Claims\ClaimControllerV1@generateEdiReports');
        Route::get('/getresponsefile/{id}', 'Claims\ClaimControllerV1@getresponsefile');
        /* selvakumar added this routes */
        Route::get('/status/{type?}','Claims\ClaimControllerV1@claims_data');
        Route::post('/search/{type?}','Claims\ClaimControllerV1@ClaimsDataSearch');
        Route::get('/search/{claim}/export/{type?}','Claims\ClaimControllerV1@ClaimsDataSearchExport');
        Route::get('/autocomplete/{type?}','Claims\ClaimControllerV1@autocomplete');
        Route::get('/generate/search/{page_name?}','Claims\ClaimControllerV1@generateSearch');
        Route::get('/generate/searchSavedData/{page_name?}/{search_id?}','Claims\ClaimControllerV1@searchSavedData');
        /* selvakumar added this routes */
        Route::post('/store/search-data','Claims\ClaimControllerV1@searchData');
        Route::post('/store/search-data-remove','Claims\ClaimControllerV1@searchDataRemove');
        
        Route::get('/updateInsuranceCategory','Claims\ClaimControllerV1@updateInsuranceCategory');
        
        Route::any('/errorSubmission/{id}','Claims\ClaimControllerV1@errorClaimSubmission');
        /* ClaimControllerV1 Url End */
        
        // Armanagement dynamic checkbox selection  
        // Revision 1 : MR-2716 : 22 Aug 2019 : Selva 
        Route::any('/changeClaimStatus','Claims\ClaimControllerV1@changeClaimStatus');
        
        /* ClaimAPIControllerV1 Url Start */        
        Route::get('/downloadclaims/{claim_ids}/{type?}', 'Claims\Api\ClaimApiControllerV1@downloadCMS');
        Route::get('/printclaims/{claim_ids}/{type?}', 'Claims\Api\ClaimApiControllerV1@printCMS');
        Route::get('/{tosubmit}/export/{type}', 'Claims\Api\ClaimApiControllerV1@getIndexApi');
        Route::get('/transmission/search/export/{type}', 'Claims\Api\ClaimApiControllerV1@listClaimTransmissionApi');

        Route::any('/ediInitiate', 'Claims\ClaimControllerV1@submitEDIclaims');
		
		// Generating  missing files in era
        Route::get('/erafiles', 'Claims\ClaimControllerV1@erafiles');
        /* ClaimAPIControllerV1 Url End */  


		/* claims clearing house file data  */
		Route::get('/clearing_data', 'Claims\ClaimControllerV1@clearingHouseData');
		
    });
    ///*** Ends - Claims Url's ***///

    ///*** Starts - Practice Url's ***///
    ### Starts - Practice ###
    Route::get('practice/{id}/delete/{picture_name}', 'PracticesController@avatar_picture');

    Route::resource('practice', 'PracticesController');

    #Practice -> Overrides
    //Route::resource('overrides', 'PracticeOverridesController');  //Gopal
    // Route::get('overrides/delete/{id}', 'PracticeOverridesController@destroy');
    #Practice -> Managed care
    Route::resource('managecare', 'PracticeManagecareController');
    Route::get('managecare/delete/{id}', 'PracticeManagecareController@destroy');
    Route::get('api/practicereports/{export}', 'PracticeManagecareController@practiceManagedCareExport');

    #Practice -> Contact Details
    Route::resource('contactdetail', 'ContactdetailController');

    #Practice -> Document
    Route::resource('document', 'PracticeDocumentsController');

    Route::group(array('prefix' => 'document'), function() {
        Route::post('addDocument', 'PracticeDocumentsController@addDocument');
        Route::get('get/{filename}', ['as' => 'picture', 'uses' => 'PracticeDocumentsController@get']);
        Route::get('delete/{id}', 'PracticeDocumentsController@destroy');
    });


    #Practice -> Notes
    Route::resource('notes', 'PracticeNotesController');
    Route::get('notes/delete/{id}', 'PracticeNotesController@deleteNotes');


    #Practice -> USER Activity Starts###
    Route::resource('usersactivity', 'UsersactivityController');
    Route::resource('patientslog', 'PatientslogController');
    
    ### Ends - Practice ###
    ### Starts - Facility ###
    Route::resource('facility', 'FacilityController');
    Route::get('api/facilityreports/{export}', 'FacilityController@getFacilityExport');

    Route::group(array('prefix' => 'facility'), function() {
		
		#Facility -> Notes
        Route::resource('/{id}/notes', 'FacilityNotesController');
        Route::get('/{facility_id}/notes/delete/{id}', 'FacilityNotesController@deleteNotes');
        ### Ends - Facility ###

        #Facility -> Managed care
        Route::resource('/{id}/facilitymanagecare', 'FacilityManagecareController');
        Route::get('/{facility_id}/facilitymanagecare/delete/{managecare_id}', 'FacilityManagecareController@destroy');
		Route::get('api/facilitymanagecarereports/{id}/{export}', 'FacilityManagecareController@facilityManagedCareExport');
		
        Route::get('/{id}/delete/{picture_name}', 'FacilityController@avatarfacility');
        Route::post('/searchfilter', 'FacilityController@index');
        Route::post('/{faci_id}/{select_time}', 'Api\FacilityApiController@appointmentcheck');
        Route::get('/{facility_id}/delete', 'FacilityController@destroy');

        #Facility -> Document
        Route::resource('/{id}/facilitydocument', 'FacilityDocumentsController');
        Route::post('/{id}/facilitydocument/addDocument', 'FacilityDocumentsController@addDocument');
        Route::get('/{id}/facilitydocument/get/{filename}', ['as' => 'picture', 'uses' => 'FacilityDocumentsController@get']);
        Route::get('/{id}/facilitydocument/delete/{img_id}', 'FacilityDocumentsController@destroy');
    });

    // Facility Overrides
    /* Route::resource('facility/{id}/facilityoverrides','FacilityOverridesController');
      Route::get('facility/{facility_id}/facilityoverrides/delete/{overrides_id}', 'FacilityOverridesController@destroy'); */

    ### Starts - Provider ###
    Route::resource('provider', 'ProviderController');
    Route::get('api/providerreports/{export}', 'ProviderController@providerExport');
    Route::get('trail/provider/create', 'ProviderController@trailProviderCreate');
    Route::post('trail/provider/store', 'ProviderController@trailProviderStore');
    
    Route::group(array('prefix' => 'provider'), function() {
        #Provider -> Overrides
        Route::resource('/{provider_id}/provideroverrides', 'ProviderOverridesController');
        Route::get('/{provider_id}/provideroverrides/{id}/delete', 'ProviderOverridesController@destroy');

        #Provider -> Managed Care
        Route::resource('/{provider_id}/providermanagecare', 'ProviderManagecareController');
        Route::get('/{provider_id}/providermanagecare/{id}/delete', 'ProviderManagecareController@destroy');
		Route::get('api/providermanagecarereports/{id}/{export}', 'ProviderManagecareController@providerManagedCareExport');

        #Provider -> Document
        Route::resource('/{provider_id}/providerdocuments', 'ProviderDocumentsController');
        Route::post('/{provider_id}/providerdocuments/addDocument', 'ProviderDocumentsController@addDocument');
        Route::get('/{provider_id}/providerdocuments/get/{filename}', ['as' => 'picture', 'uses' => 'ProviderDocumentsController@get']);
        Route::get('/{provider_id}/providerdocuments/{id}/delete', 'ProviderDocumentsController@destroy');

        #Provider -> Notes
        Route::resource('/{id}/notes', 'ProviderNotesController');
        Route::get('/{provider_id}/notes/delete/{id}', 'ProviderNotesController@deleteNotes');

        Route::get('/{id}/delete/{picture_name}', 'ProviderController@avatarprovider');
        Route::get('/{provider_id}/delete', 'ProviderController@destroy');
        Route::post('/search', 'ProviderController@searchIndexlist');
    });
    ### Ends - Provider ###
    ### Starts - Resources ###
    Route::resource('resources', 'ResourcesController');
    Route::get('resources/delete/{resources_id}', 'ResourcesController@destroy');
    ### Ends - Resources ###
    ### Starts - Insurance ###
    #Insurance Optum
    Route::get('insurance/{id}/delete/{picture_name}', 'InsuranceController@avatarinsurance');
    Route::resource('insurance', 'InsuranceController');
    Route::get('insurance/delete/{id}', 'InsuranceController@destroy');

    #Insurance Optum Overrides
    Route::resource('insurance/{insurance_id}/insuranceoverrides', 'InsuranceOverridesController');
    Route::get('insurance/{insuranceid}/insuranceoverrides/delete/{id}', 'InsuranceOverridesController@destroy');
     //Unique validation check for Insurance 
    Route::get('shortname/ins_short_val/{shortname?}', 'Api\InsuranceApiController@insuranceUnique');
    #Insurance Master
    Route::resource('insurance', 'InsuranceController');
    Route::get('get_insurancelist/{name?}/{search_category}', 'InsuranceController@GetInsuranceList');
    Route::get('implement_insurance/{id}', 'InsuranceController@GetInsuranceDetails');
    Route::get('insurance/delete/{id}', 'InsuranceController@destroy');
    Route::get('api/insurancereports/{export}', 'InsuranceController@getInsuranceExport');
   
    #Insurance Master-> Appeal Address
    Route::resource('insurance/{insurance_id}/insuranceappealaddress', 'InsuranceAppealAddressController');
    Route::get('insurance/{insurance_id}/insuranceappealaddress/delete/{id}', 'InsuranceAppealAddressController@destroy');
	#Export
	Route::get('api/insuranceappealaddress/{id}/{export}', 'InsuranceAppealAddressController@appealAddressExport');

    #Insurance Master -> Overrides
    Route::resource('insurance/{insurance_id}/insuranceoverrides', 'InsuranceOverridesController');
    Route::get('insurance/{insuranceid}/insuranceoverrides/delete/{id}', 'InsuranceOverridesController@destroy');
	
    ### Ends - Insurance ###
    #Stats slection change 
    Route::get('stats/listchange/{data}', 'StatsController@SelectlistChange');
    ### Ends - slection change API ###
    ### Starts - CPT ###
    Route::resource('cpt', 'CptController');
    Route::get('getmastercpt', 'CptController@importMasterCpt');
    Route::get('cpt/{cpt_id}/delete', 'CptController@destroy');
    Route::get('searchcpt', 'CptController@searchIndex');
    Route::get('yearInsurance/{year?}', 'CptController@getyearInsurance');
    Route::post('multiFeeScheduleData', 'CptController@multiFeeScheduleData');
    #Export
    Route::get('api/cptreports/{export}', 'CptController@getCptMasterExport');
    #CPT -> Favourite
    Route::get('listfavourites', 'CptController@listFavourites');
    Route::any('cptimport', 'CptController@cptImport'); //
    Route::post('cptupdate', 'CptController@cptUpdate'); //
    #Export
    Route::get('api/cptfavouritereports/{export}', 'CptController@getCptFavoritesExport');
    #Procedure Category
    Route::resource('procedurecategory','ProcedureCategoryController');//
    Route::get('api/procedurecategory/{export}', 'ProcedureCategoryController@getProcedureCategoryExport');
    // Route::post('procedurecategory/create','CptController@addProcedure');//
    // Route::get('procedurecategory/create','CptController@addProcedure');//
    // Route::get('samplecpt/{type}', 'CptController@get_SampleCpt_file'); //
    Route::get('togglecptfavourites/{id}', 'CptController@toggleFavourites');
    ### Ends - CPT ###
    ### Starts - ICD ###    
    // ICD-10
    Route::resource('icd', 'IcdController');
    Route::get('getmastericd', 'IcdController@importMasterIcd');
    Route::get('icd/{icd_id}/delete', 'IcdController@destroy');
    Route::get('api/icdreports/{export}', 'IcdController@getIcdExport');

    //IMO Search
    Route::get('searchicd', 'IcdController@searchIndex');
    Route::post('advanced/keywordsearch', 'AdvancedSearchIcdCptController@AdvancedSearchIcdCpt');
    
    
    ### Ends - ICD ###
    ### Starts - EDI ###
    Route::resource('edi', 'ClearingHouseController');
    Route::get('edi/delete/{id}', 'ClearingHouseController@destroy');

    ### Ends - EDI ###
    
    
    ### Start - Followup Create Category ###
    Route::group(array('prefix' => 'followup'), function() {
        Route::get('/category', 'FollowupController@index');
        Route::get('/create-category', 'FollowupController@create_category');
        Route::post('/store/category', 'FollowupController@store_category');
        Route::post('/edit/category', 'FollowupController@edit_category');
        Route::get('/view/category/{id}', 'FollowupController@view_category');
        Route::get('/question', 'FollowupController@question');
        Route::get('/create-question', 'FollowupController@create_question');
        Route::post('/store/question', 'FollowupController@store_question');
        Route::get('/view/question/{id}', 'FollowupController@view_question');
        Route::post('/edit/question', 'FollowupController@edit_question');
        Route::get('/category/question/{id}', 'FollowupController@category_question');
    });
    ### End - Followup Create Category ###
    ### Starts - Modifiers ###
    
    Route::get('getmastermodifier', 'ModifierController@importMasterModifiers');
    // Modifiers level 1
    Route::resource('modifierlevel1', 'ModifierController');
    Route::get('modifierlevel1/delete/{id}', 'ModifierController@destroy');
    Route::post('modifierlevel1/search', 'ModifierController@searchIndexlist');
    Route::get('api/modifierreports/{export}', 'ModifierController@getModifierExport');

    // Modifiers level 2
    Route::resource('modifierlevel2', 'ModifierLevelController');
    Route::get('modifierlevel2/delete/{id}', 'ModifierLevelController@destroy');
    Route::post('modifierlevel2/search', 'ModifierLevelController@searchIndexlist');
    Route::get('api/modifierlevelreports/{export}', 'ModifierLevelController@getModifierExport');
    ### Ends - Modifiers ###
    ### Starts - Employers ###
    Route::get('employer/{id}/delete/{pic_name}', 'EmployerController@empAvatarDelete');
    Route::resource('employer', 'EmployerController');
    Route::get('employer/delete/{id}', 'EmployerController@destroy');
    Route::get('api/employerreports/{export}', 'EmployerController@getEmployerExport');

    // Employer Notes
    Route::resource('employer/{id}/notes', 'EmployerNotesController');
    Route::get('employer/{provider_id}/notes/delete/{id}', 'EmployerNotesController@deleteNotes');
    ### Ends - Employers ###    
    ### Starts - Codes ###
    Route::get('getmastercode', 'CodeController@importMasterCodes');
    Route::resource('code', 'CodeController');
    Route::get('code/delete/{code_id}', 'CodeController@destroy');
    Route::post('code/search', 'CodeController@searchIndexlist');
    Route::get('api/codereports/{export}', 'CodeController@getCodeExport');
    ### Ends - Codes ###        
    ### Starts - Templates ###
    Route::resource('templates', 'TemplatesController');

    Route::resource('apptemplate', 'AppTemplatesController');
    Route::get('api/templatereports/{type}/{export}', 'AppTemplatesController@getTemplatesExport');

    /// Template Category Dropdown ///
    Route::group(array('prefix' => 'templates'), function() {
        Route::any('/addnewselect', 'TemplatesController@addnewselect');
        Route::post('/sample_post/{id}', 'TemplatesController@sample_post');
        Route::get('/delete/{id}', 'TemplatesController@destroy');
    });
    Route::get('template/category/{id}', 'TemplatesController@categoryDropdown');
    // Templates types
    Route::resource('templatetypes', 'TemplateTypesController');
    Route::get('templatetypes/delete/{id}', 'TemplateTypesController@destroy');
    ### Ends - Templates ###
    ### Starts - Call History ### 
    Route::resource('comhistory', 'CommunicationHistoryController');
    ### Ends - Call History ### 
    ### Starts - Fee Schedule ###
    Route::resource('feeschedule', 'FeescheduleController');

    Route::group(array('prefix' => 'feeschedule'), function() {
        Route::get('/{feeschedule_id}/delete', 'FeescheduleController@destroy');
        Route::get('/delete/{id}', 'FeescheduleController@destroy');
        Route::get('/statusChange/{id}/{status}', 'FeescheduleController@statusChange');
    });
    Route::get('feeschedule_file/{type}', 'FeescheduleController@get_feeschedule_file'); // Fee schedule upload sample xls file
    ### Ends - Fee Schedule ###
    ### Starts - Help ###
    Route::resource('staticpage', 'StaticPageController');
    Route::get('staticpage/delete/{staticpage_id}', 'StaticPageController@destroy');
    // Common help page
    Route::get('help/{type}', 'StaticPageController@getHelpContent');
    ### Ends - Fee Help ###
    ### Starts - Patient Statement Settings ###
    Route::resource('patientstatementsettings', 'PatientstatementsettingsController');
    Route::get('patientstatement/getaddress', 'PatientstatementsettingsController@getaddress');

    
    Route::get('bulkstatement/statementList/{args?}', 'PatientbulkstatementController@indexTableData');

    Route::resource('bulkstatement', 'PatientbulkstatementController');
    Route::get('individualstatement', 'PatientindividualstatementController@index');
    //Route::get('patientstatements', 'PatientindividualstatementController@patientstatements');
    Route::post('statementhistoryfilter', 'PatientindividualstatementController@getStatementHistory');
    Route::get('statementhistory/{patientid?}', 'PatientindividualstatementController@getStatementHistory');
    Route::get('individualpatientlist/{get_name}', 'PatientindividualstatementController@getPatientList');
    Route::get('individualstatementtype/{patientid}/{type}', 'PatientindividualstatementController@getindividualtype');
    Route::get('individualstatementdownload/{filename}/{id}/{existname}', 'PatientindividualstatementController@getindividualdownload');
    Route::get('individualpatientdetails/{patientid}', 'PatientindividualstatementController@getPatientDetails');
    ### Ends - Patient Statement ###
    ### Starts - API Setting ###
    Route::resource('apisettings', 'ApiSettingsController');
    ### Ends - API Setting ###
    ### Starts - API Setting ###
    Route::resource('userapisettings', 'UserApiSettingsController');
    Route::resource('apistatus', 'ApiSettingsController@getApiList');
    ### Ends - API Setting ###
    ### Starts - Cheat Sheet ###
    Route::resource('cheatsheet', 'CheatsheetController');
    Route::get('cheatsheet/delete/{cheatsheet_id}', 'CheatsheetController@destroy');
    ### Starts - Cheat Sheet ###
    Route::resource('sentfax', 'SentFaxController');
    Route::get('callandmessage/{phonenumber}', 'TwilioController@callandmessage');

    ### Starts - Scheduler settings ###
    Route::resource('practiceproviderschedulerlist', 'ProviderSchedulerController');
    Route::post('practiceproviderschedulerlist', 'ProviderSchedulerController@index');
    Route::get('practicescheduler/provider/{provider_id}', 'ProviderSchedulerController@viewProviderScheduler');
    Route::get('addproviderscheduler/{provider_id}/{scheduler_id?}', 'ProviderSchedulerController@addProviderScheduler');
    Route::get('practicescheduler/provider/{provider_id}/delete/{id}', 'ProviderSchedulerController@destroy');
    Route::get('practiceproviderscheduler/{provider_id}/{scheduler_id}', 'ProviderSchedulerController@viewProviderSchedulerDetailsById');
	#Export
    Route::get('api/schproviderreports/{export}', 'ProviderSchedulerController@getProviderSchedulerExport');
	Route::get('api/schproviderlistingreports/{id}/{export}', 'ProviderSchedulerController@providerScheduledListExport');

    //Route::get('practicefacilityschedulerlist','FacilitySchedulerController@listSchedulerByFacility');
    //Route::get('practicescheduler/facility/{facility_id}','SchedulerController@listSchedulerByFacility');

    Route::resource('practicefacilityschedulerlist', 'FacilitySchedulerController');
    Route::post('practicefacilityschedulerlist', 'FacilitySchedulerController@index');
    Route::get('facilityscheduler/facility/{facility_id}', 'FacilitySchedulerController@viewFacilityScheduler');
    Route::get('practicefacilityscheduler/{facility_id}/{scheduler_id}', 'FacilitySchedulerController@viewFacilitySchedulerDetailsById');
    Route::get('facilityscheduler/facility/{facility_id}/delete/{id}', 'FacilitySchedulerController@destroy');    
	#Export
	Route::get('api/facilityschedulerreports/{export}', 'FacilitySchedulerController@getFacilitySchedulerExport');
    Route::get('api/schedulerfacilityreports/{id}/{export}', 'FacilitySchedulerController@facilityScheduledListExport');
   
    ### Ends - Scheduler settings ###   
    ### Starts - Superbills ###
    Route::resource('superbills', 'SuperbillsController');

    Route::group(array('prefix' => 'superbills'), function() {
        Route::post('/create', 'SuperbillsController@getTemplatelist');
        Route::post('/store', 'SuperbillsController@getTemplatestore');
        Route::post('/template/show', 'SuperbillsController@getTemplateshow');
        Route::get('/template/delete/{id}', 'SuperbillsController@destroy');
        Route::post('/template/search', 'SuperbillsController@getTemplatesearch');
    });
    ### Ends - Superbills ###   
    ### Starts - holdoption ###
    Route::resource('holdoption', 'HoldOptionController');
    Route::get('holdoption/{holdoption_id}/delete', 'HoldOptionController@destroy');
    Route::get('api/holdoptionreports/{export}', 'HoldOptionController@getHoldReasonExport');
    ### Ends - holdoption ### 
    
    ### Starts - reason for Visit ###
    Route::resource('reason', 'ReasonController');
    Route::get('reason/{reason_id}/delete', 'ReasonController@destroy');
    Route::get('api/reasonreports/{export}', 'ReasonController@getReasonExport');
    ### Ends - Reason for visits ###
    
    ### Starts - User Login History ###
    Route::get('userLoginHistory/{pageType}', 'UserLoginHistoryController@index');
    Route::post('userStatusChange', 'UserLoginHistoryController@userStatusChange');
    Route::post('userIpSecurityCodeRest', 'UserLoginHistoryController@userIpSecurityCodeRest');
    ### Ends - User Login History ###
       
    ####Insurance type Start ###
    Route::resource('insurancetypes', 'InsuranceTypesController');
    Route::get('insurancetypes/{insurancetypes}/delete', 'InsuranceTypesController@destroy');
    Route::get('api/insurancetypereports/{export}', 'InsuranceTypesController@getInsuranceTypesExport'); 
    ### Insurancetype End ###
    
    ### Starts -Adjust reason for Visit ###
    Route::resource('adjustmentreason', 'AdjustmentReasonController');
    Route::get('adjustmentreason/{adjustmentreason_id}/delete', 'AdjustmentReasonController@destroy');
    Route::get('api/adjustmentreason/{export}', 'AdjustmentReasonController@getAdjustmentreasonExport');    
    ### Ends - Reason for visits ###
    
    ### Starts - Patient statement category ###
    Route::resource('statementcategory', 'StatementCategoryController');
    Route::get('statementcategory/{statementcategory_id}/delete', 'StatementCategoryController@destroy');
    Route::get('api/statementcategoryreports/{export}', 'StatementCategoryController@getStatementCategoryExport');
    ### Ends - Patient statement category ###

    ### Starts -Patient statement hold reason ###
    Route::resource('statementholdreason', 'StatementHoldReasonController');
    Route::get('statementholdreason/{statementholdreason_id}/delete', 'StatementHoldReasonController@destroy');
    Route::get('api/statementholdreasonreports/{export}', 'StatementHoldReasonController@getStatementHoldReasonExport');
    ### Ends -Patient statement hold reason ###

    ### Starts - claim substatus ###
    Route::resource('claimsubstatus', 'ClaimSubStatusController');
    Route::get('claimsubstatus/{holdoption_id}/delete', 'ClaimSubStatusController@destroy');
    Route::get('api/claimsubstatusreports/{export}', 'ClaimSubStatusController@getHoldReasonExport');
    ### Ends - claim substatus ### 

    ### Starts - Email Template ###
    Route::resource('emailtemplate', 'EmailTemplateController');
    Route::post('emailtemplate/1', 'EmailTemplateController@update');
    ### Ends - Email Templates ###

    ### Starts - Questionnaries ###
    Route::resource('questionnaire/template', 'QuestionnariesTemplateController');
    Route::get('questionnaire/template/{id}/delete', 'QuestionnariesTemplateController@destroy');
    
    Route::resource('questionnaires', 'QuestionnariesController');
    Route::get('questionnaires/{id}/delete', 'QuestionnariesController@destroy');
    Route::post('questionnaire/template/quesansdelete', 'QuestionnariesTemplateController@quesansdelete');
    Route::get('api/questionnaireexport/{type}', 'QuestionnariesController@getQuestionnariesExport');
    Route::get('api/questionnaire/templateexport/{type}', 'QuestionnariesTemplateController@getQuestionnariesTemplateExport');
    
    ### Ends - Questionnaries ###   
    ### Starts - UserHistory ###
    Route::resource('practices/userhistory', 'UserHistoryController');
    Route::get('export/userhistory/{type}', 'Api\UserHistoryApiController@getIndexApi');
    ### Ends - UserHistory ###     
    ### Start - clinicalnotescategory ###

    Route::resource('clinicalnotescategory', 'ClinicalNotesCategoryController');
    Route::patch('clinicalnotescategory/{id}/update', 'ClinicalNotesCategoryController@update');
    Route::get('clinicalnotescategory/delete/{id}', 'ClinicalNotesCategoryController@destroy');


    ### End - clinicalnotescategory ###
    ### Starts - Ajax ###
    // Taxanomy
    

    // Dynamic select box add new option get and save
    Route::get('getoptionvalues', 'Api\InsuranceApiController@getoptionvalues');
    Route::get('addnewselect', 'InsuranceController@addnewselect');

    // Side bar collapse //
    Route::get('collapse', 'PracticesController@setCollapse');
    ### Ends - Ajax ###       
    ///*** Ends - Practice Url's ***///  
    ### Starts - Practice registration ###
    Route::group(array('prefix' => 'registration'), function() {
        Route::resource('', 'RegistrationController');
        Route::post('/edit', 'RegistrationController@store');
    });
    ### Ends - Practice Registration ###  
    Route::group(array('prefix' => 'wishlists'), function() {
        Route::get('', 'WishlistController@index');
        Route::get('create', 'WishlistController@create');
        Route::post('create', 'WishlistController@store');
        Route::get('{id}/show', 'WishlistController@show');
        Route::get('{id}/edit', 'WishlistController@edit');
        Route::any('{id}/update', 'WishlistController@update');
        Route::any('delete', 'WishlistController@destroy');
    });
});


### Authentication ###
Route::resource('auth', 'Auth\AuthController');
Route::resource('password', 'Auth\PasswordController');

### Starts - API Url's ###
// Payment posting routes ends here
### Ends - API Url's ###
### Start to Forgetpassword ###
Route::get('password/email', 'Auth\AuthController@getEmail');

// Password reset routes...
Route::get('resetpassword/{email}/{token}', 'Auth\AuthController@getReset');
Route::post('resetpassword', 'Auth\AuthController@postReset');
### End to Forgetpassword ###

### Start Hide "Security Code" when typing a new email ID ###
Route::post('check-security-code', 'Auth\AuthController@check_security_code');
### End Hide "Security Code" when typing a new email ID ###

### Start to Support ###
Route::get('support/faq', 'Support\FaqController@index');
Route::get('support', 'Support\FaqController@index');
# Route::get('soap', 'SoapController@show'); 

Route::resource('ticket', 'Support\TicketController');
Route::get('searchticket', 'Support\TicketStatusController@index');
Route::get('getticketdocument/{id}', 'Support\Api\TicketStatusApiController@getticketdocumentApi');
Route::post('replyticket', 'Support\TicketStatusController@getReplyTicket');
Route::get('getticketdetail/{id}/{page}', 'Support\TicketStatusController@getTicketDetail');
Route::resource('myticket', 'Support\MyticketController');
Route::post('filterticket', 'Support\MyticketController@index');
Route::get('removereplyticket/{ticket_id}/{replyid}', 'Support\MyticketController@removeReply');
Route::get('myticketreports/{export}', 'Support\Api\MyticketApiController@getMyTicketApi');
Route::get('emailticket', 'Support\TicketController@emailticket');
### End to Support ###
//APP URL PROCESS START

//APP URL PROCESS END
//Charge Capture app start
Route::group(array('prefix' => 'chargeCapture/app'), function () {
    Route::post('getlogin', 'App\Api\ChargeCaptureAppApiController@getUserList');
    Route::post('login', 'App\Api\ChargeCaptureAppApiController@checklogindetails');
    Route::post('forgotpassword', 'App\Api\ChargeCaptureAppApiController@forgotpasswordprocess');
    Route::post('logout', 'App\Api\ChargeCaptureAppApiController@logoutprocess');
    
    Route::post('getCptAndIcdWithLastModifiedDate', 'App\Api\ChargeCaptureAppApiController@getCptAndIcdWithLastModifiedDate');
    Route::post('getFacilityList', 'App\Api\ChargeCaptureAppApiController@getFacilityList');
    Route::post('getPatientList', 'App\Api\ChargeCaptureAppApiController@getPatientList');
    Route::post('getPatientDetails', 'App\Api\ChargeCaptureAppApiController@getFullPatientDetails');
    Route::post('GetAppData', 'App\Api\ChargeCaptureAppApiController@GetAppData');
    Route::get('getCptAndIcdWithLastModifiedDate', 'App\Api\ChargeCaptureAppApiController@getCptAndIcdWithLastModifiedDate');
    Route::post('addNewClaims', 'App\Api\ChargeCaptureAppApiController@addNewClaims');
    Route::post('searchIcdDatas', 'App\Api\ChargeCaptureAppApiController@searchIcdDatas');
    Route::post('getMapedModifierWithCpt', 'App\Api\ChargeCaptureAppApiController@getMapedModifierWithCpt');
    Route::post('getfaq', 'App\Api\ChargeCaptureAppApiController@getAllFaq');
    Route::post('getAboutData', 'App\Api\ChargeCaptureAppApiController@getAboutData');
    Route::post('addNewPatientFromExtension', 'App\Api\ChargeCaptureAppApiController@createFromExtensionPatient');
    Route::post('checkSsn', 'App\Api\ChargeCaptureAppApiController@checkSsn');
    Route::post('getDocumentCategoryList', 'App\Api\ChargeCaptureAppApiController@getDocumentCategoryList');
    Route::post('documentUpload', 'App\Api\ChargeCaptureAppApiController@documentUpload');
});
//Charge Captur End

//chromeExtensionWork Start
Route::post('medcubicsExtension/app/addNewPatient', 'App\Api\ChargeCaptureAppApiController@addNewPatientFromChromeExtension');
//chromeExtensionWork End

Route::get('storage/{filename}', 'Api\CommonExportApiController@getStorageFile');

//twilio Check
Route::get('makeacall/{phonenumber}', 'Twilio\Api\TwilioApi@createNewCall');
Route::get('callhistory/{phonenumber}/{userId}/{type}', 'Twilio\Api\TwilioApi@callsList');
Route::post('createCallLogHistory', 'Twilio\Api\TwilioApi@createCallLogHistory');
Route::post('updateCallLogHistory', 'Twilio\Api\TwilioApi@updateCallLogHistory');
Route::get('getTwilioToken/{phonenumber}', 'Twilio\Api\TwilioApi@getTwilioToken');
Route::post('connectthecall', 'Twilio\Api\TwilioApi@connectTheCall');
Route::post('sendSms', 'Twilio\Api\TwilioApi@sendSms');
Route::get('phoneNumLoookup', 'Twilio\Api\TwilioApi@phoneNumLoookup');

Route::get('privacypolicy', 'HomeController@getPrivacyPolicy');

// Author - Baskar

Route::group(array('middleware' => ['csrf', 'auth', 'session']), function() {
    
    //Author: baskar
    //--------------- ADMIN SQL MAINTENANCE START ------------------
    Route::get('admin/maintenance-sql', 'MaintenanceController@index');
    Route::post('admin/sql/create', 'MaintenanceController@create');
    Route::post('admin/sql/execute', 'MaintenanceController@execute');
    Route::get('claimsintegrity', 'ClaimsIntegrityController@claimsintegrity');
    //--------------- ADMIN SQL MAINTENANCE END ------------------

    //Author: sridhar
    //--------------- ADMIN API LIST START ------------------
    Route::group(array('prefix' => 'admin/apiconfig'), function() {
        Route::get('', 'Medcubics\ApiConfigController@index');
        Route::get('create', 'Medcubics\ApiConfigController@create');
        Route::post('create', 'Medcubics\ApiConfigController@store');
        Route::get('{id}/show', 'Medcubics\ApiConfigController@show');
        Route::get('{id}/edit', 'Medcubics\ApiConfigController@edit');
        Route::any('{id}/update', 'Medcubics\ApiConfigController@update');
        Route::any('{id}/delete', 'Medcubics\ApiConfigController@destroy');
    });
    //--------------- ADMIN API LIST END ------------------


    //--------------- ADMIN Claim Integrity ------------------
    
    Route::get('admin/claimsintegrity', 'ClaimsIntegrityController@index');
    Route::post('admin/dynamic', 'ClaimsIntegrityController@getdynamicdocument');
    Route::get('admin/claimsintegrity/category', 'ClaimsIntegrityController@getCategory');

    //--------------- ADMIN Claim Integrity END ------------------
    
    Route::group(array('prefix' => 'reports'), function() {

        //Author: Pandian
        //-------------------All Reports PDF Export Start--------------------
        Route::group(array('prefix' => 'export_pdf'), function(){
            Route::get('generate_report/{report_name}/{id?}','ExportPDF\GenerateReportController@index');
            Route::post('charge-analysis-detailed','ExportPDF\ChargeAnalysisPDFController@index');
            Route::post('charges-payments-summary','ExportPDF\ChargesPaymentsPDFController@index');
            Route::post('unbilled-claims-analysis','ExportPDF\UnbilledPDFController@index');
            Route::post('end-of-the-day-totals','ExportPDF\EndDayTotalsPDFController@index');
            Route::post('year-end-financials','ExportPDF\YearEndFinancialsPDFController@index');
            Route::post('work-rvu-report','ExportPDF\WorkRvuPDFController@index');
            Route::post('charge-category-report','ExportPDF\ChargeCategoryPDFController@index');
            Route::post('refund-analysis-detailed','ExportPDF\RefundAnalysisPDFController@index');
            Route::post('payment-analysis-detailed-report','ExportPDF\PaymentAnalysisDetailedPDFController@index');
            Route::post('procedure-collection-report-insurance-only','ExportPDF\ProcedureCollectionPDFController@index');
            Route::post('adjustment-analysis-detailed','ExportPDF\AdjustmentAnalysisPDFController@index');
            Route::post('insurance-over-payment','ExportPDF\InsuranceOverPaymentPDFController@index');
            Route::post('patient-and-insurance-payment','ExportPDF\PatientInsurancePaymentPDFController@index');
            Route::post('appointment-analysis-report','ExportPDF\AppointmentAnalysisPDFController@index');
            Route::post('aging-summary','ExportPDF\AgingSummaryPDFController@index');
            Route::post('aging-analysis-detailed','ExportPDF\AgingAnalysisDetailedPDFController@index');
            Route::post('denial-trend-analysis','ExportPDF\DenialtrendanalysisController@index');
            Route::post('ar-work-bench-report','ExportPDF\ArWorkbenchReportPDFController@index');
            Route::post('demographic-sheet','ExportPDF\DemographicSheetPDFController@index');
            Route::post('address-listing','ExportPDF\AddressListingPDFController@index');
            Route::post('icd-worksheet','ExportPDF\ICDWorksheetPDFController@index');
            Route::post('wallet-history-detailed','ExportPDF\WalletHistoryPDFController@index');
            Route::post('statement-history-detailed','ExportPDF\PatientStatementHistoryPDFController@index');
            Route::post('statement-status-detailed','ExportPDF\PatientStatementStatusPDFController@index');
            Route::post('wallet-balance','ExportPDF\WalletBalancePDFController@index');
            Route::post('cpt-hcpcs-summary','ExportPDF\CPTsummaryPDFController@index');
            Route::post('payer-summary','ExportPDF\PayerSummaryPDFController@index');
            Route::post('provider-summary','ExportPDF\ProviderSummaryPDFController@index');
            Route::post('facility-summary','ExportPDF\FacilitySummaryPDFController@index');

            Route::post('patients_list','ExportPDF\PatientsListPDFController@index');
            Route::post('patient_claims_list','ExportPDF\PatientBillingPDFController@index');
            Route::post('charges','ExportPDF\ChargesPDFController@index');
            Route::post('payments','ExportPDF\PaymentsPDFController@index');
            Route::post('electronic_claims','ExportPDF\ElectronicClaimsPDFController@index');
            Route::post('paper_claims','ExportPDF\PaperClaimsPDFController@index');
            Route::post('claim_edits','ExportPDF\ClaimEditsPDFController@index');
            Route::post('submitted_claims','ExportPDF\SubmittedClaimsPDFController@index');
            Route::post('rejection_claims','ExportPDF\EdiRejectionsPDFController@index');
            Route::post('paymentsE-remittance','ExportPDF\EremittancePDFController@index');
        });
        //-------------------All Reports PDF Export End----------------------

        // Collection reports  - Start
        //---------------- Insurance Over Payment-----------------
        Route::get('collections/insurance-over-payment', 'Reports\CollectionController@insuranceOverpaymentList');
        Route::post('collections/insurance-over-payment/export', 'Reports\CollectionController@insuranceOverPaymentSearchexport');
        Route::post('search/insuranceOverPayment', 'Reports\CollectionController@insuranceOverpaymentSearch');
        Route::post('search/insuranceOverPayment/pagination', 'Reports\CollectionController@insuranceOverpaymentSearch');
        //---------------- Patient and Insuranace Payment-----------------
        Route::get('collections/patient-insurance', 'Reports\CollectionController@patientInsurancePaymentList');
        Route::post('collections/patient-insurance/export', 'Reports\CollectionController@patientInsurancePaymentSearchexport');
        Route::get('collections/patient-insurance/export_pdf', 'Api\CommonExportPdfController@index');
        Route::post('search/patientInsurancePayment', 'Reports\CollectionController@patientInsurancePaymentSearch');
        Route::post('search/patientInsurancePayment/pagination', 'Reports\CollectionController@patientInsurancePaymentSearch');
    	// Collection reports  - End

    	// Patient reports  - Start
        Route::get('wallet-balance', 'Reports\PatientController@walletBalance');
        Route::post('wallet-balance/export', 'Reports\PatientController@walletBalanceSearchExport');
        Route::post('search/walletBalanceSearch', 'Reports\PatientController@walletBalanceSearch');
        Route::post('search/walletBalanceSearch/pagination', 'Reports\PatientController@walletBalanceSearch');
            //Patient - Itemized Bill
        Route::get('patient-itemized-bill', 'Reports\PatientController@itemizedBill');
        Route::post('patient-itemized-bill/export', 'Reports\PatientController@itemizedBillSearchExport');
        Route::post('search/itemizedBillSearch', 'Reports\PatientController@itemizedBillSearch');
        Route::post('search/itemizedBillSearch/pagination', 'Reports\PatientController@itemizedBillSearch');

        Route::get('performance/list', 'Reports\PerformanceController@reportList');
        Route::any('performance/monthend', 'Reports\PerformanceController@monthendperformanceLoad');
        Route::any('search/performance/monthend', 'Reports\PerformanceController@monthendperformance');
        Route::post('performance/monthend/export', 'Reports\PerformanceController@monthendperformanceExport');
        Route::any('performance/provider', 'Reports\PerformanceController@providerSummary');
        Route::post('performance/provider/export', 'Reports\PerformanceController@providerSummaryExport');
        // Route::post('performance/provider/export_pdf', 'Reports\Financials\UnbilledExportPDFController@performance_provier');
        Route::any('search/performance/provider', 'Reports\PerformanceController@providerSummary');
        Route::any('performance/denials', 'Reports\PerformanceController@denialsSummary');
        Route::get('performance/denials/export_pdf', 'Api\CommonExportPdfController@index');
        Route::any('search/performance/denials', 'Reports\PerformanceController@denialsSummary');
        Route::any('performance/billing', 'Reports\PerformanceController@weeklyBillingReport');
        Route::any('search/performance/billing', 'Reports\PerformanceController@weeklyBillingReportExport');
        // Patient reports  - End
    });
    

    // Financials Dashboard
    //Route::any('analytics/financials', 'FinancialsDashboardController@dashboard');
    
    //Charge Delete in practice Settings
    Route::group(array('prefix' => 'practice'), function() {
        Route::get('charge/delete', 'practiceSettings\chargeDeleteController@index');
        Route::post('charge/delete', 'practiceSettings\chargeDeleteController@destroy');
        Route::post('search/chargeDelete', 'practiceSettings\chargeDeleteController@search');
        Route::post('search/chargeDelete/pagination', 'practiceSettings\chargeDeleteController@search');
    });
});


Route::group(array('prefix' => 'api', 'middleware' => ['auth', 'session']), function() {
  
// Moved routes to api.php     
    

});
