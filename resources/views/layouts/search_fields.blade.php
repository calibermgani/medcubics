<div class="box no-border no-shadow no-background no-bottom hidden-print listing_search_append" style="display: none;">
    <div class="hide">
        <div class="box-tools pull-right med-green" style="right:0px;">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <div class="p-l-0 bg-white">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <?php
        $is_srch_filter = 0;
        if (Input::get('search') == 'yes') {
            $searchUserData = '';
            $is_srch_filter = 1;
        }
        $searchUserDetails = [];
        if (isset($searchUserData->search_fields_data) && Request::segment(1) != 'reports')
            $searchUserDetails = json_decode(@$searchUserData->search_fields_data, true);
		
        $fields = json_decode($search_fields->search_fields);
        $moreArr = [];
        $more_data = '';
		
        try {
            if(array_key_exists('practice_dbid', Session::all()))
                $end_date = \App\Http\Helpers\Helpers::timezone(date('m/d/Y H:i:s'),'m/d/Y',Session::all()['practice_dbid']);
            else
                $end_date = date('m/d/Y H:i:s');
            $start_date = date('m-01-Y', strtotime($end_date));
            ?>
            <script>
                var start_date = "{{$start_date}}";
                var end_date = "{{$end_date}}";
                var today = end_date;
            </script>
            <div class="search_fields_container col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <?php   $url = $_SERVER['REQUEST_URI'];    ?>
                
                @if(Request::segment(1) != 'reports')
                @if($url !== '/admin/userLoginHistory/settings')
              <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
                    <?php 
						$url = Request::url('/'); 
						@$user_search_data =  App\Http\Helpers\Helpers::getSearchUserDate($search_fields->page_name,$url);
                    ?>
                    @if(isset($searchUserData) && !empty($searchUserData))
                    <input type="checkbox" data-page-id='{{ $search_fields->id }}' data-userpage-id='{{ @$searchUserData->id }}' checked name="remember" id="search_remember" /><label for="search_remember" class="font600 med-orange cur-pointer">&nbsp;Remember Search @if(@$user_search_data != "") &emsp;<span class="med-gray">|</span>&emsp; @endif </label>
                    @elseif(Input::get('search') == 'yes' && !empty(Input::get('search')))
                    <input type="checkbox" data-page-id='{{ $search_fields->id }}' data-userpage-id='{{ @$searchUserData->id }}' checked name="remember" id="search_remember" /><label for="search_remember" class="font600 med-orange cur-pointer">&nbsp;Remember Search @if(@$user_search_data != "") &emsp;<span class="med-gray">|</span>&emsp; @endif </label>
                    @else
                    <input type="checkbox" data-page-id='{{ $search_fields->id }}' data-userpage-id='{{ @$searchUserData->id }}' name="remember" id="search_remember" /><label for="search_remember" class="font600 med-orange cur-pointer">&nbsp;Remember Search @if(@$user_search_data != "") &emsp;<span class="med-gray">|</span>&emsp; @endif  </label>
                    @endif
                    {!! @$user_search_data !!}
                </div> -->  
                @endif
                @endif
				<input type="hidden" data-page-id='{{ $search_fields->id }}' data-userpage-id='{{ @$searchUserData->id }}' name="remember" id="search_remember" />
                @foreach($fields as $list)
                <?php
                $key = array_search($list->label_name, array_column($searchUserDetails, 'label_name'));
                $label_name = str_replace('[]', '', $list->label_name);
                $label_data = is_array(Input::get($label_name)) ? Input::get($label_name)[0] : Input::get($label_name);

                if (!empty($label_data))
                    if (strpos($label_data, ',') !== false)
                        $userData = $label_data;
                    else
                        $userData = $label_data;
                elseif ($list->label_name == @$searchUserDetails[$key]['label_name'])
                    $userData = @$searchUserDetails[$key]['value'];
                else
                    $userData = '';
                ?>
                @if($list->show_type == 'show')
                @if($list->type == 'select')
                <?php
                if (strpos($userData, ',') !== false)
                    $userData = explode(',', $userData);

                $value = json_decode(json_encode($list->value), true);
                $data = [];
                $year_range = array_combine(range(date("Y")+0, date("Y")-4), range(date("Y")+0, date("Y")-4));
                $all =  array('' => 'All' ); 
                $yeardata = $all + $year_range;
				$listName = ucwords(strtolower($list->name));
                switch ($listName) {
                    case 'Insurance':
                    case 'Payer':
                    case 'Billed To':
					case 'Responsbility':
					case 'Responsibility':
						if($list->label_name == 'ar_responsibility') {
							$select_type = '';
							$data['All'] = 'All';
							$data['Patient'] = 'Patient';
							$data['Insurance'] = 'Insurance';
						} else {
							if ($list->label_name != "aging_insurance_id")
								$select_type = 'multiple';
							if (isset($srchFltr_patient_id) && $srchFltr_patient_id != ''){
								$data = App\Http\Helpers\Helpers::getInsuranceNameLists($srchFltr_patient_id, 1);
							}else { 
								$data = App\Http\Helpers\Helpers::getInsuranceNameLists(0, 1);
							}	
							if ($list->name == "Payer" || $list->name == "Billed To") {
								if ($list->label_name == "insurance_charge") {
									if(last(Request::segments())=='payments'){
										$a['self'] = "Patient Payments";
										$a['insurance'] = "Insurance Payments";
										$a['detail'] = "Patient Payments – Detailed Transaction";
									}else{
										$a['all'] = "All";
										$a['self'] = "Self-Pay";
										$a['insurance'] = "Insurance Only";
									}
									$select_type = '';
									$data = $a;
								}elseif($list->label_name =='insurance_id' && $list->name == "insurance_id"){
                                        $data = ['0' => 'Patient'] + $data; 
                                }elseif($list->label_name =='payer' && $list->name == "Payer"){
                                        $a['all'] = "All";
                                        $a['self'] = "Patient Payments";
                                        $a['insurance'] = "Insurance Payments";
                                        $select_type = '';
                                        $data = $a;
                                } else {
									if ($list->label_name != "aging_insurance_id") {
										if($search_fields->page_name == 'unbillied_report_listing'){
											// 
										} else {
											$data = ['0' => 'Patient'] + $data; 
											// Array unshift commented since it reassign the index.
											// array_unshift($data,"Patient");
										}  
									}	
								}
							}
							if ($list->label_name == "aging_insurance_id")
								$data = ['' => '-- Select --'] + $data;
						}
                        break;

                    case 'Rendering Provider':
                        $data = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering');
                        if (trim($list->class_name) == "hide aging_summary") {
                            $select_type = '';
                            if(!(Auth::user()->isProvider()))
                                $data = ['' => '-- Select --'] + $data;
                        } else {
                            $select_type = 'multiple';
                        }
                        break;   

                    case 'Provider Type':
                        $select_type = '';
                        if(Request::segment(3)!="providerSummary")
                            $data[0] = 'All';
                        $data[5] = 'Billing Provider';
                        $data[1] = 'Rendering Provider';                        
                        break;

                    case 'Billing Provider':
                        $data = App\Models\Provider::typeBasedAllTypeProviderlist('Billing');
                        if (trim($list->class_name) == "hide aging_summary") {
                            $select_type = '';
                            $data = ['' => '-- Select --'] + $data;
                        } else {
                            $select_type = 'multiple';
                        }
                        break;

                    case 'Referring Provider':
                        $select_type = 'multiple';
                        $data = [];
                        $data = App\Models\Provider::filterReferringProviderlist('Referring');
                        break;

                    case 'Facility':
                        $data = App\Http\Helpers\Helpers::getFacilityLists();
                        if (trim($list->class_name) == "hide aging_summary") {
                            $select_type = '';
                            $data = ['' => '-- Select --'] + $data;
                        } else {
                            $select_type = 'multiple';
                        }
                        break;

                    case 'Category':
						$select_type = 'multiple';
						if($list->label_name == 'responsibility_category') {
							$select_type = '';	
							$data[''] = '-- Select --';
							$data['Patient'] = 'Patient';	
						} 
						$data['Primary'] = 'Primary';
						$data['Secondary'] = 'Secondary';
						$data['Tertiary'] = 'Tertiary';
                        break;

                    case 'Status':
                        $select_type = 'multiple';
                        if ($search_fields->page_name == 'armanagement_workbench_total' || $search_fields->page_name == 'armanagement_workbench_assigned' ||  $search_fields->page_name == 'patient_workbench') {
                            $data['Assigned'] = 'Assigned';
                            $data['Inprocess'] = 'Inprocess';
                            $data['Completed'] = 'Completed';
                        } else if ($search_fields->page_name == 'appointment_list') {
                            $select_type = 'multiple';
                            $data['All'] = 'All';
                            $data['Scheduled'] = 'Scheduled';
                            $data['Complete'] = 'Complete';
                            $data['Rescheduled'] = 'Rescheduled';
                            $data['Canceled'] = 'Canceled';
                            $data['Encounter'] = 'Encounter';
                            $data['No Show'] = 'No Show';
                        } else if ($search_fields->page_name == 'uploadedListing') {
                            $select_type = 'multiple';
                            $data['All'] = 'All';
                            $data['Pending'] = 'Pending';
                            $data['In Progress'] = 'In Progress';
                            $data['Completed'] = 'Completed';
                            $data['Failed'] = 'Failed';
                        } else if ($search_fields->page_name == 'edi_reports') {
                            $select_type = 'multiple';
                            $data['EDI Acknowledgement Response'] = 'EDI Acknowledgement Response';
                            $data['EDI Response'] = 'EDI Response';
                            $data['Payer Response'] = 'Payer Response';
                            $data['ERA Response'] = 'ERA Response';
                            $data['Error'] = 'Error';
                        } else {
                            $data['All'] = 'All';
                            $data['Hold'] = 'Hold';
                            $data['Ready'] = 'Ready';
                            $data['Patient'] = 'Patient';
                            $data['Submitted'] = 'Submitted';
                            $data['Paid'] = 'Paid';
                            $data['Denied'] = 'Denied';
                            $data['Pending'] = 'Pending';
                            $data['Rejection'] = 'Rejection';
                            $value = (!empty($list->value) && $is_srch_filter != 1) ? ['Pending', "Hold"] : [];
                        }
                        break;

                    case 'Payment Type':
                        $select_type = 'multiple';
                        $data['Insurance'] = 'Insurance';
                        $data['Patient'] = 'Patient';
                        break;

                    case 'Payment Mode':
                        $select_type = 'multiple';
                        $data['Cash'] = 'Cash';
                        $data['Check'] = 'Check';
                        $data['Money Order'] = 'Money Order';
                        $data['Credit'] = 'Credit Card';
                        if (Request::segment(2) != 'patientwallethistory')
                            $data['EFT'] = 'EFT';
                        break;

                    case 'Gender':
                        $select_type = 'multiple';
                        $data['Male'] = 'Male';
                        $data['Female'] = 'Female';
                        $data['Others'] = 'Others';
                        break;

                    case 'Patient Type':
                        $select_type = '';
                        $data['New'] = 'New Patients';
                        $data['All'] = 'All Patients';
                        $data['App'] = 'App Patients';
                        break;

                    case 'Include':
                        $select_type = 'multiple';
                        $data['include_cpt_description'] = 'CPT Description';
                        $data['include_modifiers'] = 'Modifiers';
                        $data['include_icd'] = 'ICD';
                        break;

                    case 'Options':
                        $select_type = '';                        
						if ($search_fields->page_name == 'ar_deniallist')	{
							$data['line_items'] = 'Line Item Wise';
							$data['claim'] = 'Claim Wise';
							
						} else {
							$data[''] = '- Select Options -';
							$data['zero_payments'] = 'Zero payments';
							if(last(Request::segments())!='patient-insurance')
							$data['line_items'] = 'Line Item Payments';
						}
                        break;

                    case 'Bill Cycle':
                        $select_type = 'multiple';
                        $data['A - G'] = 'A - G';
                        $data['H - M'] = 'H - M';
                        $data['N - S'] = 'N - S';
                        $data['T - Z'] = 'T - Z';
                        break;

                    case 'Aging By':
                        $select_type = '';
                        $data['created_date'] = 'Transaction Date';
                        $data['submitted_date'] = 'Submitted Date';
                        $data['date_of_service'] = 'DOS';
                        break;

                    case 'Aging Days':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['Unbilled'] = 'Unbilled';
                        $data['0-30'] = '0-30';
                        $data['31-60'] = '31-60';
                        $data['61-90'] = '61-90';
                        $data['91-120'] = '91-120';
                        $data['121-150'] = '121-150';
                        if ($list->class_name == "aging_summary") {
                            $data['>150'] = '>150';
                        } else {
                            $data['150-above'] = '>150';
                        }
                        break;

                    case 'Group By':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['facility'] = 'Facility';
                        $data['rendering_provider'] = 'Rendering Provider';
                        $data['billing_provider'] = 'Billing Provider';
                        $data['payer'] = 'Payer';
                        break;

                    case 'Aging Group By':
                        $select_type = '';
                        $data['all'] = 'All';
                        $data['patient'] = 'Patient Balance';
                        $data['insurance'] = 'Insurance Balance';
                        $data['billing_provider'] = 'Billing Provider';
                        $data['rendering_provider'] = 'Rendering Provider';                        
                        $data['facility'] = 'Facility';                        
                        break;

                    case 'Aging Group':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['Patient'] = 'Patient';
                        $data['Insurance'] = 'Insurance';
                        break;

                    case 'User Type':
                        $select_type = 'multiple';
                        $data['Customer'] = 'Customer';
                        $data['practice_admin'] = 'Practice Admin';
                        $data['practice_user'] = 'Practice User';
                        break;

                    case 'Users':
                        $select_type = 'multiple';                    
                        $data = App\Http\Helpers\Helpers::user_list();
                        break;

                    case 'Practice':
                        $select_type = 'multiple';
                        $data = ['All']+App\Http\Helpers\Helpers::getPrac();                        
                        break; 
						
                    case 'Customer':
                        $select_type = 'multiple';
                        $data = ['All']+App\Http\Helpers\Helpers::getCus();
                        // array_unshift($data, 'All');
                        // $data = array_merge($data, $temp);
                        break; 

                    // case 'Date and Time of Attempt':
                    //     dd("dfsg");
                    //     $select_type = 'multiple';
                    //     $data = App\Http\Helpers\Helpers::user_list();
                    //     break;

                    case 'Assigned To':
                        $select_type = 'multiple';
                        $data = App\Http\Helpers\Helpers::user_list();
                        break;

                    case 'Year Option':
                        $select_type = '';
                        $data = App\Http\Helpers\Helpers::getPracticeYearList('option');
                        break;

                    case 'Choose Year':
                        $select_type = '';
                        $data = App\Http\Helpers\Helpers::getPracticeYearList();
                        break;

                    case 'Insurance Group By':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data = $data+App\Http\Helpers\Helpers::getInsuranceTypeName();
                        break;

                    case 'Refund Type':
                        $select_type = '';
                        //$data[''] = ''; 
                        $data['insurance'] = 'Insurance Refund';
                        $data['patient'] = 'Patient Refund';
                        break;

                    case 'Include ':
                        $select_type = '';
                        $data[''] = '';
                        $data['unposted'] = 'Unposted';
                        //$data['Wallet'] = 'Wallet';  																			
                        break;
						
                    case 'Adjustment Type':
                        $select_type = '';
                        $data['all'] = 'All';
                        $data['patient'] = 'Patient';
                        $data['insurance'] = 'Insurance';
                        break;

                    case 'Adjustment Reason':
                        $data['all'] = 'All';
                    
					case 'Reason For Visit':
                        $select_type = 'multiple';
                        $data = App\Models\ReasonForVisit::reasonForVists();
                        break;

                    case 'Priority':
                        $select_type = 'multiple';
                        $data['Low'] = 'Low';
                        $data['Moderate'] = 'Moderate';
                        $data['High'] = 'High';
                        break;
					
					case 'Statements':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['Yes'] = 'Yes';
                        $data['Hold'] = 'Hold';
						$data['Insurance Only'] = 'Insurance Only';
                        break;

					case 'Statement Category':
                        $select_type = 'multiple';
                        $data = App\Models\STMTCategory::getStmtCategoryList();
                        break;
					
					case 'Hold Reason':
                        $select_type = 'multiple';
                        $data = App\Models\STMTHoldReason::getStmtHoldReasonList();
                        break;
					
					case 'Claim Hold Reason':
						$select_type = 'multiple';
                        $data =  App\Http\Helpers\Helpers::getClaimHoldReasons();
						break;
					
					case 'Status Reason':
					case 'Sub Status':					
						$select_type = 'multiple';
                        $data =  App\Models\ClaimSubStatus::getClaimSubStatusList(1);
						if($search_fields->page_name == 'armanagement_listing')
							$data[0] = '--NIL--';
						
						break;
						
					case 'CPT Type':
					case 'Cpt Type':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['custom_type'] = 'Custom Range';
                        $data['cpt_code'] = 'CPT Code';
                        break;

                    case 'Sort By':
                        $select_type = '';
                        $data['CPT'] = 'CPT/HCPCS';
                        $data['DOS'] = 'DOS';
                        $data['Insurance'] = 'Insurance';
                        $data['payment_date'] = 'Payment Date';
                        $data['charge_date'] = 'Charge Date';
                        break;   

                    case 'Sort By Order':
                        $select_type = '';
                        $data['ASC'] = 'ASC';
                        $data['DESC'] = 'DESC';                       
                        break;   
					
					case 'CustomerName':
                        $select_type = '';                        
                        // $data[] = "All";
                        $data = App\Http\Helpers\Helpers::getCustomername();
                        // array_unshift($data, 'All');
                        // $data = array_merge($data, $temp);
                        break; 
                    
                    case 'Month':
                        $select_type = '';
                        $data[''] = 'All';
                        $data['01'] = 'Jan';
                        $data['02'] = 'Feb';
                        $data['03'] = 'Mar';
                        $data['04'] = 'April';
                        $data['05'] = 'May';
                        $data['06'] = 'June';
                        $data['07'] = 'July';
                        $data['08'] = 'Aug';
                        $data['09'] = 'Sep';
                        $data['10'] = 'Oct';
                        $data['11'] = 'Nov';
                        $data['12'] = 'Dec';                       
                        break; 

                    case 'Year':
                        $select_type = '';
                        $data = $yeardata;              
                        break;
						
					case 'Eligibility':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['Active'] = 'Eligible';                       
                        $data['Inactive'] = 'Ineligible';                       
                        $data['None'] = 'Unverified';                       
                        break;

                    case 'Appointment Status':    
                        $select_type = 'multiple';
                        $data['All'] = 'All';
                        $data['Scheduled'] = 'Scheduled';
                        $data['Complete'] = 'Complete';
                        $data['Rescheduled'] = 'Rescheduled';
                        $data['Canceled'] = 'Canceled';
                        $data['Encounter'] = 'Encounter';
                        $data['No Show'] = 'No Show';
                        break;
						
					case 'Claim Age':		
						$select_type = '';
						$data[''] = 'All';
                        $data['0-30'] = '0-30';
                        $data['31-60'] = '31-60';
                        $data['61-90'] = '61-90';
                        $data['91-120'] = '91-120';
                        $data['121-150'] = '121-150';
                        if ($list->class_name == "aging_summary") {
                            $data['>150'] = '>150';
                        } else {
                            $data['150-above'] = '>150';
                        }
						break;
					
					case 'Claim Status':
                        $select_type = 'multiple';                        
                        if ($search_fields->page_name == 'charge_delete') {
                            $data['Hold'] = 'Hold';
                            $data['Ready'] = 'Ready';
                            $data['Patient'] = 'Patient';
                            $data['Pending'] = 'Pending';
                        }else{
    						$data['All'] = 'All';
    						$data['Hold'] = 'Hold';
    						$data['Ready'] = 'Ready';
    						$data['Patient'] = 'Patient';
    						$data['Submitted'] = 'Submitted';
    						$data['Paid'] = 'Paid';
    						$data['Denied'] = 'Denied';
    						$data['Pending'] = 'Pending';
    						$data['Rejection'] = 'Rejection';
    						$value = (!empty($list->value) && $is_srch_filter != 1) ? ['Pending', "Hold"] : [];                        
                        }
                        break;	
						
					case 'Workbench Status':
						$select_type = '';         
						if( $list->class_name == 'selFnwbstatus'){
							$data['Include'] = 'Include';						
							$data['Exclude'] = 'Exclude';	
						} else {
							$data['All'] = 'All';						
							$data['Assigned'] = 'Assigned';
							$data['Inprocess'] = 'Inprocess';
							$data['Completed'] = 'Completed';	
						}                       
                        break;
						
					case '$0 Line Item':
						$select_type = '';         					
						$data['Include'] = 'Contains $0 Line Item';						
						$data['Exclude'] = 'Remove $0 Line Item';											   
                        break;
                        
                     case 'CPT/HCPCS Type':
					 case 'Cpt/hcpcs Type':
                        $select_type = '';
                        $data['All'] = 'All';
                        $data['custom_type'] = 'Custom Range';
                        $data['cpt_code'] = 'CPT/HCPCS Code';
                        break;                   

                    case 'CPT/HCPCS Category':
					case 'Cpt/hcpcs Category':
                        $select_type = '';
                        $data = App\Http\Helpers\Helpers::getProcedureCategory();
						$data = ['0' => 'All'] + $data; 
						// Array unshift commented since it reassign the index.
						// array_unshift($data,"All");						
                        break;    
                        
                    case 'Choose Date':
                        $select_type = '';
                        if(Request::segment(2)=="denials")
                            $data['submitted_date'] = 'Submitted Date';
                        else
                            $data['transaction_date'] = 'Transaction Date';
                        $data['DOS'] = 'DOS';   
                        if(Request::segment(2) !="denials")                             
                        $data['all'] = 'All';                                
                        break;      
						
					case 'From':
					case 'To':
						if($list->label_name == 'custom_type_from' || $list->label_name == 'custom_type_to') {	
							$select_type = '';
							$data[''] = 'All';
							$data = App\Http\Helpers\Helpers::favCptsList();
							$data[''] = 'Select';  
						}				
						break;      	
						
					case 'Include Refund':
						$select_type = '';
						$data['No'] = 'No';
						$data['Yes'] = 'Yes';						
						break;
						
					default:
                        $select_type = '';
                        $data = [];
                        break;
                }
                ?>
                @if($list->label_name=='choose_date')
                @push('transaction_date_scripts')
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#choose_date').val('{{!empty($userData)?$userData:"transaction_date"}}');
                        var choose_date = "{{!empty($userData)?$userData:"transaction_date"}}";
                        $('#select_date_of_service').parent().parent().hide();
                        $('#select_transaction_date').parent().parent().hide();
                        if(choose_date=='transaction_date'){
                            $('#select_transaction_date').parent().parent().show();
                            $('#select_date_of_service').parent().parent().hide();
                        } else if(choose_date==='DOS'){
                            $('#select_date_of_service').parent().parent().show();
                            $('#select_transaction_date').parent().parent().hide();
                        } else if (choose_date=='all' ){
                            $('#select_transaction_date').parent().parent().show();
                            $('#select_date_of_service').parent().parent().show();
                        }
                        $('#choose_date').val('{{!empty($userData)?$userData:"submitted_date"}}');
                        var choose_date = "{{!empty($userData)?$userData:"submitted_date"}}";
                        $('#date_of_service').parent().parent().hide();
                        $('#submitted_date').parent().parent().hide();
                        if(choose_date=='submitted_date'){
                            $('#submitted_date').parent().parent().show();
                            $('#date_of_service').parent().parent().hide();
                        } else if(choose_date==='DOS'){
                            $('#date_of_service').parent().parent().show();
                            $('#submitted_date').parent().parent().hide();
                        }
                    });
                </script>
                @endpush
                @endif
                @if($list->name=='Include')
                <div id="{{$list->label_name}}" class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
                    {!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
                    {!! Form::select($list->label_name, []+(array)$data,(isset($userData) && !empty($userData))?@$userData:$value,['class'=> $list->class_name. $list->type.' auto-generate select2 form-control form-select','data-field'=>$list->label_name,$select_type,'data-placeholder'=>'-- Select --','data-id'=>$list->label_name, 'autocomplete'=>'off', 'data-label-name' => $list->name]) !!}
                </div>
                @else
                <div id="{{$list->label_name}}" class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
                    {!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
					@if($list->name == 'Rendering Provider' && Auth::user()->isProvider())
						{!! Form::select($list->label_name, []+(array)$data,(isset($userData))?@$userData:$value,['class'=> $list->class_name. $list->type.' auto-generate select2 form-control form-select','data-field'=>$list->label_name,'data-id'=>$list->label_name, 'autocomplete'=>'off', 'data-label-name' => $list->name]) !!}
					@else
                        @if($select_type=='multiple')
						{!! Form::select($list->label_name, []+(array)$data,(isset($userData))?@$userData:$value,['class'=> $list->class_name. $list->type.' auto-generate select2 form-control form-select','data-field'=>$list->label_name,$select_type,'data-id'=>$list->label_name, 'autocomplete'=>'off', 'data-label-name' => $list->name,'data-placeholder'=>'-- Select --']) !!}
                        @else
                        {!! Form::select($list->label_name, []+(array)$data,(isset($userData))?@$userData:$value,['class'=> $list->class_name. $list->type.' auto-generate select2 form-control form-select','data-field'=>$list->label_name,$select_type,'data-id'=>$list->label_name, 'autocomplete'=>'off', 'data-label-name' => $list->name]) !!}
                        @endif
					@endif
                </div>
                @endif
				<?php $key = ''; ?>
                @endif

                @if($list->type == 'text')
                <div id="{{$list->label_name}}" class="{{@$searchUserDetails[$key]['label_name'] }} margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
                    {!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
                    {!! Form::text($list->label_name, @$userData ,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate form-control form-select','data-id'=>$list->label_name, 'autocomplete'=>'off', "data-id"=> "", 'data-label-name' => $list->name]) !!}
                </div>          
				<?php $key = ''; ?>
                @endif

                @if($list->type == 'date')
                <div  data-date="{{ @$searchUserDetails[$key]['value'] }}" class="margin-b-4 margin-t-10 margin-r-5 " style="float:left; width: 200px;">
                    <div class="right-inner-addon">
                        {!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!}
                        {!! Form::text($list->label_name, @$userData ,['class'=> $list->class_name. $list->type.' auto-generate bg-white form-control form-select js-date-range','data-id'=>'','autocomplete' => 'off','readonly'=>'readonly', 'data-label-name' => $list->name]) !!}<i class="fa fa-calendar-o"></i>
                    </div>
                </div>					
				<?php $key = ''; ?>
                
                @if($list->label_name=='transaction_date' || $list->label_name=='created_at' || $list->label_name=='scheduled_at' || $list->label_name=='select_transaction_date' || $list->label_name=='select_date_of_service')
                @if(!empty($userData))
				<?php $exp = explode('-', $userData); ?>
                @if(Request::segment(3) == 'problemlist')
					<script type="text/javascript">
						var label = 'Clear';
					</script>
                @else
                    <script type="text/javascript">
                        var label = 'Cancel';
                    </script>
                @endif
                @push('transaction_date_scripts')
                <script type="text/javascript">
                    $(document).ready(function () {
						var startDate = '{{$exp[0]}}';
						var endDate = '{{$exp[1]}}';
                        var name = "{{$list->label_name}}";
                        $('input[name="'+name+'"]').daterangepicker({
                            locale: {
                              format: 'MM/DD/YYYY',
                              cancelLabel: label
                            },
                            alwaysShowCalendars: true,
                            showDropdowns: true,
                            linkedCalendars:false,
                            startDate: startDate,
                            endDate: endDate,
                            ranges: {
                               'Today': [moment(today), moment(today)],
                               'Yesterday': [moment(today).subtract(1, 'days'), moment(today).subtract(1, 'days')],
                               'Last 7 Days': [moment(today).subtract(6, 'days'), moment(today)],
                               'Last 30 Days': [moment(today).subtract(29, 'days'), moment(today)],
                               'This Month': [moment(today).startOf('month'), moment(today)],
                               'Last Month': [moment(today).subtract(1, 'month').startOf('month'), moment(today).subtract(1, 'month').endOf('month')],
                               'This Year': [moment(today).startOf("year"), moment(today)],
                               'Last Year': [moment(today).subtract(1, "y").startOf("year"), moment(today).subtract(1, "y").endOf("year")]
                            }
                        });
                        $('input[name="'+name+'"]').on('cancel.daterangepicker', function(ev, picker) {
						  $(this).val('');
						});
                    });
                </script>
                @endpush
                @endif
                @endif
                @endif

                @elseif($list->show_type == 'more') 
                <?php
                $moreArr[$list->label_name] = $list->name;
                if (!empty($label_data)) {
                    $more_data .= $list->label_name . ',';
                    $moreArr[$list->label_name] = $list->name;
                }
                ?>
                @if($list->type == 'select')
					<?php
					if (strpos($userData, ',') !== false)
						$userData = explode(',', $userData);

					$value = json_decode(json_encode($list->value), true);
					$data = [];
					$listName = ucwords(strtolower($list->name));
					switch ($listName) {
						case 'Insurance':
						case 'Payer':
						case 'Billed To':
							$select_type = 'multiple';
							if (isset($srchFltr_patient_id) && $srchFltr_patient_id != '')
								$data = App\Http\Helpers\Helpers::getInsuranceNameLists($srchFltr_patient_id, 1);
							else
								$data = App\Http\Helpers\Helpers::getInsuranceNameLists(0, 1);
							if ($list->name == "Payer" || $list->name == "Billed To") {                            
								$data = ['0' => 'Patient'] + $data; 
								// Array unshift commented since it reassign the index.
								// array_unshift($data, "Patient");
							}
							break;

						case 'Rendering Provider':
							$select_type = 'multiple';
							$data = App\Models\Provider::typeBasedAllTypeProviderlist('Rendering');
							break;

						case 'Billing Provider':
							$select_type = 'multiple';
							$data = App\Models\Provider::typeBasedAllTypeProviderlist('Billing');
							break;

						case 'Referring Provider':
							$select_type = 'multiple';
							$data = App\Models\Provider::filterReferringProviderlist('Referring');
							break;

						case 'Facility':
							$select_type = 'multiple';
							$data = App\Http\Helpers\Helpers::getFacilityLists();
							break;

						case 'User':
							$select_type = 'multiple';
							$data = App\Http\Helpers\Helpers::user_list();
							break;

						case 'Category':
							$select_type = 'multiple';
							$data['Primary'] = 'Primary';
							$data['Secondary'] = 'Secondary';
							$data['Tertiary'] = 'Tertiary';
							break;

						case 'Payment Mode':
							$select_type = 'multiple';
							$data['Cash'] = 'Cash';
							$data['Check'] = 'Check';
							$data['Money Order'] = 'Money Order';
							$data['Credit'] = 'Credit Card';
							if (Request::segment(2) != 'patientwallethistory')
								$data['EFT'] = 'EFT';
							break;

						case 'Status':
							$select_type = 'multiple';
							$data['Hold'] = 'Hold';
							$data['Ready'] = 'Ready';
							$data['Patient'] = 'Patient';
							$data['Submitted'] = 'Submitted';
							$data['Paid'] = 'Paid';
							$data['Denied'] = 'Denied';
							$data['Pending'] = 'Pending';
							$data['Rejection'] = 'Rejection';
							break;

						case 'Gender':
							$select_type = 'multiple';
							$data['Male'] = 'Male';
							$data['Female'] = 'Female';
							$data['Others'] = 'Others';
							break;

						case 'Patient Type':
							$select_type = '';
							$data['New'] = 'New Patients';
							$data['All'] = 'All Patients';
							$data['App'] = 'App Patients';
							break;
							
						case 'Bill Cycle':
							$select_type = 'multiple';
							if($list->label_name=='bill_cycle[]' || $list->label_name =='bill_cycle'){
								$data['A - G'] = 'A - G';
								$data['H - M'] = 'H - M';
								$data['N - S'] = 'N - S';
								$data['T - Z'] = 'T - Z';
							}else{
								$data['include_cpt_description'] = 'CPT Description';
								$data['include_modifiers'] = 'Modifiers';
								$data['include_icd'] = 'ICD';
							}
							break;

						case 'Aging By':
							$select_type = '';
							$data['created_date'] = 'Transaction Date';
							$data['submitted_date'] = 'Submitted Date';
							$data['date_of_service'] = 'DOS';
							break;

						case 'Aging Days':
							$select_type = '';
							$data['All'] = 'All';
							$data['Unbilled'] = 'Unbilled';
							$data['0-30'] = '0-30';
							$data['31-60'] = '31-60';
							$data['61-90'] = '61-90';
							$data['91-120'] = '91-120';
							$data['121-150'] = '121-150';
							$data['150-above'] = '>150';
							break;

						case 'Group By':
							$select_type = '';
							$data['facility'] = 'Facility';
							$data['rendering_provider'] = 'Rendering Provider';
							$data['billing_provider'] = 'Billing Provider';
							$data['payer'] = 'Payer';
							break;

						case 'Aging Group':
							$select_type = '';
							$data['All'] = 'All';
							$data['Patient'] = 'Patient';
							$data['Insurance'] = 'Insurance';
							break;

						case 'Insurance Group By':
							$select_type = '';
							$data['All'] = 'All';
							$data['Primary'] = 'Primary';
							$data['Secondary'] = 'Secondary';
							$data['Tertiary'] = 'Tertiary';
							break;

						case 'Refund Type':
							$select_type = '';
							$data[''] = '';
							$data['insurance'] = 'Insurance Refund';
							$data['patient'] = 'Patient Refund';
							break;

						case 'Include ':
							$select_type = '';
							$data[''] = '';
							$data['Unposted'] = 'Unposted';
							//$data['Wallet'] = 'Wallet';  																			
							break;

						case 'Adjustment Type':
							$select_type = '';
							$data['all'] = 'All';
							$data['patient'] = 'Patient';
							$data['insurance'] = 'Insurance';
							break;

						case 'Adjustment Reason':
							$data['all'] = 'All';
							break;
						
						case 'From':
						case 'To':
							if($list->label_name == 'custom_type_from' || $list->label_name == 'custom_type_to') {	
								$select_type = '';
								$data[''] = 'All';
								$data = App\Http\Helpers\Helpers::favCptsList();
								$data[''] = 'Select';  
							}
							break;
							
						case 'Assigned To':
							$select_type = 'multiple';
							$data = App\Http\Helpers\Helpers::user_list();
							break;	
						
						default:
							$data = [];
							break;
					}
					?>

					<div class="{{ str_replace("[]","",$list->label_name) }}_more @if(isset($userData)) hide @endif">
						<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;" id="{{ str_replace("[]","",$list->label_name) }}">
							{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} <a href="#" data-placement="right" data-remove-id="{{ str_replace(' ', '-', strtolower($list->name)) }}"  data-toggle="tooltip" data-original-title="Remove" class="fa fa-times-circle js-remove-search-field"></a>
							{!! Form::select($list->label_name, []+(array)$data,(isset($userData) && count((array)$userData) > 0)?@$userData:$value,['class'=>$list->class_name. $list->type.' adv-search-text auto-generate select2 form-control form-select',$select_type,'data-id'=>str_replace("[]","",$list->label_name), 'autocomplete'=>'off', 'data-label-name' => $list->name]) !!}
						</div>
					</div>
					<?php $key = ''; ?>
                @endif

                @if($list->type == 'text')
					<div class="{{$list->label_name}}_more text-suggest @if(isset($userData)) hide @endif">
						<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;" id="{{$list->label_name}}">
							{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} <a href="#" data-placement="right" data-remove-id="{{ str_replace(' ', '-', strtolower($list->name)) }}" data-toggle="tooltip" data-original-title="Remove" class="fa fa-times-circle js-remove-search-field"></a>
							{!! Form::text($list->label_name, @$userData ,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate form-control form-select','data-id'=>$list->label_name, 'autocomplete'=>'off', 'data-label-name' => $list->name]) !!}
						</div>
					</div>
					<?php $key = ''; ?>
                @endif

                @if($list->type == 'date')
					<div class="{{$list->label_name}}_more text-suggest @if(isset($userData)) hide @endif">
						<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;" id="{{$list->label_name}}">
							{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} <a href="#" title="Remove" class="fa fa-close med-orange"></a>
							{!! Form::text($list->label_name, @$userData ,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate js-date-range form-control form-select','data-id'=>$list->label_name, 'autocomplete' => 'off','readonly'=>'readonly', 'data-label-name' => $list->name]) !!}
						</div>
					</div>
					<?php $key = ''; ?>
                @endif
                @endif
                @endforeach

                @if(!empty($moreArr) && isset($moreArr))
                <div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 200px;">
                    @if(isset($searchUserData->more_field_data))
						<?php $more_data = explode(',', $searchUserData->more_field_data) ?>
						{!! Form::label('More Options', 'More Options',['class'=>'control-label font600']) !!} 
						{!! Form::select('more',(array)$moreArr,$more_data,['autocomplete'=>'off' ,'class'=>'more_generate filter adv-search-text select2 form-control form-select','multiple'=>'multiple']) !!}
                    @elseif(isset($more_data))
                        <?php $more_data = explode(',', $more_data) ?>
                        {!! Form::label('More Options', 'More Options',['class'=>'control-label font600']) !!} 
                        {!! Form::select('more',(array)$moreArr,@$more_data,['autocomplete'=>'off' ,'class'=>'more_generate filter adv-search-text select2 form-control    form-select','multiple'=>'multiple']) !!}
                    @else   
						{!! Form::label('More Options', 'More Options',['class'=>'control-label font600']) !!} 
						{!! Form::select('more',(array)$moreArr,null,['autocomplete'=>'off' ,'class'=>'more_generate filter adv-search-text select2 form-control form-select','multiple'=>'multiple']) !!}
                    @endif
                </div>
                @endif
            </div>
	<?php
		} catch (Exception $e) {
			\Log::error("Error occured on search fields, Msg: " . $e->getMessage());
		}
	?>
		{!! HTML::style('css/search_fields.css') !!}
    </div>

	<?php if(Request::segment(1) != 'reports'){ ?>
		<div id="js_search_date_adj" class="js_date_validation js_date_option js_enter_date no-padding col-md-12" style="background: rgba(255,255,255,.5);margin: 3px;margin-top: -3px">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal no-padding js_search_part">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			   <div class="col-lg-11 col-md-12 col-sm-10 col-xs-12 no-padding">
					<input class="btn generate-btn js-search-filter pull-left  margin-b-10" value="Search" type="submit">
				</div>
				</div>

			</div>
		</div>
	<?php } ?>
</div>
@push('transaction_date_scripts')
<script type="text/javascript">    
    $('.select2-container').attr('autocomplete','off');
</script>
@endpush