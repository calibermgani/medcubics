<!-- Start Admin form error message  -->	
@if(strpos($currnet_page, 'admin') !== false)
	<script>
		var atleastone_letter_lang_err_msg 	= '{{ trans("common.validation.oneletter") }}';
		var atleastone_number_lang_err_msg 	= '{{ trans("common.validation.onenumeric") }}';
		var min_length_lang_err_msg 	    = '{{ trans("common.validation.password_length") }}';
		var support_select_user_err_msg 	    = '{{ trans("support/ticket.validation.selectuser") }}';
	</script>
@endif	
<!-- End Admin form error message  -->	
<script>
	/*** Define variable length  ***/
	var date_valid_lang_err_msg 			= '{{ trans("common.validation.date_format") }}';
	var start_date_req_lang_err_msg 		= '{{ trans("reports/report.validation.start_date_required") }}';
	var end_date_val_lang_err_msg 			= '{{ trans("reports/report.validation.end_date_valid") }}';	
	var end_date_req_lang_err_msg			= '{{ trans("reports/report.validation.end_date_required") }}';	
	var address_max_defined_length			= '{{ Config::get("siteconfigs.form_field_max_length.address") }}';
	var taxonomy_code_max_defined_length	= '{{ Config::get("siteconfigs.form_field_max_length.taxonomy_code") }}';
	var companyname_max_defined_length      = '{{ Config::get("siteconfigs.form_field_max_length.city") }}';
	var contactperson_max_defined_length    = '{{ Config::get("siteconfigs.form_field_max_length.city") }}';
	var practicemanager_max_defined_length  = '{{ Config::get("siteconfigs.form_field_max_length.city") }}';
	var practiceceo_max_defined_length  	= '{{ Config::get("siteconfigs.form_field_max_length.city") }}';
	var city_max_defined_length				= '{{ Config::get("siteconfigs.form_field_max_length.city") }}';
	var state_max_defined_length			= '{{ Config::get("siteconfigs.form_field_max_length.state") }}';
	var zipcode5_max_defined_length			= '{{ Config::get("siteconfigs.form_field_max_length.zipcode5") }}';
	var zipcode4_max_defined_length			= '{{ Config::get("siteconfigs.form_field_max_length.zipcode4") }}';
	var feeschedule_max_defined_length		= '{{ Config::get("siteconfigs.form_field_max_length.feeschedule") }}';
	var filesize_max_defined_length			= '{{ Config::get("siteconfigs.form_field_max_length.filesize") }}';
	var eob_attacment_max_defined_length	= '{{ Config::get("siteconfigs.form_field_max_length.eobfilesize") }}';
	var contact_person_max_defined_length 	=	'{{ Config::get("siteconfigs.form_field_max_length.contact_person") }}';
	var practice_name_max_defined_length 	= '{{ Config::get("siteconfigs.form_field_max_length.practice_name") }}';
	var create_lang_err_msg 				= '{{ trans("common.validation.create_msg") }}';
	var update_lang_err_msg 				= '{{ trans("common.validation.update_msg") }}';	
	/*** Define variable length end  ***/

	/*** Defined variable length error message  ***/	
	var feeschedule_max_length_lang_err_msg	= '{{ trans("practice/practicemaster/managecare.validation.feeschedule_limit") }}';
	var contact_person_max_length_lang_err_msg	= '{{ trans("admin/customer.validation.contact_person_limit") }}';
	var practice_name_max_length_lang_err_msg	= '{{ trans("admin/customer.validation.practice_name_limit") }}';	
	/*** Define variable length end  ***/
	
	/***	common error message start	***/
	var only_numeric_lang_err_msg		= '{{ trans("common.validation.numeric") }}';
	var only_alpha_lang_err_msg			= '{{ trans("common.validation.alpha") }}';
	var alphanumeric_lang_err_msg		= '{{ trans("common.validation.alphanumeric") }}';
	var alphaspace_lang_err_msg			= '{{ trans("common.validation.alphaspace") }}';
	var alphanumericspace_lang_err_msg	= '{{ trans("common.validation.alphanumericspac") }}';
	var alphanumericdot_lang_err_msg	= '{{ trans("practice/practicemaster/cpt.validation.anesthesia_dot_limit") }}';
	
	//Documents msg
	var title_lang_err_msg				= '{{ trans("common.validation.title") }}';
	var category_lang_err_msg			= '{{ trans("common.validation.category") }}';
	var module_lang_err_msg				= '{{ trans("common.validation.module") }}';
	var description_lang_err_msg		= '{{ trans("common.validation.description") }}';
	var attachment_lang_err_msg			= '{{ trans("common.validation.upload") }}';
	var attachment_valid_lang_err_msg	= '{{ trans("common.validation.upload_valid") }}';
	var attachment_length_lang_err_msg	= '{{ trans("common.validation.upload_limit") }}';	
	var address1_lang_err_msg			= '{{ trans("common.validation.address1_required") }}';
	var city_lang_err_msg				= '{{ trans("common.validation.city_required") }}';
	var city_max_length_lang_err_msg	= '{{ trans("common.validation.city_limit") }}';
	var companyname_max_length_lang_err_msg		= '{{ trans("common.validation.companyname") }}';
	var contactperson_max_length_lang_err_msg	= '{{ trans("common.validation.contactperson") }}';
	var practicemanager_max_length_lang_err_msg	= '{{ trans("common.validation.practicemanager") }}';
	var practiceceo_max_length_lang_err_msg		= '{{ trans("common.validation.practiceceo") }}';
	var state_lang_err_msg 				= '{{ trans("common.validation.state_required") }}';
	var state_limit_lang_err_msg		= '{{ trans("common.validation.state_limit") }}';
	var zip5_lang_err_msg				= '{{ trans("common.validation.zipcode5_required") }}';
	var zip5_limit_lang_err_msg			= '{{ trans("common.validation.zipcode5_limit") }}';
	var zip4_limit_lang_err_msg			= '{{ trans("common.validation.zipcode4_limit") }}';
	var ssn_lang_err_msg				= '{{ trans("practice/patients/patients.validation.ssn") }}';
	var home_phone_lang_err_msg			= '{{ trans("common.validation.home_phone") }}';
	var work_phone_lang_err_msg			= '{{ trans("common.validation.work_phone") }}';
	var cell_phone_lang_err_msg			= '{{ trans("common.validation.cell_phone") }}';
	var phone_lang_err_msg				= '{{ trans("common.validation.phone") }}';
	var phone_number_valid_lang_err_msg	= '{{ trans("common.validation.phone_number_valid") }}';
	var home_phone_limit_lang_err_msg	= '{{ trans("common.validation.home_phone_limit") }}';
	var work_phone_limit_lang_err_msg	= '{{ trans("common.validation.work_phone_limit") }}';
	var cell_phone_limit_lang_err_msg	= '{{ trans("common.validation.cell_phone_limit") }}';
	var phone_limit_lang_err_msg		= '{{ trans("common.validation.phone_limit") }}';
	var fax_lang_err_msg				= '{{ trans("common.validation.fax") }}';
	var fax_limit_lang_err_msg			= '{{ trans("common.validation.fax_limit") }}';
	var email_valid_lang_err_msg		= '{{ trans("common.validation.email_valid") }}';
	var email_max_length_lang_err_msg	= '{{ trans("common.validation.email_max_length") }}';
	var valid_dob_format_err_msg        = '{{ trans("common.validation.valid_dob_format") }}';       
	var valid_deceased_format        	= '{{ trans("common.validation.valid_deceased_format") }}'; 	
	/***	common error message end	***/

	/*** Common message for payment posting and charges starts here**/
	var empty_amt   = '{{ trans("practice/patients/payments.validation.paid_amount")}}';
    var greater_zero_amt   = '{{ trans("practice/patients/payments.validation.grater_amt")}}';
    var valid_amt = '{{ trans("practice/patients/payments.validation.valid_amt")}}';
    var card_empty = '{{trans("common.validation.card_notempty")}}';
    var wallet_amt_exceed = '{{ trans("practice/patients/payments.validation.wallet_amt_exceed")}}';
    var refund_amt_exceed = '{{ trans("practice/patients/payments.validation.refund_amt_exceed")}}';
    var card_no = '{{trans("common.validation.card_no")}}';
    var card_on_card ='{{trans("common.validation.name_on_card")}}';
    var empty_check_no ='{{trans("common.validation.check_no")}}';
    lengthval = '{{ Config::get("siteconfigs.payment.check_no_minlength") }}'; 
    var checkminlength = '{{ trans("practice/patients/payments.validation.checkminlength")}} '+lengthval;
    var checkexist = '{{ trans("practice/patients/payments.validation.checkexist")}}';
    var adjustment_reason = '{{ trans("practice/patients/payments.validation.adjustment")}}';
    var future_date = '{{ trans("practice/patients/payments.validation.furute_date")}}';
    var past_date = '{{ trans("practice/patients/payments.validation.past_date")}}';
    var sel_insurance = '{{ trans("practice/patients/payments.validation.insurance_notempty")}}';
    var sel_provider = '{{ trans("practice/patients/payments.validation.provider_notempty")}}';
    var check_eft_no  = '{{trans("common.validation.check_eft_no")}}';
    var checkeftexist  = '{{ trans("practice/patients/payments.validation.checkeftexist")}}';
    var check_eft_date  = '{{trans("common.validation.check_eft_date")}}';
    var date_format		= '{{ trans("common.validation.date_format") }}';
    var date_deposite		= '{{ trans("common.validation.deposit_date") }}';
    var date_posting		= '{{ trans("common.validation.posting_date") }}';
    var bankname		= '{{ trans("practice/patients/payments.validation.bankname")}}';
    var check_date_msg  = '{{ trans("practice/patients/payments.validation.check_date")}}';
	var lastname_lang_err_msg		= '{{ trans("practice/patients/patients.validation.lastname") }}';
	var name_limit		= '{{ trans("practice/patients/patients.validation.name_limit") }}';
	var firstname		= '{{ trans("practice/patients/patients.validation.firstname") }}';
	var add1_limit		= '{{ trans("practice/patients/patients.validation.add1_limit") }}';
	var guar_last_name	= '{{ trans("practice/patients/patients.validation.guarantor_last_name") }}';
	var guar_fst_name	= '{{ trans("practice/patients/patients.validation.guarantor_first_name") }}';
    /*** Common message for payment posting and charges ends here **/
</script>
<!-- End Common form error message  -->	
	<!-- Start patients form error message  -->	
@if($patient_current_page == 'patients' || $patient_charges_page == 'charges' || $patient_charges_page == 'payments')
	<script>
		var deceased_date	= '{{ trans("practice/patients/patients.validation.deceased_date") }}';	
		var patient_name	= '{{ trans("practice/patients/patients.validation.name_limit") }}';
		var category 		= '{{ trans("practice/patients/patients.validation.category") }}';
		var con_category	= '{{ trans("practice/patients/patients.validation.contact_category") }}';
		var categoryunique 	= '{{ trans("practice/patients/patients.validation.category_unique") }}';
		var insurance		= '{{ trans("practice/patients/patients.validation.insurance") }}';
		var policyid		= '{{ trans("practice/patients/patients.validation.policyid") }}';
		var policyid_limit	= '{{ trans("practice/patients/patients.validation.policyid_limit") }}';
		var eff_date		= '{{ trans("common.validation.eff_date_required") }}';
		var eff_date_valid	= '{{ trans("common.validation.effectivedate") }}';
		var ter_date_valid	= '{{ trans("common.validation.terminationdate") }}';
		var inact_date_valid= '{{ trans("common.validation.inactivedate") }}';
		var ter_date		= '{{ trans("common.validation.ter_date_required") }}';
		var inact_date_req	= '{{ trans("common.validation.inactdate_required") }}';		
		var medical_secondary_code_lang_err_msg	= '{{ trans("practice/patients/patients.validation.medical_secondary") }}';
		var insured_lstname	= '{{ trans("practice/patients/patients.validation.insured_last_name") }}';
		var insured_fstname	= '{{ trans("practice/patients/patients.validation.insured_first_name") }}';
		var employer_name	= '{{ trans("practice/patients/patients.validation.employer_name") }}';
		var employer_status	= '{{ trans("practice/patients/patients.validation.employer_status") }}';
		var adjustor_name 	= '{{ trans("practice/patients/patients.validation.adjustor_name") }}';
		var adjustor_ph_limit 	= '{{ trans("practice/patients/patients.validation.adjustor_ph_limit") }}';
		var adjustor_fax 	= '{{ trans("practice/patients/patients.validation.adjustor_fax") }}';
		var auth_limit		= '{{ trans("practice/patients/patients.validation.auth_limit") }}';
		var visits_used		= '{{ trans("practice/patients/patients.validation.visits_used") }}';
		var visit_remains	= '{{ trans("practice/patients/patients.validation.alert_visit_remains") }}';
		var pos				= '{{ trans("practice/patients/patients.validation.pos") }}';
		var amt_used		= '{{ trans("practice/patients/patients.validation.amt_used") }}';
		var insurance		= '{{ trans("practice/patients/patients.validation.insurance") }}';
		var auth_insurance	= '{{ trans("practice/patients/patients.validation.auth_insurance") }}';
		var end_date		= '{{ trans("practice/patients/patients.validation.end_date") }}';
        var authorization_contact_person = '{{ trans("practice/patients/patients.validation.authorization_contact_person") }}';
        var kwd_limit_lang_err_msg = '{{ trans("practice/patients/patients.validation.search_keyword_limit") }}';
        var kwd_req_lang_err_msg = '{{ trans("practice/patients/patients.validation.search_keyword_req") }}';		
	</script>
	<!-- End patients form error message  -->	

@endif
<!-- Starts JS Alert message for charges-->
<script>
	var auth_no			= '{{ trans("practice/patients/patients.validation.authorization_no") }}';
	var invalid_date = '{{ trans("common.validation.date_format") }}';
	var not_future = '{{ trans("practice/patients/charges.validation.doi_future") }}';	
	var invalid_cpt_msg = '{{ trans("practice/patients/charges.validation.invalid_cpt") }}';
	var invalid_icd_msg = '{{ trans("practice/patients/charges.validation.invalid_icd") }}';
	var numeric_err_msg = '{{ trans("practice/patients/charges.validation.numeric_err") }}';
	var succ_msg = '{{ trans("practice/patients/charges.validation.succ_msg") }}';
	var exist_err_msg = '{{ trans("practice/patients/charges.validation.exist_err") }}';
	var modifier_err_msg= '{{ trans("practice/patients/charges.validation.modifier_err") }}';
	var amt_err_msg = '{{ trans("practice/patients/charges.validation.amt_err") }}';
	var min_choose_err_msg = '{{ trans("practice/patients/charges.validation.min_choose_err") }}';
	var baalnce_ant_err_msg = '{{ trans("practice/patients/charges.validation.baalnce_ant_err") }}';
	var unappied_err_msg = '{{ trans("practice/patients/charges.validation.unappied_err") }}';
	var billed_amt_err_msg = '{{ trans("practice/patients/charges.validation.billed_amt_err") }}';
	var allowed_amt_err_msg = '{{ trans("practice/patients/charges.validation.allowed_amt_err") }}';
	var paid_amt_err_msg = '{{ trans("practice/patients/charges.validation.paid_amt_err") }}';
	var wallet_confm_msg = '{{ trans("practice/patients/charges.validation.wallet_confm_msg") }}';
	var dos_cnfm_msg = '{{ trans("practice/patients/charges.validation.dos_cnfm_msg") }}';
	var paid_message = '{{ trans("practice/patients/charges.validation.paid_message") }}';
	var search_msg = '{{ trans("common.validation.search_msg") }}';
	var data_msg = '{{ trans("common.validation.data_msg") }}';
	var data_add_msg = '{{ trans("common.validation.data_add_msg") }}';
	var icd1 = '{{ trans("practice/patients/charges.validation.icd1") }}';
	var icd_exist = '{{ trans("practice/patients/charges.validation.icd_exist") }}';
	var invalid_icd = '{{ trans("practice/patients/charges.validation.invalid_icd") }}';
	var invalid_start_date = '{{ trans("practice/patients/charges.validation.start_date") }}';
	var invalid_end_date = '{{ trans("practice/patients/charges.validation.end_date") }}';
	var choose_patient = '{{ trans("practice/patients/charges.validation.end_date") }}';
	var minimum_line_item = '{{ trans("practice/patients/charges.validation.minimum_line_item") }}';
	var maximum_line_item = '{{ trans("practice/patients/charges.validation.maximum_line_item") }}';
	var checkamount_excedd = '{{ trans("practice/patients/payments.validation.checkamount") }}';
</script>
<!-- ends JS Alert message for charges-->

@if((strpos($currnet_page, 'superbills') !== false))
	<script type="text/javascript">
		/*** start to assign error message ***/
		var template_req 	= '{{ trans("practice/practicemaster/superbill.validation.template_name") }}';
		var provider_req 	= '{{ trans("common.validation.provider_required") }}';
		var header_req 		= '{{ trans("practice/practicemaster/superbill.validation.header_list") }}';
		var header_empty_lang_err_msg 	= '{{ trans("practice/practicemaster/superbill.validation.header_empty") }}';
		var status_req 		= '{{ trans("practice/practicemaster/superbill.validation.status") }}';
		var keyword_req 	= '{{ trans("practice/practicemaster/superbill.validation.keyword") }}';
		var alphaspace_lang_err_msg			= '{{ trans("common.validation.alphaspace") }}';
		/*** start to assign error message ***/
	</script>
@endif	

@if((strpos($currnet_page, 'questionnaire') !== false))
	<script type="text/javascript">
		/*** start to assign error message ***/
		var question_lang_err_msg 	= '{{ trans("practice/practicemaster/questionnaries.validation.question") }}';
		var question_regex_lang_err_msg = '{{ trans("practice/practicemaster/questionnaries.validation.question_regex") }}';
		var question_answer_lang_err_msg = '{{ trans("practice/practicemaster/questionnaries.validation.ques_answer") }}';
		var option_lang_err_msg 	= '{{ trans("practice/practicemaster/questionnaries.validation.option_value") }}';
		var option_limit_lang_err_msg 	= '{{ trans("practice/practicemaster/questionnaries.validation.option_limit") }}';
		/*** start to assign error message ***/
	</script>
@endif	

@if(strpos($currnet_page, 'practicescheduler') !== false || strpos($currnet_page, 'practiceproviderscheduler') !== false)
	<script type="text/javascript">
		/*** start to assign error message ***/
		var date_format_lang_err_msg	= '{{ trans("common.validation.date_format") }}';
		var facility_id_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.facility_id") }}';
		var end_date_lang_err_msg 		= '{{ trans("practice/practicemaster/providerscheduler.validation.enddate") }}';
		var start_date_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.startdate") }}';
		var strdate_req_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.startdate_required") }}';
		var enddate_req_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.stopdate_required") }}';
		var time_slot_empty_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.time_slot") }}';
		var from_to_time_slot_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.from_to_time") }}';
		var enter_occurence_lang_err_msg 	= '{{ trans("practice/practicemaster/providerscheduler.validation.enter_occurence") }}';	
		/*** start to assign error message ***/
	</script>
@endif	

<script type="text/javascript">
	/*** start to assign error message ***/
	var greater_than_zero_err_msg		= '{{ trans("practice/scheduler.validation.greater_than_zero") }}';
	var unable_to_appt_past_err_msg		= '{{ trans("practice/scheduler.validation.unable_to_appt_past") }}';
	var select_appt_date_err_msg		= '{{ trans("practice/scheduler.validation.select_appt_date") }}';
	var appt_date_not_availe_err_msg	= '{{ trans("practice/scheduler.validation.appt_date_not_available") }}';
	var slot_time_not_available_err_msg	= '{{ trans("practice/scheduler.validation.slot_time_not_available") }}';
	var select_appt_slot_time_err_msg	= '{{ trans("practice/scheduler.validation.select_appt_slot_time") }}';
	var select_reason_visit_err_msg		= '{{ trans("practice/scheduler.validation.select_reason_visit") }}';
	var select_copay_option_err_msg		= '{{ trans("practice/scheduler.validation.select_copay_option") }}';
	var enter_copay_amt_err_msg			= '{{ trans("practice/scheduler.validation.enter_copay_amt") }}';
	var select_patient_err_msg			= '{{ trans("practice/scheduler.validation.select_patient") }}';
	var enter_last_name_err_msg			= '{{ trans("practice/scheduler.validation.enter_last_name") }}';
	var enter_first_name_err_msg		= '{{ trans("practice/scheduler.validation.enter_first_name") }}';
	var enter_address1_err_msg			= '{{ trans("practice/scheduler.validation.enter_address1") }}';
	var enter_city_err_msg				= '{{ trans("practice/scheduler.validation.enter_city") }}';
	var enter_state_err_msg				= '{{ trans("practice/scheduler.validation.enter_state") }}';
	var enter_zipcode_err_msg			= '{{ trans("practice/scheduler.validation.enter_zipcode") }}';
	var select_pri_ins_err_msg			= '{{ trans("practice/scheduler.validation.select_pri_ins") }}';
	var pat_exist_err_msg			= '{{ trans("practice/scheduler.validation.pat_exist") }}';
	var enter_policy_id_err_msg			= '{{ trans("practice/scheduler.validation.enter_policy_id") }}';
	var enter_checkin_time_err_msg		= '{{ trans("practice/scheduler.validation.enter_checkin_time") }}';
	var check_in_out_diff_err_msg		= '{{ trans("practice/scheduler.validation.check_in_out_diff") }}';
	var check_out_in_diff_err_msg		= '{{ trans("practice/scheduler.validation.check_out_in_diff") }}';
	var appt_canceled_err_msg			= '{{ trans("practice/scheduler.validation.appt_canceled") }}';
	var appt_deleted_err_msg			= '{{ trans("practice/scheduler.validation.appt_deleted") }}';		
	var charge_dos_from					= '{{ trans("practice/patients/billing.validation.dos_from") }}';
	var charge_doi_future				= '{{ trans("practice/patients/charges.validation.doi_future") }}';
	var charge_grater_than_admit		= '{{ trans("practice/patients/charges.validation.grater_than_admit") }}';
	var charge_to_date					= '{{ trans("practice/patients/billing.validation.to_date") }}';
	var charge_cpt						= '{{ trans("practice/patients/billing.validation.cpt") }}';
	var charge_billing_provider			= '{{ trans("practice/patients/billing.validation.billing_provider") }}';
	var charge_rendering_provider		= '{{ trans("practice/patients/billing.validation.rendering_provider") }}';
	var charge_icd1						= '{{ trans("practice/patients/billing.validation.icd1") }}';
	var charge_facility_id				= '{{ trans("practice/patients/billing.validation.facility_id") }}';
	var charge_insurance_id				= '{{ trans("practice/patients/billing.validation.insurance_id") }}';
	var charge_pos_name					= '{{ trans("practice/patients/billing.validation.pos_name") }}';
	var charge_admit_date				= '{{ trans("practice/patients/billing.validation.admit_date") }}';
	var charge_unable_to_work_to_call	= '{{ trans("practice/patients/claim_detail.validation.unable_to_work_to_call") }}';
	var charge_copay					= '{{ trans("practice/patients/billing.validation.copay") }}';
	var hold_reason						= '{{ trans("practice/patients/billing.validation.hold_reason_id") }}';
	var charge_copay_amt				= '{{ trans("practice/patients/billing.validation.copay_amt") }}';
	var charge_not_zero					= '{{ trans("practice/patients/billing.validation.not_zero") }}';
	var charge_icd1_0					= '{{ trans("practice/patients/billing.validation.icd1_0") }}';
	var charge_check_no					= '{{ trans("common.validation.check_no") }}';
	var charge_card_notempty			= '{{ trans("common.validation.card_notempty") }}';
	var insurancetype					= '{{ trans("common.validation.enternew") }}';
	var exist_insurancetype				= '{{ trans("common.validation.alreadyexist") }}';
	var dos_msg							= '{{ trans("practice/patients/charges.validation.dos") }} ';
	var dischargedate					= '{{ trans("practice/patients/charges.validation.dischargedate") }}';    
	var maximum_amt						= '{{ trans("practice/patients/billing.validation.maximum_amt") }}';
	var anesthesia_start				= '{{ trans("practice/patients/billing.validation.anesthesia_start") }}';
	var anesthesia_stop				    = '{{ trans("practice/patients/billing.validation.anesthesia_stop") }}';
	var anesthesia_start_time			= '{{ trans("practice/patients/billing.validation.anesthesia_start_time") }}';
	var anesthesia_end_time				= '{{ trans("practice/patients/billing.validation.anesthesia_end_time") }}';
	var enter_admit_date				= '{{ trans("practice/patients/billing.validation.enter_admit_date") }}';
</script>
	
@if(strpos($currnet_page, 'patientstatementsettings') !== false || strpos($currnet_page, 'bulkstatement') !== false || strpos($currnet_page, 'individualstatement') !== false || strpos($currnet_page, 'patients') !== false || strpos($currnet_page, 'charges') !== false)
	<script type="text/javascript">		
		var patient_balance	= '{{ trans("practice/practicemaster/patientstatementsettings.validation.nopatientbalance") }}';
		var unknownsettingsmsg = '{{ trans("practice/practicemaster/patientstatementsettings.validation.unknownsettingsmsg") }}';
		var show_patient_balance	= '{{ trans("practice/practicemaster/patientstatementsettings.validation.patientbalance") }}';
	</script>
@endif	