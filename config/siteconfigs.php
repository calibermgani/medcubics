<?php

return [
	'scheduler' => [
		'default_view_facility' => 'Facility',
		'default_view_provider' => 'Provider',
		'dynamic_color_code' => ['#FF1493','#00BFFF','#228B22','#9400D3','#00FFFF','#DAA520','#FF69B4','#B22222',
                                '#0000FF','#7FFFD4','#D2691E','#483D8B','#E9967A','#20B2AA','#C71585','#BDB76B',
                                '#8B008B','#556B2F','#F0E68C','#B0C4DE','#191970','#808000','FFA500','#DB7093',
                                '#8B4513','#2E8B57','#D2B48C','#D8BFD8','#808080','#2F4F4F'],
	],
	'customer_image' => [
		'defult_image_size' => 'mimes:jpeg,jpg,png',
		'defult_image_message' => 'Image file only allowed',
		'user_image_size' => 'mimes:jpeg,jpg,png',
		'user_image_message' => 'Image file only allowed'
	],
	'production' => [
		'defult_production' => 'production',
		'claim_server' => 'development'
	],
	'file_uplode' => [
		'defult_file_attachment' => 'mimes:jpeg,jpg,png,txt,doc,docx,pdf,csv,xlsx,gif,xls,JPEG,JPG,PNG,TXT,DOC,DOCX,PDF,CSV,XLSX,GIF,XLS',
		'defult_file_message' => 'The attachment must be a file of type: jpeg, jpg, png, txt, doc, docx, pdf.',
		'server' => 'local'
	],	
	'providerAlertCron' => [
		'alert_on' => 'day',
		'alert_before' => '1'
	],
	'userticket' => [
		'alert_on' => 'day',
		'alert_before' => '2',
		'status_change_before' => '1',
		'admin_email'	=> 'admin@medcubics.com'
	], 
	'language_id' => [
		'defult_language_id' => '5',
	],
	'provider_scheduler' => [
		'appointment_slot' => '15'
	],
	'password' => [
		'attempt' => 3,
		'attempt_expire' => 20 //only given mins
	],
	'login' => [
		'resetpasswordactivationhours' => '24'
	],
	'maximum_file_upload' => [
		'size' => '100000',
		'unit' => 'KB',
	],
	'document_upload_modal_icon' => 'fa fa-paperclip icon-green-attachment', 
	'document_upload_modal_icon_view' => 'fa fa-paperclip icon-green',	
	'manual_error_log_enable' => 'yes',
	'is_enable_provider_add' => 1,
	'default_date_format' => 'MM/DD/YYYY',
	'document_upload' => [
		'webcam' => false,
		'scanner' => false,
	],
	'pagepermission' => [
		'practice' => true,
		'medcubics' => true,
	],
	'providertype' => [
		'Referring' => 2,
		'Ordering' => 3,
		'Supervising' => 4,
		'Billing' => 5,
		'Rendering' => 1
	],
	'form_field_max_length' => [
		'address' 			=> 50,  // also setted in data-mask
		'companyname'		=> 50,	// also setted in data-mask
		'contactperson'		=> 50,	// also setted in data-mask
		'practicemanager'	=> 50,	// also setted in data-mask
		'practiceceo'		=> 50,
		'city' 				=> 50,	// also setted in data-mask
		'state' 			=> 2,	// also setted in data-mask
		'zipcode5' 			=> 5,	// also setted in data-mask
		'zipcode4' 			=> 4,	// also setted in data-mask
		'feeschedule' 		=> 100,
		'taxonomy_code'		=> 10,
		'filesize' 			=> 32768, // Script validation set in kb only 
		'contact_person' 	=> 50, 
		'practice_name' 	=> 64, 
		'eobfilesize' 		=> 100000, // Script validation set in kb only 
	],
	'templatetype' => [
		'benefit_verifications' => 1,
		'patient_letters' => 2,
		'insurance_letters' => 3,
		'others' => 4
	],
	'faq_category' => [
		'General' => 'General',
		'Product' => 'Product',
		'CHARGECAPTURE' => 'Chargecapture',		
	],
	'patient' => [
		'statement_icon' => true
	],
	'reports' => [
		'paginate_list_per_page' => 25,
	],
	'insurance_type_id' => [
		'default_id' => 1,
	],
	'patientstatement' => [
		'removepreviewinminutes' => 10
	],
	'charges' => [
		'patientorchargelimit' => 25
	],
	'payment' => [
		'adjustment_code' => 3,
		'remark_code' => 2,
		'check_no_minlength' => 3,
		'check_no_maxlength' => 50,
		'max_claim_choose_onsearch' =>10,
		'denial_code' => ['','CO', 'PR',  'OA', 'PI'],
	],
	'medicare_insurance_type_code' => ['MA','MB'],
	'connection_database' 	=> 'betacore',
	'useractivity'      	=> array('blog'=>array('table'=>'blog','field_name'=>'title','parent'=>'blog','child'=>'blog'),
                                     'comments'=>array('table'=>'blog_comments','field_name'=>'comments','parent'=>'blog','child'=>'comments'),
                                     'bloggroup' => array('table'=>'blog_group','field_name'=>'group_name','parent'=>'blog','child'=>'group'),
                                     'customer'=>array('table'=>'customers','field_name'=>'customer_name','parent'=>'customers','child'=>'customer'), 
                                     'customerpractices'=>array('table'=>'practices','field_name'=>'practice_name','parent'=>'customers','child'=>'Practice'), 
                                     'providers'=>array('table'=>'providers','field_name'=>'provider_name','parent'=>'customers','child'=>'Provider'), 
                                     'customerusers'=>array('table'=>'users','field_name'=>'name','parent'=>'customers','child'=>'users'), 
                                     'customernotes'=>array('table'=>'customernotes','field_name'=>'title','parent'=>'customers','child'=>'notes'), 
                                     'insurance'=>array('table'=>'insurances','field_name'=>'insurance_name','parent'=>'Insurance','child'=>'Insurance'), 
                                     'insurancetypes'=>array('table'=>'insurancetypes','field_name'=>'type_name','parent'=>'Insurance','child'=>'Type'), 
                                     'modifier'=>array('table'=>'modifiers','field_name'=>'name','parent'=>'Modifiers','child'=>'Modifiers'), 
                                     'feeschedule'=>array('table'=>'feeschedules','field_name'=>'file_name','parent'=>'Fee Schedule','child'=>'Fee Schedule'),
                                     'cpt'=>array('table'=>'cpts','field_name'=>'cpt_hcpcs','parent'=>'CPT','child'=>'Cpt code'),
                                     'icd'=>array('table'=>'icd_10','field_name'=>'icd_code','parent'=>'ICD','child'=>'Icd Code'),
                                     'speciality'=>array('table'=>'specialities','field_name'=>'speciality','parent'=>'speciality','child'=>'speciality'),
                                     'taxanomy'=>array('table'=>'taxanomies','field_name'=>'code','parent'=>'taxanomy','child'=>'taxanomy'),
                                     'placeofservice'=>array('table'=>'pos','field_name'=>'pos','parent'=>'POS','child'=>'place of service'),
                                     'qualifiers'=>array('table'=>'id_qualifiers','field_name'=>'id_qualifier_name','parent'=>'ID Qualifiers','child'=>'ID Qualifiers'),
                                     'providerdegree'=>array('table'=>'provider_degrees','field_name'=>'degree_name','parent'=>'Provider Degree','child'=>'Provider Degree'),
                                     'role'=>array('table'=>'roles','field_name'=>'role_name','parent'=>'Roles','child'=>'role'),
                                     'adminuser'=>array('table'=>'users','field_name'=>'name','parent'=>'Admin User','child'=>'user'),
                                     'practice'=>array('table'=>'practices','field_name'=>'practice_name','parent'=>'Practice','child'=>'practice'),
                                     'overrides'=>array('table'=>'practiceoverrides','field_name'=>'providers_id','parent'=>'Practice','child'=>'Overrides'),
                                     'managecare'=>array('table'=>'practicemanagecares','field_name'=>'practice_id','parent'=>'Practice','child'=>'Managecare'),
                                     'contactdetail'=>array('table'=>'contactdetails','field_name'=>'practiceceo','parent'=>'Practice','child'=>'contact detail'),
                                     'document'=>array('table'=>'documents','field_name'=>'title','parent'=>'Practice','child'=>'documents'),
                                     'notes'=>array('table'=>'notes','field_name'=>'title','parent'=>'Practice','child'=>'notes'),
									 'patients-notes'=>array('table'=>'patient_notes','field_name'=>'title','parent'=>'Patient','child'=>'notes'),
                                     'facility'=>array('table'=>'facilities','field_name'=>'facility_name','parent'=>'Facility','child'=>'facility'),
									 'facilitymanagecare'=>array('table'=>'facilitymanagecares','field_name'=>'providers_id','parent'=>'Facility','child'=>'Managecare'),
									 'facilitydocument'=>array('table'=>'documents','field_name'=>'title','parent'=>'Facility','child'=>'document'),
                                     'provider'=>array('table'=>'providers','field_name'=>'provider_name','parent'=>'Provider','child'=>'provider'),
                                     'provideroverrides'=>array('table'=>'provideroverrides','field_name'=>'providers_id','parent'=>'Provider','child'=>'overrides'),
                                     'providermanagecare'=>array('table'=>'providermanagecares','field_name'=>'providers_id','parent'=>'Provider','child'=>'Managecare'),
                                     'providerdocuments'=>array('table'=>'documents','field_name'=>'title','parent'=>'Provider','child'=>'documents'),
                                     'insuranceappealaddress'=>array('table'=>'insuranceappealaddress','field_name'=>'insurance_id','parent'=>'Insurance','child'=>'appealaddress'),
                                     'insuranceoverrides'=>array('table'=>'insuranceoverrides','field_name'=>'insurance_id','parent'=>'Insurance','child'=>'overrides'),
                                     'modifierlevel1'=>array('table'=>'modifiers','field_name'=>'name','parent'=>'Modifier Level I','child'=>'modifer'),
                                     'modifierlevel2'=>array('table'=>'modifiers','field_name'=>'name','parent'=>'Modifier Level II','child'=>'modifer'),
                                     'employer'=>array('table'=>'employers','field_name'=>'employer_name','parent'=>'Employers','child'=>'employer'),
                                     'code'=>array('table'=>'codes','field_name'=>'transactioncode_id','parent'=>'Codes','child'=>'code'),
                                     'templates'=>array('table'=>'templates','field_name'=>'name','parent'=>'Templates','child'=>'template'),
                                     'templatetypes'=>array('table'=>'templatetypes','field_name'=>'templatetypes','parent'=>'Templates','child'=>'type'),
                                     'staticpage'=>array('table'=>'staticpages','field_name'=>'title','parent'=>'Help','child'=>'help'),   
                                     'registration'=>array('table'=>'practice_registration','field_name'=>'email_id','parent'=>'Registration','child'=>'registration'),
                                     'superbills'=>array('table'=>'superbills','field_name'=>'superbill_name','parent'=>'Superbills','child'=>'template Name'),
                                     'questionnaire'=>array('table'=>'questionnaries_template','field_name'=>'title','parent'=>'Questionnaires','child'=>'questionnaires'),
                                     'questionnaires'=>array('table'=>'questionnaries_template','field_name'=>'template_id','parent'=>'questionnaires','child'=>'template Name'),
                                     'holdoption'=>array('table'=>'holdoptions','field_name'=>'option','parent'=>'Hold Option','child'=>'hold Option'),
									 'reason'=>array('table'=>'reason_for_visits','field_name'=>'reason','parent'=>'Reason For Visit','child'=>'reason'),
									 'adjustmentreason'=>array('table'=>'adjustment_reasons','field_name'=>'adjustment_reason','parent'=>'Adjustment Reason','child'=>'adjustment reason'),
									 'billing'=>array('table'=>'claim_info_v1','field_name'=>'claim_number','parent'=>'Charges','child'=>'claim number'),
									 'charges'=>array('table'=>'claim_info_v1','field_name'=>'claim_number','parent'=>'Charges','child'=>'claim number'),
									 'insurancepost'=>array('table'=>'pmt_info_v1','field_name'=>'pmt_no','parent'=>'Payments','child'=>'payment number'),
									 'patientpost'=>array('table'=>'pmt_info_v1','field_name'=>'pmt_no','parent'=>'Payments','child'=>'payment number'),
									 'payments'=>array('table'=>'pmt_info_v1','field_name'=>'pmt_no','parent'=>'Payments','child'=>'payment number'),
									 'editcheck'=>array('table'=>'pmt_info_v1','field_name'=>'pmt_mode_id','parent'=>'Payments','child'=>'payment number'),
									 'changeClaimResponsibility'=>array('table'=>'claim_tx_desc_v1','field_name'=>'transaction_type','parent'=>'AR Management','child'=>''),
									 'getclaimdenailnotesadded'=>array('table'=>'claim_tx_desc_v1','field_name'=>'transaction_type','parent'=>'AR Management','child'=>''),
									 'getclaimstatusfinalnotesadded'=>array('table'=>'patient_notes','field_name'=>'patient_notes_type','parent'=>'AR Management','child'=>''),
									 'correspondence'=>array('table'=>'patient_correspondence','field_name'=>'email_id','parent'=>'Correspondence','child'=>'email'),
									 'superbill'=>array('table'=>'claim_info_v1','field_name'=>'claim_number','parent'=>'','child'=>''),
									 'budgetplan'=>array('table'=>'patient_budget','field_name'=>'patient_id','parent'=>'Patient','child'=>'budget plan'),
									 'faq'=>array('table'=>'faqs','field_name'=>'id','parent'=>'faq','child'=>'faq') 
                                    ),
	'claim_length_validation'	=> [
		'insured_id' => 28,
		'patient_name' => 28,
		'patient_address' => 28,
		'patient_city' => 23,
		'patient_state' => 3,
		'patient_zip' => 9,
		'patient_telephone' => 10,
		'insured_address' => 23,
		'insurance_name' => 28,
		'claim_codes' => 19,
		'other_claim_id_qualifier' => 2,
		'other_claim_id' => 28,
		'current_illness_qualifier' => 3,
		'other_date_qualifier' => 3,
		'referring_provider_qualifier' => 2,
		'referring_provider_name' => 24,
		'referring_provider_npi' => 10,
		'referring_provider_qualifier' => 2,
		'referring_provider_qualifier_id' => 17,
		'additional_claim_information' => 71,
		'outside_lab_charge_left' => 8,
		'resubmission_code' => 11,
		'original_reference_no' => 18,
		'prior_authorization_no' => 29,
		'emg' => 2,
		'pos' => 2,
		'cpt' => 6,
		'modifiers' => 2,
		'cpt_icd_pointers' => 4,
		'cpt_charges_left' => 6,
		'cpt_charges_right' => 2,
		'cpt_units' => 3,
		'cpt_epsdt' => 1,
		'rendering_provider_qualifier' => 2,
		'rendering_provider_qualifier_id' => 11,
		'rendering_provider_npi' => 10,
		'billing_provider_tax_id' => 15,
		'patient_account_number' => 14,
		'total_charge_left' => 7,
		'total_charge_right' => 2,
		'paid_amount_left' => 6,
		'facility_name' => 26,
		'facility_address' => 26,
		'facility_city_state_zip' => 26,
		'facility_secondary_type' => 2,
		'facility_secondary_type_id' => 12,
		'billing_provider' => 87,
		'billing_provider_secondary_type' => 2,
		'billing_provider_secondary_type_id' => 15
	],
	'ar_max_claim_seleted' => 5,
	'icdcptsearch' => [
		'type' => "imo"     // [imo, dbsearch] change anyone here
	],	
	'icdcptsearch' => [
		'type' => 'dbsearch' //['imo','dbsearch'] only condition check whether its imo or not
	],
	'encode_decode_alg' => 'base64_alg', //'base64_alg','base64_rot13_alg','base64_rand_alg','base64_rot13_rand_alg','id_encode_alg'
    'twilioActiveCountryCode'=> '+91',
    // Practice master insurance types
    'cms_insurance_types' => ['OTHER','MEDICARE','MEDICAID','TRICARE','CHAMPVA','GROUP HEALTH PLAN','FECA BLK LUNG'],
    'reports_use_stored_procedure' => 0,
];
