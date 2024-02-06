<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/	

Route::post('login', 'App\Api\AppMainApiController@getlogindetails');
Route::post('forgotpassword', 'App\Api\AppMainApiController@forgotpasswordprocess');
Route::post('logout', 'App\Api\AppMainApiController@logoutprocess');
Route::post('password/email', 'Auth\AuthController@postEmail');


//Route::group(array('prefix' => 'app', 'middleware' => ['auth']), function() {
Route::group(array('prefix' => 'app', 'middleware' => ['auth']), function() {
	
    Route::post('getProviderListByschedulardate', 'App\Api\AppMainApiController@getProviderListByschedulardate');
    Route::post('getPatientDetailsByproviderDate', 'App\Api\AppMainApiController@getPatientDetailsByproviderDate');
    Route::post('getPatientDetailsByproviderAppointment', 'App\Api\AppMainApiController@getPatientDetailsByproviderAppointment');
    Route::post('getPatientDetailsEditbaseproviderAppointment', 'App\Api\AppMainApiController@getPatientDetailsEditbaseproviderAppointment');
    Route::post('PatientDetailsEditUniquevalidation', 'App\Api\AppMainApiController@PatientDetailsEditUniquevalidation');
    Route::post('getPrepopulatedList', 'App\Api\AppMainApiController@getPrepopulatedList');
    Route::post('storepatient', 'App\Api\AppMainApiController@storePatient');
    Route::post('tmppatient_img', 'App\Api\AppMainApiController@tmppatient_img');
    Route::post('getProviderListIntakeBydate', 'App\Api\AppMainApiController@getProviderListIntakeBydate');
    Route::post('getPatDetailsIntakeByProviderDate', 'App\Api\AppMainApiController@getPatDetailsIntakeByProviderDate');

    // NEWLY ADDED (17/03/2017)
    Route::post('getProviderAndPatientList', 'App\Api\AppMainApiController@getProviderAndPatientList');
    Route::post('getPatientInfDetails', 'App\Api\AppMainApiController@getPatientInfDetails');
    Route::post('updatePatientImg', 'App\Api\AppMainApiController@updatePatientImg');
});


/// Scheduler settings by provider ///
Route::group(array('middleware' => ['auth', 'session']), function() {	
	Route::get('checkicdexist/{icd_code}', 'Patients\Api\BillingApiController@checkICDexistApi');

    Route::get('getworkinghourstimingsforprovider/{provider_id}/{facility_id}', 'Api\ProviderSchedulerApiController@getAvailableWorkingHoursByFacilityAndProvider');
    Route::post('storeproviderschedulersettings', 'Api\ProviderSchedulerApiController@storeProviderScheduleSettings');

    // WebcamApi starts
    Route::post('getwebcamimage/{type?}', 'Api\CommonWebcamApiController@getwebcamimageApi');

    ###### Report Start #######
    Route::get('aginganalysisdetailsreports/{export}', 'Reports\Billing\Api\BillingApiController@getIndexApi');

    ###### Report Start ####
    ///*** Starts - Practice Url's ***///    
    ### Starts - Practice ### 
    #Practice -> Overrides        
    //Route::get   ('practiceoverridesreports/{export}', 'Api\PracticeOverridesApiController@getIndexApi');
    #Practice -> Managecare
    Route::group(array('prefix' => 'managecare'), function() {
	    Route::get('/', 'Api\PracticeManagecareApiController@getIndexApi');
	    Route::get('create', 'Api\PracticeManagecareApiController@getCreateApi');
	    Route::post('store', 'Api\PracticeManagecareApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\PracticeManagecareApiController@getEditApi');
	    Route::post('update/{id}', 'Api\PracticeManagecareApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\PracticeManagecareApiController@getDeleteApi');
	});
    #Export
    //Route::get('practicereports/{export}', 'Api\PracticeManagecareApiController@getIndexApi');

    #Practice -> Contact Details
    Route::group(array('prefix' => 'contactdetail'), function() {
	    Route::get('/', 'Api\ContactdetailApiController@getShowApi');
	    Route::get('{id}/edit', 'Api\ContactdetailApiController@getEditApi');
	    Route::post('store', 'Api\ContactdetailApiController @getStoreApi');
	});

    // Practice Documents
    Route::group(array('prefix' => 'document'), function() {
	    Route::get('{type}/{id?}', 'Api\DocumentApiController@getIndexApi');
	    Route::get('create/{type}/{id?}', 'Api\DocumentApiController@getCreateApi');
	    Route::post('store/{type}/{id?}', 'Api\DocumentApiController@getAddDocumentApi');
	    Route::post('get/{filename}/{type}/{id?}', 'Api\DocumentApiController@getGetApi');
	    Route::get('delete/{type}/{id}', 'Api\DocumentApiController@getDestroyApi');
	    Route::post('get_document_subgategory_list/{id?}', 'Api\DocumentApiController@get_document_subgategory_list');
    	Route::post('deletePopupDocument/{id}', 'Api\DocumentApiController@deletePopupDocumentApi');
	});

    Route::get('adddocumentmodal/{type}/{type_id}/{category}/{temp_doc_id?}', 'Api\DocumentApiController@addDocumentmodelApi');
    Route::post('documentmodal/store/{type}/{id?}', 'Api\DocumentApiController@getAddDocumentmodalApi');
    Route::get('documentmodal/get/{id}/{type}/{filename}', 'Api\DocumentApiController@getdocumentmodalApi');
    Route::get('documentdownload/get/{id}/{type}/{filename}', 'Api\DocumentApiController@getdocumentdownloadApi');
    

    Route::get('get_seleted_icd_details/{icd_code}', 'Patients\Api\SuperBillApiController@get_seleted_icd_details');
    Route::post('superbillformvalidation/{tab_type}', 'Patients\Api\SuperBillApiController@superbillformvalidation');
    Route::post('superbill_getseletedproviderdetails/{provider_id}', 'Patients\Api\SuperBillApiController@superbill_getseletedproviderdetails');
    Route::get('superbill_getseletedtemplatedetails/{template_id}/{sel_cpts_vals}', 'Patients\Api\SuperBillApiController@superbill_getseletedtemplatedetails');
    Route::get('get_seleted_cpt_details/{cpt_code}', 'Patients\Api\SuperBillApiController@get_seleted_cpt_details');
    Route::post('superbill_getcreatebilltab_details', 'Patients\Api\SuperBillApiController@superbill_getcreatebilltab_details');
    Route::post('get_superbill_search_icd_cpt_list', 'Patients\Api\SuperBillApiController@get_superbill_search_icd_cpt_list');
    Route::post('select_api_search_icd_cpt_list', 'Patients\Api\SuperBillApiController@select_api_search_icd_cpt_list');

    #Practice -> Notes
    Route::group(array('prefix' => 'notes/{type}'), function() {
	    Route::get('/', 'Api\NotesApiController@getIndexApi');
	    Route::get('create', 'Api\NotesApiController@getCreateApi');
	    Route::post('store', 'Api\NotesApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\NotesApiController@getEditApi');
	    Route::post('update/{id}', 'Api\NotesApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\NotesApiController@getDeleteApi');
	});
    #Export
    Route::get('practicenotesreports/{type}/{id}/{export}', 'Api\NotesApiController@getIndexApi');
    ### Ends - Practice ### 
    ### Starts - Facility ### 
    #Facility Export API
    //Route::get('facilityreports/{export}', 'Api\FacilityApiController@getIndexApi');

    #Facility Overrides Export API
    Route::get('facilityoverridesreports/{id}/{export}', 'Api\FacilityoverridesApiController@getIndexApi');
    #Facility Managed Care Export API
    //Route::get('facilitymanagecarereports/{id}/{export}', 'Api\FacilitymanagecareApiController@getIndexApi');
    #Facility Notes Export API
    Route::get('facilitynodesreports/{type}/{id}/{export}', 'Api\NotesApiController@getIndexApi');
    ### Ends - Facility ###
    ### Starts - Provider ###
    #Provider
    // Route::pattern('providerid', '[0-9]+');Route::pattern('id', '[0-9]+');
    Route::group(array('prefix' => 'provider'), function() {
	    Route::get('/', 'Api\ProviderApiController@getIndexApi');
	    Route::get('{id}', 'Api\ProviderApiController@getShowApi');
	    Route::get('create', 'Api\ProviderApiController@getCreateApi');
	    Route::get('{id}/edit', 'Api\ProviderApiController@getEditApi');
	    Route::get('delete/{id}', 'Api\ProviderApiController@getDeleteApi');
	    Route::post('{id}/store', 'Api\ProviderApiController@getStoreApi');
	    Route::post('{id}/update', 'Api\ProviderApiController@getUpdateApi');
	});
    #Export
    Route::get('provider/search/{export}', 'Api\ProviderApiController@getIndexApi');
    //Route::get('providerreports/{export}', 'Api\ProviderApiController@getIndexApi');

    #Provider -> Overrides
    Route::group(array('prefix' => 'provider/{providerid}/provideroverrides'), function() {
	    Route::get('/', 'Api\ProviderOverridesApiController@getIndexApi');
	    Route::get('{id}', 'Api\ProviderOverridesApiController@getShowApi');
	    Route::get('create', 'Api\ProviderOverridesApiController@getCreateApi');
	    Route::get('{id}/edit', 'Api\ProviderOverridesApiController@getEditApi');
	    Route::get('delete/{id}', 'Api\ProviderOverridesApiController@getDeleteApi');
	    Route::post('{id}/store', 'Api\ProviderOverridesApiController@getStoreApi');
	    Route::post('{id}/update', 'Api\ProviderOverridesApiController@getUpdateApi');
	});
    #Export
    Route::get('provideroverridesreports/{id}/{export}', 'Api\ProviderOverridesApiController@getIndexApi');

    #Provider -> Managed Care
    Route::group(array('prefix' => 'provider/{providerid}/providermanagecare'), function() {
	    Route::get('/', 'Api\ProviderManagedcareApiController@getIndexApi');
	    Route::get('{id}', 'Api\ProviderManagedcareApiController@getShowApi');
	    Route::get('create', 'Api\ProviderManagedcareApiController@getCreateApi');
	    Route::get('{id}/edit', 'Api\ProviderManagedcareApiController@getEditApi');
	    Route::get('delete/{id}', 'Api\ProviderManagedcareApiController@getDeleteApi');
	    Route::post('{id}/store', 'Api\ProviderManagedcareApiController@getStoreApi');
	    Route::post('{id}/update', 'Api\ProviderManagedcareApiController@getUpdateApi');
	});
    #Export
    //Route::get('providermanagecarereports/{id}/{export}', 'Api\ProviderManagedcareApiController@getIndexApi');

    #Provider -> Documents
    Route::group(array('prefix' => 'provider/{providerid}/providerdocuments'), function() {
	    Route::get('/', 'Api\ProviderDocumentApiController@getIndexApi');
	    Route::get('{id}', 'Api\ProviderDocumentApiController@getShowApi');
	    Route::get('create', 'Api\ProviderDocumentApiController@getCreateApi');
	    Route::get('{id}/edit', 'Api\ProviderDocumentApiController@getEditApi');
	    Route::get('delete/{id}', 'Api\ProviderDocumentApiController@getDeleteApi');
	    Route::post('{id}/store', 'Api\ProviderDocumentApiController@getStoreApi');
	    Route::post('{id}/update', 'Api\ProviderDocumentApiController@getUpdateApi');
	});
    #Provider -> Notes Export API
    Route::get('providernotesreports/{type}/{id}/{export}', 'Api\NotesApiController@getIndexApi');
    ### Ends - Provider ###
    ### Starts - Resources ###
    #Resources
    Route::get('resourcesreports/{export}', 'Api\ResourcesApiController@getIndexApi');

    Route::group(array('prefix' => 'resources'), function() {
	    Route::get('/', 'Api\ResourcesApiController@getIndexApi');
	    Route::get('create', 'Api\ResourcesApiController@getCreateApi');
	    Route::post('store', 'Api\ResourcesApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\ResourcesApiController@getEditApi');
	    Route::post('update/{id}', 'Api\ResourcesApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\ResourcesApiController@getDeleteApi');
	});
    ### Ends - Resources ###
    ### Starts - Insurance ###
    #Insurance 
    Route::group(array('prefix' => 'insurance'), function() {
	    Route::get('/', 'Api\InsuranceApiController@getIndexApi');
	    Route::get('{id}', 'Api\InsuranceApiController@getShowApi');
	    Route::get('create', 'Api\InsuranceApiController@getCreateApi');
	    Route::get('{id}/edit', 'Api\InsuranceApiController@getEditApi');
	    Route::get('delete/{id}', 'Api\InsuranceApiController@getDeleteApi');
	    Route::post('{id}/store', 'Api\InsuranceApiController@getStoreApi');
	    Route::post('{id}/update', 'Api\InsuranceApiController@getUpdateApi');
	});
    #Exports
    //Route::get('insurancereports/{export}', 'Api\InsuranceApiController@getIndexApi');

    #Insurance -> Overrides
    Route::group(array('prefix' => 'insurance/{insuranceid}/insuranceoverrides'), function() {
	    Route::get('/', 'Api\InsuranceOverridesApiController@getIndexApi');
	    Route::get('/{id}', 'Api\InsuranceOverridesApiController@getShowApi');
	    Route::get('/create', 'Api\InsuranceOverridesApiController@getCreateApi');
	    Route::get('/{id}/edit', 'Api\InsuranceOverridesApiController@getEditApi');
	    Route::get('/delete/{id}', 'Api\insuranceOverridesApiController@getDeleteApi');
	    Route::post('/{id}/store', 'Api\InsuranceOverridesApiController@getStoreApi');
	    Route::post('/{id}/update', 'Api\InsuranceOverridesApiController@getUpdateApi');
	});
    #Export
    Route::get('insuranceoverridesreports/{id}/{export}', 'Api\InsuranceOverridesApiController@getIndexApi');

    #Insurance Master
    Route::group(array('prefix' => 'insurance'), function() {
	    Route::get('/', 'Api\InsuranceApiController@getIndexApi');
	    Route::get('/{id}', 'Api\InsuranceApiController@getShowApi');
	    Route::get('/create', 'Api\InsuranceApiController@getCreateApi');
	    Route::get('/{id}/edit', 'Api\InsuranceApiController@getEditApi');
	    Route::get('/delete/{id}', 'Api\InsuranceApiController@getDeleteApi');
	    Route::post('/{id}/store', 'Api\InsuranceApiController@getStoreApi');
	    Route::post('/{id}/update', 'Api\InsuranceApiController@getUpdateApi');
	});
    #Export
    //Route::get('insurancereports/{export}', 'Api\InsuranceApiController@getIndexApi');

    #Insurance Master -> Appeal address
    Route::group(array('prefix' => 'insurance/{insuranceid}/insuranceappealaddress'), function() {
	    Route::get('/', 'Api\InsuranceAppealAddressApiController@getIndexApi');
	    Route::get('{id}', 'Api\InsuranceAppealAddressApiController@getShowApi');
	    Route::get('/create', 'Api\InsuranceAppealAddressApiController@getCreateApi');
	    Route::get('/{id}/edit', 'Api\InsuranceAppealAddressApiController@getEditApi');
	    Route::get('/delete/{id}', 'Api\InsuranceAppealAddressApiController@getDeleteApi');
	    Route::post('/{id}/store', 'Api\InsuranceAppealAddressApiController@getStoreApi');
	    Route::post('/{id}/update', 'Api\InsuranceAppealAddressApiController@getUpdateApi');
	});
    #Export
    //Route::get('insuranceappealaddress/{id}/{export}', 'Api\InsuranceAppealAddressApiController@getIndexApi');

    #Insurance Master -> Overrides
    Route::group(array('prefix' => 'insurance/{insuranceid}/insuranceoverrides'), function() {
	    Route::get('/', 'Api\InsuranceOverridesApiController@getIndexApi');
	    Route::get('{id}', 'Api\InsuranceOverridesApiController@getShowApi');
	    Route::get('create', 'Api\InsuranceOverridesApiController@getCreateApi');
	    Route::get('edit', 'Api\InsuranceOverridesApiController@getEditApi');
	    Route::get('delete/{id}', 'Api\InsuranceOverridesApiController@getDeleteApi');
	    Route::post('{id}/store', 'Api\InsuranceOverridesApiController@getStoreApi');
	    Route::post('{id}/update', 'Api\InsuranceOverridesApiController@getUpdateApi');
	});
    #Export
    Route::get('insuranceoverridesreports/{id}/{export}', 'Api\InsuranceOverridesApiController@getIndexApi');

    ### Ends - Insurance ###
    ### Starts - CPT ###
    #CPT
    Route::group(array('prefix' => 'cpt'), function() {
	    Route::get('/', 'Api\CptApiController@getIndexApi');
	    Route::get('create', 'Api\CptApiController@getCreateApi');
	    Route::post('store', 'Api\CptApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\CptApiController@getEditApi');
	    Route::post('update/{id}', 'Api\CptApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\CptApiController@getDeleteApi');
	});
    #Export
    //Route::get('cptreports/{export}', 'Api\CptApiController@getIndexApi');
    #Favourite Export
    //Route::get('cptfavouritereports/{export}', 'Api\CptApiController@getListFavouritesApi');
    #CPT Get datatable values
    Route::get('getcpttablevalues', 'Api\CptApiController@getcpttablevalues');
    Route::get('getFavouritescpttablevalues/{year?}/{insurance?}', 'Api\CptApiController@getFavouritescpttablevalues');
    ### Ends - CPT ###
    ### Starts - ICD ###
    #ICD-10
    Route::group(array('prefix' => 'icd'), function() {
	    Route::get('/', 'Api\IcdApiController@getIndexApi');
	    Route::get('create', 'Api\IcdApiController@getCreateApi');
	    Route::post('store', 'Api\IcdApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\IcdApiController@getEditApi');
	    Route::post('update/{id}', 'Api\IcdApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\IcdApiController@getDeleteApi');
	});
    #Export
    //Route::get('icdreports/{export}', 'Api\IcdApiController@getIndexApi');

    #ICD-10 Get datatable values
    Route::get('geticdtablevalues', 'Api\IcdApiController@geticdtablevalues');
    ### Ends - ICD ###
    ### Starts - Advanced Search ###
    Route::post('advanced/keywordsearch', 'Api\AdvancedSearchIcdCptApiController@getAdvancedSearchApi');
    ### Ends - Advanced Search ###
    ### Starts - Modifiers ###
    #Modifiers Level 1
    Route::group(array('prefix' => 'modifierlevel1'), function() {
	    Route::get('/', 'Api\ModifierApiController@getIndexApi');
	    Route::get('create', 'Api\ModifierApiController@getCreateApi');
	    Route::post('store', 'Api\ModifierApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\ModifierApiController@getEditApi');
	    Route::post('update/{id}', 'Api\ModifierApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\ModifierApiController@getDeleteApi');
	    Route::get('{id}', 'Api\ModifierApiController@getShowApi');
	    #Export
	    Route::get('search/{export}', 'Api\ModifierApiController@getIndexApi');
	});

    //Route::get('modifierreports/{export}', 'Api\ModifierApiController@getIndexApi');

    #Modifiers Level 2
    Route::group(array('prefix' => 'modifierlevel2'), function() {
	    Route::get('/', 'Api\ModifierLevelApiController@getIndexApi');
	    Route::get('create', 'Api\ModifierLevelApiController@getCreateApi');
	    Route::post('store', 'Api\ModifierLevelApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\ModifierLevelApiController@getEditApi');
	    Route::post('update/{id}', 'Api\ModifierLevelApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\ModifierLevelApiController@getDeleteApi');
	    Route::get('{id}', 'Api\ModifierLevelApiController@getShowApi');
	    #Export
	    Route::get('search/{export}', 'Api\ModifierLevelApiController@getIndexApi');
	});
    //Route::get('modifierlevelreports/{export}', 'Api\ModifierLevelApiController@getIndexApi');

    ### Ends - Modifiers ###
    ### Starts - Employers ###
    Route::group(array('prefix' => 'employer'), function() {
	    Route::get('/', 'Api\EmployerApiController@getIndexApi');
	    Route::get('create', 'Api\EmployerApiController@getCreateApi');
	    Route::post('store', 'Api\EmployerApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\EmployerApiController@getEditApi');
	    Route::post('update/{id}', 'Api\EmployerApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\EmployerApiController@getDeleteApi');
	    Route::get('{id}', 'Api\EmployerApiController@getShowApi');
	});
    #Export
    //Route::get('employerreports/{export}', 'Api\EmployerApiController@getIndexApi');
    #Employer -> Notes Export
    Route::get('employernotesreports/{type}/{id}/{export}', 'Api\NotesApiController@getIndexApi');
    ### Ends - Employers ###
    ### Starts - Codes ###
    Route::group(array('prefix' => 'code'), function() {
	    Route::get('/', 'Api\CodeApiController@getIndexApi');
	    Route::get('create', 'Api\CodeApiController@getCreateApi');
	    Route::post('store', 'Api\CodeApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\CodeApiController@getEditApi');
	    Route::post('update/{id}', 'Api\CodeApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\CodeApiController@getDeleteApi');
	    Route::get('search/{export}', 'Api\CodeApiController@getIndexApi');
	});
	#Export
    //Route::get('codereports/{export}', 'Api\CodeApiController@getIndexApi');
    ### Ends - Codes ###
    ### Starts - Templates ###
    //Route::get('templatereports/{type}/{export}', 'Api\TemplatesApiController@getIndexApi');

    #Templates -> Template Types
    Route::group(array('prefix' => 'templatetypes'), function() {
	    Route::get('/', 'Api\TemplatetypesApiController@getIndexApi');
	    Route::get('create', 'Api\TemplatetypesApiController@getCreateApi');
	    Route::post('store', 'Api\TemplatetypesApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\TemplatetypesApiController@getEditApi');
	    Route::post('update/{id}', 'Api\TemplatetypesApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\TemplatetypesApiController@getDeleteApi');
	});
	Route::get('templatetypeexport/{export_type}', 'Api\TemplatetypesApiController@getIndexApi');

    ### Ends - TemplateTypes ### 
    ### Starts - Call History ### 
    Route::get('comhistory', 'Api\CommunicationHistoryApiController@getIndexApi');
    ### Ends - Call History ### 
    ### Starts - Fee Schedule ###
    Route::group(array('prefix' => 'feeschedule'), function() {
	    Route::get('/', 'Api\FeescheduleApiController@getIndexApi');
	    Route::get('create', 'Api\FeescheduleApiController@getCreateApi');
	    Route::post('store', 'Api\FeescheduleApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\FeescheduleApiController@getEditApi');
	    Route::post('update/{id}', 'Api\FeescheduleApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\FeescheduleApiController@getdestroyApi');
	});
    Route::get('feeschedule_file/{type}', 'FeescheduleController@get_feeschedule_file'); // Fee schedule upload sample xls file
    #Export
    // Route::get('feeschedulereports/{export}', 'Api\FeescheduleApiController@getIndexApi');
    Route::get('feeschedulereports/{export}', 'FeescheduleController@getReport');
    ### Ends - Fee Schedule ###
    ### Starts - Help ###
    Route::group(array('prefix' => 'staticpage'), function() {
	    Route::get('/', 'Api\StaticPageApiController@getIndexApi');
	    Route::get('create', 'Api\StaticPageApiController@getCreateApi');
	    Route::post('store', 'Api\StaticPageApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\StaticPageApiController@getEditApi');
	    Route::post('update/{id}', 'Api\StaticPageApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\StaticPageApiController@getDeleteApi');
	});
    ### Ends - Help ###

    //Route::get('schproviderreports/{export}', 'Api\ProviderSchedulerApiController@getIndexApi');
    //Route::get('facilityschedulerreports/{export}', 'Api\FacilitySchedulerApiController@getIndexApi');

    ### Starts - Patient statement ###
    Route::get('patientstatementsettings', 'Api\PatientstatementsettingsApiController@getIndexApi');
    Route::post('patientstatementsettings', 'Api\PatientstatementsettingsApiController@getStoreApi');
    Route::get('patientstatement/getaddress', 'Api\PatientstatementsettingsApiController@getaddressApi');

    Route::get('bulkstatement', 'Api\PatientbulkstatementApiController@getIndexApi');
    Route::post('bulkstatement', 'Api\PatientbulkstatementApiController@getStoreApi');

    Route::get('individualstatement', 'Api\PatientindividualstatementApiController@getIndexApi');
    Route::get('statementhistory', 'Api\PatientindividualstatementApiController@getStatementHistoryApi');
    Route::get('individualpatientlist/{get_name}', 'Api\PatientindividualstatementApiController@getPatientListApi');
    Route::get('individualstatementtype/{patientid}/{type}', 'Api\PatientindividualstatementApiController@getTypeApi');
    Route::get('individualstatementdownload/{filename}/{id}/{existname}', 'Api\PatientindividualstatementApiController@getIndividualDownloadApi');
    Route::get('individualpatientdetails/{patientid}', 'Api\PatientindividualstatementApiController@getPatientDetailsApi');
    ### Ends - Patient Statement ###
    ### Starts - Cheat Sheet ###    
    Route::group(array('prefix' => 'cheatsheet'), function() {
	    Route::get('/', 'Api\CheatsheetApiController@getIndexApi');
	    Route::get('create', 'Api\CheatsheetApiController@getCreateApi');
	    Route::post('store', 'Api\CheatsheetApiController@getStoreApi');
	    Route::get('edit/{id}', 'Api\CheatsheetApiController@getEditApi');
	    Route::post('update/{id}', 'Api\CheatsheetApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\CheatsheetApiController@getDeleteApi');
	});
    Route::get('cheatsheetreports/{export}', 'Api\CheatsheetApiController@getIndexApi');
    ### Ends - Cheat Sheet ###

    

    //Route::pattern('id', '[0-9]+');
    //Route::pattern('insuranceid', '[0-9]+');
    #Common API Check
    Route::post('addresscheck', 'Api\CommonApiController@checkUSPSAddressCheck');
    //Author: baskar
    //Start
    Route::post('adjustment_validation', 'Api\CommonApiController@adjustment_validation');
    Route::post('practice/security_code/{id}', 'Api\CommonApiController@security_code_generate');
    Route::post('set_practice', 'Api\CommonApiController@setPractice');
    Route::post('lastIcd', 'Api\CommonApiController@lastIcd');
    //End

    Route::post('npicheck', 'Api\CommonApiController@npiCheck');

    #Practice page stats count 
    Route::get('practiceprovidercount', 'Api\PracticesApiController@getProviderCount');
    Route::get('practicefacilitycount', 'Api\PracticesApiController@getFacilityCount');
    Route::get('practicepatientcount', 'Api\PracticesApiController@getPatientrCount');
    Route::get('practicevisitscount', 'Api\PracticesApiController@getVistiCount');
    Route::get('practiceclaimscount', 'Api\PracticesApiController@getClaimCount');
    Route::get('practicecollectionscount', 'Api\PracticesApiController@getCollectionCount');

    ### Starts - Superbills ###
    Route::group(array('prefix' => 'superbills'), function() {
	    Route::get('/', 'Api\SuperbillsApiController@getIndexApi');
	    Route::get('create', 'Api\SuperbillsApiController@getCreateApi');
	    Route::get('{id}/edit', 'Api\SuperbillsApiController@getEditApi');
	    Route::post('store', 'Api\SuperbillsController@getTemplatestoreApi');
	    Route::get('show/{id}', 'Api\SuperbillsController@getShowApi');
	    Route::post('store', 'Api\SuperbillsApiController@getTemplatestoreApi');
	    Route::post('template/show', 'Api\SuperbillsApiController@getTemplateshowApi');
	    Route::get('template/delete/{id}', 'Api\SuperbillsApiController@getDeleteApi');
	    Route::post('template/search', 'Api\SuperbillsApiController@getTemplatesearchApi');	    
	});

	Route::get('superbillreports/{export}', 'Api\SuperbillsApiController@getIndexApi');
    ### Ends - Superbills ###       
    ###Starts Patientbiling Api
    Route::get('getreferringprovider/{patient_id?}/{query?}', 'Patients\Api\BillingApiController@getReferringproviderApi');
    
    Route::get('checkmodifier/{mod_code}', 'Patients\Api\BillingApiController@checkModifierexistApi');
    //Route::get('checkcptexist/{cpt_hcpcs}', 'Patients\Api\BillingApiController@checkCPTexistApi');
    ###Ends Patientbiling Api 
    ###Starts Patientbiling Api
    Route::get('getreferringprovider/{patient_id?}/{query?}', 'Patients\Api\BillingApiController@getReferringproviderApi');
    ###Ends Patientbiling Api
    ###Starts Patientbiling Api
    Route::get('getinsurance_details_modal/{serach_keyword}/{serach_category}', 'Patients\Api\PatientApiController@getinsurance_details_modal');
    ###Ends Patientbiling Api
    ### Starts - HoldOption ###
    #HoldOption
    Route::group(array('prefix' => 'holdoption'), function() {
    	Route::get('{export}', 'Api\HoldOptionApiController@getIndexApi');
	    Route::get('/', 'Api\HoldOptionApiController@getIndexApi');
	    Route::get('create', 'Api\HoldOptionApiController@getCreateApi');
	    Route::post('store', 'Api\HoldOptionApiController@getStoreApi');
	    Route::get('show/{id}', 'Api\HoldOptionApiController@getshowApi');
	    Route::get('edit/{id}', 'Api\HoldOptionApiController@getEditApi');
	    Route::post('update/{id}', 'Api\HoldOptionApiController@getUpdateApi');
	    Route::get('delete/{id}', 'Api\HoldOptionApiController@getDeleteApi');
	    
    });

    ### Ends - holdoption ###
    ### Starts - reason for Visit ###
    Route::resource('reason', 'ReasonController');
    Route::get('reason/{reason_id}/delete', 'ReasonController@destroy');
    //Route::get('reasonreports/{export}', 'Api\ReasonApiController@getIndexApi');
    ### Ends - Reason for visits ###
    ### Starts - Questionnaries API ###

    Route::resource('questionnaire/template', 'Api\QuestionnariesTemplateApiController');
    Route::resource('questionnaires', 'Api\QuestionnariesApiController');

    Route::group(array('prefix' => 'questionnaire'), function() {
	    Route::get('template/{id}/delete', 'Api\QuestionnariesTemplateApiController@getDestroyApi');
	    Route::get('templateexport/{type}', 'Api\QuestionnariesTemplateApiController@getIndexApi');
	    Route::get('getvalidation/{type}/{id?}', 'Api\QuestionnariesTemplateApiController@getValidationApi');
	    Route::post('template/quesansdelete', 'Api\QuestionnariesTemplateApiController@getQuesansdeleteApi');
	    Route::get('{id}/delete', 'Api\QuestionnariesApiController@getDestroyApi');
	});    
    
    Route::get('questionnaireexport/{type}', 'Api\QuestionnariesApiController@getIndexApi');

    ### Ends - Questionnaries API ###   
    ####Insurance type Start ###

    Route::resource('insurancetypes', 'InsuranceTypesController');
    Route::get('insurancetypes/{insurancetypes}/delete', 'InsuranceTypesController@destroy');
    Route::get('insurancetypereports/{export}', 'Api\InsuranceTypesApiController@getIndexApi');

    ### Insurancetype End ###

    Route::get('admin/faqreports/{export}', 'Medcubics\Api\FaqApiController@getIndexApi');

	#Insurance Master Get datatable values
	Route::get('getinsurancetablevalues', 'Api\InsuranceApiController@getinsurancetablevalues');
	Route::get('getschedulertablevalues/{all?}', 'Scheduler\AppointmentListController@schedulerTableData');
	Route::get('schedulerlistreport/{export}/{all?}/{app?}',  'Scheduler\AppointmentListController@schedulerTableDataExport');

	#Insurance Optum Get datatable values
	Route::get('getinsuranceoptumvalues', 'Api\InsuranceApiController@getinsuranceoptumvalues');

	//ICD-9 Get datatable values
	Route::get('geticd9tablevalues', 'Api\Icd09ApiController@geticd9tablevalues');

	Route::get('edireports/{export}', 'Api\ClearingHouseApiController@getIndexApi');


	//Route::get('schproviderlistingreports/{id}/{export}', 'Api\ProviderSchedulerApiController@viewProviderSchedulerApi');

	#Export
	//Route::get('schedulerfacilityreports/{id}/{export}', 'Api\FacilitySchedulerApiController@viewFacilitySchedulerApi');

	Route::get('superbillreports/{export}', 'Api\SuperbillsApiController@getIndexApi');

	Route::get('holdoptionreports/{export}', 'Api\HoldOptionApiController@getIndexApi');

	Route::get('reasonreports/{export}', 'Api\ReasonApiController@getIndexApi');

	Route::get('insurancetypereports/{export}', 'Api\InsuranceTypesApiController@getIndexApi');

	Route::get('adjustmentreason/{export}', 'Api\AdjustmentReasonApiController@getIndexApi');

	Route::get('statementcategory/{export}', 'Api\StatementCategoryApiController@getIndexApi');
	    
	Route::get('statementholdreason/{export}', 'Api\StatementHoldReasonApiController@getIndexApi');
	    
	Route::get('questionnaire/templateexport/{type}', 'Api\QuestionnariesTemplateApiController@getIndexApi');
	Route::get('questionnaire/getvalidation/{type}/{id?}', 'Api\QuestionnariesTemplateApiController@getValidationApi');

	Route::get('questionnaireexport/{type}', 'Api\QuestionnariesApiController@getIndexApi');

	Route::get('clinicalcategoriesreports/{export}', 'Api\ClinicalNotesCategoryApiController@getIndexApi');


    ### End - Profile API ###
    Route::group(array('prefix' => 'profile'), function() {
        ### Starts - Profile Events ###    
        Route::group(array('prefix' => 'calendar'), function() {
	        Route::post('event/create', 'Profile\Api\EventController@getEventCreate');
	        Route::post('event/update/{id}', 'Profile\Api\EventController@getEventCreate');
	        Route::get('/event/delete/{id}', 'Profile\Api\EventController@getEventDelete');
	        Route::get('/', 'Profile\Api\EventController@index');
	        Route::get('/show', 'Profile\Api\EventController@getCalendarshow');
	        Route::get('/events/add', 'Profile\Api\EventController@getCalendarAdd');
	        Route::get('/event/edit/{id}', 'Profile\Api\EventController@getCalendaredit');
	        Route::get('event/show/{timestamp}', 'Profile\Api\EventController@getCalendarshowTimestamp');
	    });
        ### Ends - Profile Events ###
        ### Start - Profile Blog Api URL's
        Route::get('blogs/{order?}/{keyword?}', 'Profile\Api\BlogApiController@blogListingApi');
        Route::get('userblog/{order?}/{keyword?}', 'Profile\Api\BlogApiController@getIndexApi');

        Route::group(array('prefix' => 'blog'), function() {
	        Route::get('favourite', 'Profile\Api\BlogApiController@getFavouriteApi');
	        Route::get('getblog', 'Profile\Api\BlogApiController@blogListingApi');
	        Route::post('comments', 'Profile\Api\BlogApiController@CommentApi');
	        Route::get('getcomments', 'Profile\Api\BlogApiController@getCommentApi');
	        Route::get('deletecomments/{id}/{blogid}', 'Profile\Api\BlogApiController@deleteCommentsApi');
	        Route::post('replycomments', 'Profile\Api\BlogApiController@CommentReplyApi');
	        Route::get('getreplycomments', 'Profile\Api\BlogApiController@getReplyCommentsApi');
	        Route::get('delreplycomments/{replyid}/{parentid}', 'Profile\Api\BlogApiController@deleteReplyCommentsApi');
	        Route::get('commentsfavourite', 'Profile\Api\BlogApiController@getCommentFavouriteApi');
	        Route::get('/', 'Profile\Api\BlogApiController@getIndexApi');
	        Route::get('{id}', 'Profile\Api\BlogApiController@getShowApi');
	        Route::get('create', 'Profile\Api\BlogApiController@getCreateApi');
	        Route::post('{id}/store', 'Profile\Api\BlogApiController@getStoreApi');
	        Route::get('{id}/edit', 'Profile\Api\BlogApiController@getEditApi');
	        Route::post('{id}/update', 'Profile\Api\BlogApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Profile\Api\BlogApiController@getDestroyApi');
	        ### End - Profile Blog Api URL's     
	        ###BlogGroupApiController   
	        Route::get('groupblog', 'Profile\Api\BlogGroupApiController@groupblog');
	        Route::get('addgroup/create', 'Profile\Api\BlogGroupApiController@CreateGroupApi');
	        Route::get('addgroup/store/{id}', 'Profile\Api\BlogGroupApiController@storeGroupApi');
	        Route::get('group/view/{id}', 'Profile\Api\BlogGroupApiController@ViewGroupApi');
	        Route::get('addgroup/edit/{id}', 'Profile\Api\BlogGroupApiController@editGroupApi');
	        Route::any('group/update/{id}', 'Profile\Api\BlogGroupApiController@updateGroupApi');
	    });
        ### Starts - Profile Message Api URL's
        ### Starts - Mailbox ###

        Route::group(array('prefix' => 'maillist'), function() {
	        Route::get('/', 'Profile\Api\MailboxApiController@getMaillistApi');
	        Route::get('show/{mail_id}', 'Profile\Api\MailboxApiController@getShowmailApi');
	        Route::get('sent', 'Profile\Api\MailboxApiController@getSendMaillistApi');
	        Route::get('draft', 'Profile\Api\MailboxApiController@getDraftMaillistApi');
	        Route::get('trash', 'Profile\Api\MailboxApiController@getTrashMaillistApi');
	        Route::get('other/{label_id?}', 'Profile\Api\MailboxApiController@getotherLabelMaillistApi');
	        Route::get('sent/{mail_id?}', 'Profile\Api\MailboxApiController@getSendMailViewDetApi');
	        Route::get('inbox/{mail_id?}', 'Profile\Api\MailboxApiController@getInboxMailViewDetApi');
	        Route::get('draft/{mail_id?}', 'Profile\Api\MailboxApiController@getdraftMailViewDetApi');
	        Route::get('category/{mail_id?}', 'Profile\Api\MailboxApiController@getInboxMailViewDetApi');
	        Route::get('trash/{mail_id?}', 'Profile\Api\MailboxApiController@getTrashMailViewDetApi');
	        Route::get('settings', 'Profile\Api\MailboxApiController@MailSettingsApi');
	        Route::post('settings/store', 'Profile\Api\MailboxApiController@MailSettingsstoreApi');
	        Route::get('composemail', 'Profile\Api\MailboxApiController@getComposemail');
	        Route::get('replymailprocess/{mail_id}/{reply_all_type?}', 'Profile\Api\MailboxApiController@replymailprocess');
	        Route::post('keywordsearch', 'Profile\Api\MailboxApiController@getKeywordsearchApi');
	        Route::post('keywordfilter', 'Profile\Api\MailboxApiController@getKeywordfilterApi');
	        Route::get('{status_read}/{page}/{order}/{order_by}/{labe_id?}', 'Profile\Api\MailboxApiController@getUnreadmailApi');
	    });

        Route::post('mailsendprocess', 'Profile\Api\MailboxApiController@mailsendprocess');
        Route::get('replymailprocess/{mail_id}/{reply_all_type?}', 'Profile\Api\MailboxApiController@replymailprocess');
        Route::post('draftmailprocess', 'Profile\Api\MailboxApiController@draftmailprocess');
        Route::post('newmaillabeladd', 'Profile\Api\MailboxApiController@newmaillabeladd');
        Route::post('msgmoveprocess', 'Profile\Api\MailboxApiController@msgmoveprocess');
        Route::post('message_del_list', 'Profile\Api\MailboxApiController@message_del_list');
        Route::post('msglist_applyprocess', 'Profile\Api\MailboxApiController@msglist_applyprocess');
        Route::post('message_stared_list', 'Profile\Api\MailboxApiController@message_stared_list');

        
        ### Ends - Mailbox ###       

        Route::post('messagesendprocess', 'Profile\Api\MessageApiController@messagesendprocess');
        Route::post('messageinsert', 'Profile\Api\MessageApiController@messageinsert');
    });
	### End - Profile API ###

    
    ### Starts - Patients API ###
    Route::group(array('prefix' => 'patients/{patient_id}'), function() {

        Route::resource('ledger', 'Patients\Api\LedgerApiController');

        ### Starts - Patient Archive Insurance API ###
        Route::get('/archiveinsurance/move/insurance/{arc_id}', 'Patients\Api\PatientApiController@moveArchchivetoInsuranceApi');
        ### End - Patient Archive Insurance API ###
        ### Starts - Patient Correspondence API ###
        Route::get('/correspondence', 'Patients\Api\CorrespondenceApiController@gettemplateListApi');
        Route::get('/correspondence/{temp_id}/edit', 'Patients\Api\CorrespondenceApiController@getCreateApi');
        Route::post('/correspondence/send', 'Patients\Api\CorrespondenceApiController@getSendApi');
        Route::get('/correspondencehistory', 'Patients\Api\CorrespondenceApiController@getindexApi');
        Route::get('/correspondencehistory/{cor_id}', 'Patients\Api\CorrespondenceApiController@getshowApi');
        ### End - Patient Correspondence API ###
        ### Starts - Patient Clinical Notes API ###
        Route::resource('/clinicalnotes', 'Patients\Api\ClinicalNotesApiController');
        Route::get('/clinicalnotes/{id}/claimdetail', 'Patients\Api\ClinicalNotesApiController@claimdetailsApi');
        Route::get('/clinicalnotes/delete/{id}', 'Patients\Api\ClinicalNotesApiController@getDestroyApi');
        Route::get('/clinicalnotes/export/{export}', 'Patients\Api\ClinicalNotesApiController@getIndexApi');
        ### End - Patient Clinical Notes API ###
        // Patient Questionnaries starts
        Route::get('/questionnaires', 'Patients\Api\PatientApiController@getQuestionnairesApi');
        Route::get('questionnairesreport/{id}/{export}', 'Patients\Api\PatientApiController@getQuestionnairesApi');
       //  Route::get('patientstatements', function(){
         //    return "fdsfds";
         //});
        
    });
    ### End - Patients API ###

    #Stats slection change API
    Route::get('stats/listchange/{data}', 'Api\StatsApiController@getSelectlistChangeApi');
    ### Ends - slection change API ###
    ###Starts Switchpatient Api
    Route::post('getswitchpatient_details_modal', 'Patients\Api\PatientApiController@getswitchpatient_details_modal');
    ###Ends Switchpatient Api
    ### Start - User API settings
    Route::get('getpracticeuserapi', 'Api\UserApiSettingsApiController@getPracticeUserApi');
    Route::get('getpracticedisabledapi', 'Api\ApiSettingsApiController@getDisabledUserApi');

    ### End  - User API settings
    #Patient starts    
    Route::get('profilepicture/{type}', 'Patients\Api\PatientApiController@showProfilePicture');
    #Patient ends 

    Route::group(array('prefix' => 'admin'), function() {

        #Customer
        Route::group(array('prefix' => 'customer'), function() {
	        Route::get('/', 'Medcubics\Api\CustomerApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\CustomerApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\CustomerApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\CustomerApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\CustomerApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\CustomerApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\CustomerApiController@getDeleteApi');
	    });
        #Export
        Route::get('customermedcubics/{export}', 'Medcubics\Api\CustomerApiController@getIndexApi');

        #Customer -> Practice
        Route::group(array('prefix' => 'customer/{id}/customerpractices'), function() {
	        Route::get('/', 'Medcubics\Api\CustomerPracticesApiController@getIndexApi');
	        Route::get('{customerpractices}', 'Medcubics\Api\CustomerPracticesApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\CustomerPracticesApiController@getCreateApi');
	        Route::post('{customerpractices}/store', 'Medcubics\Api\CustomerPracticesApiController@getStoreApi');
	        Route::get('{customerpractices}/edit', 'Medcubics\Api\CustomerPracticesApiController@getEditApi');
	        Route::post('{customerpractices}/update', 'Medcubics\Api\CustomerPracticesApiController@getUpdateApi');
	        Route::get('delete/{customerpractices}', 'Medcubics\Api\CustomerPracticesApiController@getDeleteApi');
	    });
        #Export
        Route::get('customerpracticesmedcubics/{id}/{export}', 'Medcubics\Api\CustomerPracticesApiController@getIndexApi');

        #Customer -> Practice -> Provider
        Route::group(array('prefix' => 'customer/{customer_id}/practice/{practice_id}/providers'), function() {
	        Route::get('/', 'Medcubics\Api\PracticeProvidersApiController@getIndexApi');
	        Route::get('{providers}', 'Medcubics\Api\PracticeProvidersApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\PracticeProvidersApiController@getCreateApi');
	        Route::post('{providers}/store', 'Medcubics\Api\PracticeProvidersApiController@getStoreApi');
	        Route::get('{providers}/edit', 'Medcubics\Api\PracticeProvidersApiController@getEditApi');
	        Route::post('{providers}/update', 'Medcubics\Api\PracticeProvidersApiController@getUpdateApi');
	        Route::get('{id}/delete', 'Medcubics\Api\PracticeProvidersApiController@getDeleteApi');
	    });
        #Export
        Route::get('providerreports/{id}/{export}', 'Medcubics\Api\PracticeProvidersApiController@getIndexApi');

        #Customer -> Users
        Route::group(array('prefix' => 'customer/{id}/customerusers'), function() {
	        Route::get('/', 'Medcubics\Api\CustomerUsersApiController@getIndexApi');
	        Route::get('{customerusers}', 'Medcubics\Api\CustomerUsersApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\CustomerUsersApiController@getCreateApi');
	        Route::post('{customerusers}/store', 'Medcubics\Api\CustomerUsersApiController@getStoreApi');
	        Route::get('{customerusers}/edit', 'Medcubics\Api\CustomerUsersApiController@getEditApi');
	        Route::post('{customerusers}/update', 'Medcubics\Api\CustomerUsersApiController@getUpdateApi');
	        Route::get('delete/{customerusers}', 'Medcubics\Api\CustomerUsersApiController@getDeleteApi');
	    });
        #Export
        Route::get('customerusersmedcubics/{id}/{export}', 'Medcubics\Api\CustomerUsersApiController@getIndexApi');

        #Customer -> Notes
        Route::group(array('prefix' => 'customer/{id}/customernotes'), function() {
	        Route::get('/', 'Medcubics\Api\CustomerNotesApiController@getIndexApi');
	        Route::get('{customernotes}', 'Medcubics\Api\CustomerNotesApiController@getShowApi');
	        Route::get('customecreate', 'Medcubics\Api\CustomerNotesApiController@getCreateApi');
	        Route::post('{customernotes}/store', 'Medcubics\Api\CustomerNotesApiController@getStoreApi');
	        Route::get('{customernotes}/edit', 'Medcubics\Api\CustomerNotesApiController@getEditApi');
	        Route::post('{customernotes}/update', 'Medcubics\Api\CustomerNotesApiController@getUpdateApi');
	        Route::get('delete/{customernotes}', 'Medcubics\Api\CustomerNotesApiController@getDeleteApi');
	    });
        #Export
        #Scheduler Page
        Route::get('scheduler/list', 'Scheduler\Api\ListingApiController@getIndexApi');
        Route::post('scheduler/keywordsearch', 'Scheduler\Api\ListingApiController@getIndexApi');

        #Insurance
        Route::group(array('prefix' => 'insurance'), function() {
	        Route::get('/', 'Medcubics\Api\InsuranceApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\InsuranceApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\InsuranceApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\InsuranceApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\InsuranceApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\InsuranceApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\InsuranceApiController@getUpdateApi');
	    });
        #Export
        Route::get('insurancereports/{export}', 'Medcubics\Api\InsuranceApiController@getIndexApi');

        #Insurance -> Overrides
        Route::group(array('prefix' => 'insurance/{insuranceid}/insuranceoverrides'), function() {
	        Route::get('/', 'Medcubics\Api\InsuranceOverridesApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\InsuranceOverridesApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\InsuranceOverridesApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\InsuranceOverridesApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\insuranceOverridesApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\InsuranceOverridesApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\InsuranceOverridesApiController@getUpdateApi');
	    });
        #Export
        Route::get('insuranceoverridesreports/{id}/{export}', 'Medcubics\Api\InsuranceOverridesApiController@getIndexApi');

        #Insurance Master
        Route::group(array('prefix' => 'insurance'), function() {
	        Route::get('/', 'Medcubics\Api\InsuranceApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\InsuranceApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\InsuranceApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\InsuranceApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\InsuranceApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\InsuranceApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\InsuranceApiController@getUpdateApi');
	    });
        Route::get('getoptionvalues', 'Medcubics\Api\InsuranceApiController@getoptionvalues');
        #Export
        Route::get('insurancereports/{export}', 'Medcubics\Api\InsuranceApiController@getIndexApi');
        #Insurance Master Get datatable values
        Route::get('getinsurancevaluesAdmin', 'Medcubics\Api\InsuranceApiController@getinsurancevaluesAdmin');
        Route::get('getinsuranceoptumvaluesAdmin', 'Medcubics\Api\InsuranceApiController@getinsuranceoptumvaluesAdmin');

        #Insurance Master -> Overrides
        Route::group(array('prefix' => 'insurance/{insuranceid}/insuranceoverrides'), function() {
	        Route::get('/', 'Medcubics\Api\InsuranceOverridesApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\InsuranceOverridesApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\InsuranceOverridesApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\InsuranceOverridesApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\InsuranceOverridesApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\InsuranceOverridesApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\InsuranceOverridesApiController@getUpdateApi');
	    });
        #Export
        Route::get('insuranceoverridesreports/{insurance_id}/{export}', 'Medcubics\Api\InsuranceOverridesApiController@getIndexApi');

        #Insurance Master -> Appeal Address
        Route::group(array('prefix' => 'insurance/{insuranceid}/insuranceappealaddress'), function() {
	        Route::get('/', 'Medcubics\Api\InsuranceAppealAddressApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\InsuranceAppealAddressApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\InsuranceAppealAddressApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\InsuranceAppealAddressApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\InsuranceAppealAddressApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\InsuranceAppealAddressApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\InsuranceAppealAddressApiController@getUpdateApi');
	    });
        #Export
        Route::get('insuranceappealaddress/{id}/{export}', 'Medcubics\Api\InsuranceAppealAddressApiController@getIndexApi');

        #Insurance Types
        Route::group(array('prefix' => 'insurancetypes'), function() {
	        Route::get('/', 'Medcubics\Api\InsuranceTypesApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\InsuranceTypesApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\InsuranceTypesApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\InsuranceTypesApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\InsuranceTypesApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\InsuranceTypesApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\InsuranceTypesApiController@getUpdateApi');
	    });
        #Export
        Route::get('insurancetypereports/{export}', 'Medcubics\Api\InsuranceTypesApiController@getIndexApi');

        #Modifiers Level 1
        Route::group(array('prefix' => 'modifierlevel1'), function() {
	        Route::get('/', 'Medcubics\Api\ModifierLevelApiController@getIndexApi');
	        Route::get('create', 'Medcubics\Api\ModifierLevelApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\ModifierLevelApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\ModifierLevelApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\ModifierLevelApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\ModifierLevelApiController@getDeleteApi');
	        Route::get('{id}', 'Medcubics\Api\ModifierLevelApiController@getShowApi');
	    });
        #Export
        Route::get('modifierreports/{export}', 'Medcubics\Api\ModifierApiController@getIndexApi');

        #Modifiers Level 2
        Route::group(array('prefix' => 'modifierlevel2'), function() {
	        Route::get('/', 'Medcubics\Api\ModifierLevelApiController@getIndexApi');
	        Route::get('create', 'Medcubics\Api\ModifierLevelApiController@getCreateApi');
	        Route::post('store', 'Medcubics\Api\ModifierLevelApiController@getStoreApi');
	        Route::get('edit/{id}', 'Medcubics\Api\ModifierLevelApiController@getEditApi');
	        Route::post('update/{id}', 'Medcubics\Api\ModifierLevelApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\ModifierLevelApiController@getDeleteApi');
	        Route::get('{id}', 'Medcubics\Api\ModifierLevelApiController@getShowApi');
	    });
        #Export
        Route::get('modifierlevelreports/{export}', 'Medcubics\Api\ModifierLevelApiController@getIndexApi');

        #Fee Schedule
        Route::group(array('prefix' => 'feeschedule'), function() {
	        Route::get('/', 'Medcubics\Api\FeescheduleApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\FeescheduleApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\FeescheduleApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\FeescheduleApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\FeescheduleApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\FeescheduleApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\FeescheduleApiController@getDeleteApi');
	    });
        #Export
        Route::get('feeschedulereportsmedcubics/{export}', 'Medcubics\Api\FeescheduleApiController@getIndexApi');

        #Codes
        Route::group(array('prefix' => 'code'), function() {
	        Route::get('/', 'Medcubics\Api\CodeApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\CodeApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\CodeApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\CodeApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\CodeApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\CodeApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\CodeApiController@getDeleteApi');
	    });
        #Export
        Route::get('codereportsmedcubics/{export}', 'Medcubics\Api\CodeApiController@getIndexApi');

        #CPT
        Route::group(array('prefix' => 'cpt'), function() {
	        Route::get('/', 'Medcubics\Api\CptApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\CptApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\CptApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\CptApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\CptApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\CptApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\CptApiController@getDeleteApi');
	    });
        Route::get('advancedsearchcpt', 'CptController@AdvancedSearchIndex');

        #Export
        Route::get('cptreportsmedcubics/{export}', 'Medcubics\Api\CptApiController@getIndexApi');
        #CPT Get datatable values in admin
        Route::get('getcptvaluesAdmin', 'Medcubics\Api\CptApiController@getcptvaluesAdmin');

        #ICD-10
        Route::group(array('prefix' => 'icd'), function() {
	        Route::get('/', 'Medcubics\Api\IcdApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\IcdApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\IcdApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\IcdApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\IcdApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\IcdApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\IcdApiController@getDeleteApi');
	    });
        #Export
        Route::get('icdreportsmedcubics/{export}', 'Medcubics\Api\IcdApiController@getIndexApi');
        #Get icd10 table values for admin
        Route::get('geticd10valuesAdmin', 'Medcubics\Api\IcdApiController@geticd10valuesAdmin');

        #Speciality
        Route::group(array('prefix' => 'speciality'), function() {
	        Route::get('/', 'Medcubics\Api\SpecialityApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\SpecialityApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\SpecialityApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\SpecialityApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\SpecialityApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\SpecialityApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\SpecialityApiController@getUpdateApi');
	    });
        #Export
        Route::get('specialityreports/{export}', 'Medcubics\Api\SpecialityApiController@getIndexApi');

        #Taxanomy
        Route::group(array('prefix' => 'taxanomy'), function() {
	        Route::get('/', 'Medcubics\Api\TaxanomyApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\TaxanomyApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\TaxanomyApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\TaxanomyApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\TaxanomyApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\TaxanomyApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\TaxanomyApiController@getUpdateApi');
	    });
        #Export
        Route::get('taxanomyreports/{export}', 'Medcubics\Api\TaxanomyApiController@getIndexApi');

        #Place of Service
        Route::group(array('prefix' => 'placeofservice'), function() {
	        Route::get('/', 'Medcubics\Api\PosApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\PosApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\PosApiController@getCreateApi');
	        Route::get('{id}/edit', 'Medcubics\Api\PosApiController@getEditApi');
	        Route::get('delete/{id}', 'Medcubics\Api\PosApiController@getDeleteApi');
	        Route::post('{id}/store', 'Medcubics\Api\PosApiController@getStoreApi');
	        Route::post('{id}/update', 'Medcubics\Api\PosApiController@getUpdateApi');
	    });
        #Export
        Route::get('placeofservicereports/{export}', 'Medcubics\Api\PosApiController@getIndexApi');

        #ID Qualifiers
        Route::group(array('prefix' => 'qualifiers'), function() {
	        Route::get('/', 'Medcubics\Api\QualifierApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\QualifierApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\QualifierApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\QualifierApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\QualifierApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\QualifierApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\QualifierApiController@getDeleteApi');
	    });
        #Export
        Route::get('qualifierreportsmedcubics/{export}', 'Medcubics\Api\QualifierApiController@getIndexApi');

        #Provider Degree
        Route::group(array('prefix' => 'providerdegree'), function() {
	        Route::get('/', 'Medcubics\Api\ProviderDegreeApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\ProviderDegreeApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\ProviderDegreeApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\ProviderDegreeApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\ProviderDegreeApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\ProviderDegreeApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\ProviderDegreeApiController@getDeleteApi');
	    });
        #Export
        Route::get('providerdegreereportsmedcubics/{export}', 'Medcubics\Api\ProviderDegreeApiController@getIndexApi');

        #Roles
        Route::get('medcubicsrole', 'Medcubics\Api\RoleApiController@getIndexApi');
        Route::get('practicerole', 'Medcubics\Api\RoleApiController@getPracticePermissionApi');

        Route::group(array('prefix' => 'role'), function() {
	        Route::get('{id}', 'Medcubics\Api\RoleApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\RoleApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\RoleApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\RoleApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\RoleApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\RoleApiController@getDestroyApi');
	    });
        #Practice Role Export
        Route::get('practicerole/{export}', 'Medcubics\Api\RoleApiController@getPracticePermissionApi');
        #Medcubics role export
        Route::get('medcubicsrole/{export}', 'Medcubics\Api\RoleApiController@getIndexApi');

        Route::get('adminpermission/{id}', 'Medcubics\Api\AdminpermissionApiController@getCreateApi');
        Route::post('adminpermission/store', 'Medcubics\Api\AdminpermissionApiController@getStoreApi');

        Route::get('setpagepermissions/{id}/edit', 'Medcubics\Api\SetPagepermissionsApiController@getEditApi');
        Route::post('setpagepermissions/{id}/update', 'Medcubics\Api\SetPagepermissionsApiController@getUpdateApi');
        
        Route::get('modulepermissions/{id}/edit', 'Medcubics\Api\ModulePermissionsApiController@getEditApi');
        Route::get('modulepermissions/{id}/update', 'Medcubics\Api\ModulePermissionsApiController@getUpdateApi');
        
        #Admin Users
        Route::group(array('prefix' => 'adminuser'), function() {
	        Route::get('/', 'Medcubics\Api\AdminuserApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\AdminuserApiController@getShowApi');
	        Route::get('create', 'Medcubics\Api\AdminuserApiController@getCreateApi');
	        Route::post('{id}/store', 'Medcubics\Api\AdminuserApiController@getStoreApi');
	        Route::get('{id}/edit', 'Medcubics\Api\AdminuserApiController@getEditApi');
	        Route::post('{id}/update', 'Medcubics\Api\AdminuserApiController@getUpdateApi');
	        Route::get('delete/{id}', 'Medcubics\Api\AdminuserApiController@getDestroyApi');
	    });
        #Export 
        Route::get('adminuserexport/{export}', 'Medcubics\Api\AdminuserApiController@getIndexApi');

        // User Activity
        Route::get('useractivity', 'Medcubics\Api\UserActivityApiController@getIndexApi');
        Route::post('useractivity/{id}/store', 'Medcubics\Api\UserActivityApiController@getUserRecordApi');
        Route::get('setuserpractice/{id}', 'Medcubics\Api\CustomerApiController@setPracticeApi');

				
        // Api List
        #Export 
        Route::get('apilistreports/{export}', 'Medcubics\Api\ApiListApiController@getIndexApi');

        ### Starts - Manage Ticket ###
        //Route::get('manageticket/delete/{id}', 'Medcubics\Api\ManageticketApiController@getDeleteApi');
        Route::group(array('prefix' => 'manageticket'), function() {
	        Route::get('/', 'Medcubics\Api\ManageticketApiController@getIndexApi');
	        Route::get('{id}', 'Medcubics\Api\ManageticketApiController@getShowApi');
	        Route::get('{id}/edit', 'Medcubics\Api\ManageticketApiController@getEditApi');
	        Route::post('update/{id}', 'Medcubics\Api\ManageticketApiController@getUpdateApi');
	    });

        Route::get('assignticket/{ticketid}/{userid}', 'Medcubics\Api\ManageticketApiController@assignTicketApi');
        Route::get('getmedcubicsuserlist/{ticketid}/{id?}', 'Medcubics\Api\ManageticketApiController@getUserListApi');
        Route::get('managemyticket', 'Medcubics\Api\ManageticketApiController@manageMyticketApi');
        Route::get('createnewticket', 'Medcubics\Api\AdminTicketApiController@getIndexApi');
        Route::post('createnewticket', 'Medcubics\Api\AdminTicketApiController@postTicketApi');
        ### End Manage Ticket end###
        ### Starts - EDI ###
        Route::resource('edi', 'Medcubics\Api\ClearingHouseApiController');
        Route::get('edi/delete/{id}', 'Medcubics\Api\ClearingHouseApiController@getDeleteApi');
        Route::get('edireports/{export}', 'Medcubics\Api\ClearingHouseApiController@getIndexApi');
        ### Ends - EDI ###  
    });
    ### Ends - Admin Api URL's

	
    ### Patient Api URL starts       
    Route::group(array('prefix' => 'patients'), function() {
        //Eligibility API URLs Starts
        Route::get('/{id}/eligibility', 'Patients\Api\PatientEligibilityApiController@getindexApi');
        Route::get('/{id}/edi_eligibility', 'Patients\Api\PatientEligibilityApiController@getindexedi_eligibilityApi'); // Patient id passed here
        Route::get('/{id}/eligibility/{eligibility_id}/delete', 'Patients\Api\PatientEligibilityApiController@getDeleteApi');
        
        Route::group(array('prefix' => 'eligibility'), function() {
	        Route::get('create/{id}', 'Patients\Api\PatientEligibilityApiController@getCreateApi'); // Patient id passed here
	        Route::get('showpdf/{type}/{id}', 'Patients\Api\PatientEligibilityApiController@getshowpdfApi');
	        Route::post('store', 'Patients\Api\PatientEligibilityApiController@getstoreApi');
	        Route::get('show/{id}', 'Patients\Api\PatientEligibilityApiController@getshowApi');
	        Route::get('edit/{id}', 'Patients\Api\PatientEligibilityApiController@geteditApi');
	        Route::post('update/{id}', 'Patients\Api\PatientEligibilityApiController@getupdateApi');
	    });
        
        //Eligibility API URLs ends
        //Billing Api Urls Starts here
        Route::get('/{id}/billing', 'Patients\Api\BillingApiController@getIndexApi');
        Route::get('/{id}/billing/create/{claim_id?}', 'Patients\Api\BillingApiController@getCreateApi');

        Route::get('billing', 'Patients\Api\BillingApiController@getStoreApi');
        Route::get('/billing/delete/{id}', 'Patients\BillingApiController@getDeleteApi');

        Route::get('{patient_id}/billing_authorization/', 'Patients\Api\BillingApiController@getCreateauthorizationApi');
        Route::post('billing_authorization/', 'Patients\Api\BillingApiController@getStoreAuthApi');
        Route::get('popuppayment/{id}/', 'Patients\Api\BillingApiController@getPaymentDetail');
        Route::get('cms/{id}/', 'Patients\Api\BillingApiController@getCmsDetailApi'); // This page needs design not integrated
        Route::get('getselectbasedvalues/{select_id}/{model}', 'Patients\Api\BillingApiController@getApiselectbasedvalue');
        Route::get('getproviderdetail/{value}/{type}', 'Patients\Api\BillingApiController@getproviderdetail');
        Route::post('billing_employer/', 'Patients\Api\BillingApiController@getStoreEmployerApi');
        Route::post('billing_provider/', 'Patients\Api\BillingApiController@getStoreReferringProviderApi');
        Route::get('getexistingdosdetail/{patient_id}/{dos}', 'Patients\Api\BillingApiController@checkExistingDosApi');

        Route::group(array('prefix' => 'claimdetail'), function() {
	        Route::get('/create/{patient_id}', "Patients\Api\ClaimDetailApiController@getCreateApi");
	        Route::post('/', "Patients\Api\ClaimDetailApiController@getStoreApi");
	        Route::get('edit/{id}', "Patients\Api\ClaimDetailApiController@getEditApi");
	        Route::post('update/{id}', "Patients\Api\ClaimDetailApiController@getUpdateApi");
	    });

        Route::group(array('prefix' => 'claimbilling'), function() {
	        Route::get('create/{patient_id}', "Patients\Api\ClaimAmbulanceBillingApiController@getCreateApi");
	        Route::post('/', "Patients\Api\ClaimAmbulanceBillingApiController@getStoreApi");
	        Route::get('edit/{id}', "Patients\Api\ClaimAmbulanceBillingApiController@getEditApi");
	        Route::post('update/{id}', "Patients\Api\ClaimAmbulanceBillingApiController@getUpdateApi");
	    });

        Route::group(array('prefix' => 'claimotherdetail'), function() {
	        Route::get('create/{patient_id}', "Patients\Api\ClaimOtherDetailApiController@getCreateApi");
	        Route::post('/', "Patients\Api\ClaimOtherDetailApiController@getStoreApi");
	        Route::get('edit/{id}', "Patients\Api\ClaimOtherDetailApiController@getEditApi");
	        Route::post('update/{id}', "Patients\Api\ClaimOtherDetailApiController@getUpdateApi");
	    });

        Route::get('chargesexport/{patient_id}/{export}', 'Patients\BillingController@getBillingExport');
        //Billing Api Urls Ends here
        // Patient inside Payments related api starts here
        // Route::get('paymentsexport/{patient_id}/{tab}/{claim_id?}/{export}', 'Payments\Api\PatientPaymentApiController@getIndexApi');
        Route::get('paymentsexport/{patient_id}/{tab}/{claim_id?}/{export}', 'Patients\PatientPaymentController@getPaymentExport');
        // Patient inside  Payments related api ends here
    });
	### Patient Api URL ends


    // Charges API URL    
	Route::group(array('prefix' => 'charges'), function() {
		Route::get('searchpatient/{type}/{key}', 'Charges\Api\Controller@getSearchPatientApi');
	    Route::get('/', 'Charges\Api\ChargeApiController@getIndexApi');
	    Route::post('/', 'Charges\Api\ChargeApiController@getStoreApi');
	    Route::get('{claim_id}/edit', 'Charges\Api\ChargeApiController@getEditApi');
	    Route::post('update/{id}', 'Charges\Api\ChargeApiController@getupdateApi');	
	});
    

    // Charges URL routes Ends 
    // Payments related code starts here
    //Route::get('paymentsexport/{export}', 'Payments\Api\PaymentApiController@getIndexApi');
    //payments new export
    Route::get('paymentsexport/{export}', 'Payments\PaymentController@paymentsExport');
    // Payments related code ends here
    //Route::get('chargesexport/{export}', 'Charges\Api\ChargeApiController@getIndexApi');
    //charges new export
    Route::get('chargesexport/{export}', 'Charges\ChargeController@chargesExport');

    Route::post('{id}/payments/insurancepost', 'Patients\PaymentInsuranceController@store');

    Route::post('get_superbill_showmore_list', 'Api\SuperbillsApiController@get_superbill_showmore_list'); //Get superbill cpt code showmore
    ### DOCUMENTS MODULE START ###
    Route::resource('documents', 'Documents\Api\DocumentApiController');

    Route::group(array('prefix' => 'documents'), function() {
    	Route::post('module/addform', 'Documents\Api\DocumentApiController@getStoreApi');
	    Route::get('list/{module}', 'Documents\Api\DocumentApiController@getIndexApi');
	    Route::get('getcategorylist/{category}', 'Documents\Api\DocumentApiController@getCategoryApi');
	    Route::get('delete/{cust_id}', 'Documents\Api\DocumentApiController@getDestroyApi');
    });	
    
    ### DOCUMENTS MODULE END ###  

});