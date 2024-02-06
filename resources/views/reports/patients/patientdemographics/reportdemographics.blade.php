<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Demographic Sheet</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">   <?php $i = 0; ?>
						@foreach($search_by as $key=>$val)
							@if($i > 0){{' | '}}@endif
							<span class="med-green">{!! $key !!} :  </span>{{ @$val[0] }}                           
							<?php $i++; ?>
						@endforeach
					</div>                    
                </div>
            </div>
        </div>
        @if(count($patient_demographics_filter) > 0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive mobile-lg-scroll mobile-md-scroll">
                        <div class="ajax_table_list hide"></div>
                        <div class="data_table_list" id="js_ajax_part">
                            <table id="sort_list_noorder" class="table table-bordered table-striped  mobile-lg-width mobile-md-width margin-l-5">
                                <thead>
                                    <tr>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>MI</th>
                                        <th>Gender</th>
                                        <th>DOB</th>        
                                        <th>SSN</th>        
                                        <th>Acc No</th>         
                                        <th>Responsibility</th>         
                                        <th>Home Phone</th>          
                                        <th>Email ID</th>   
                                        <th>Guarantor Name</th>                         
                                        <th>ER Contact Person</th>                          
                                        <th>ER HomePhone</th> 
                                        <th>ER CellPhone</th>                       
                                        <th>Employer Name</th>                          
                                        <th>Primary Insurance/Policy ID</th>                          
                                        <th>Secondary Insurance/Policy ID</th>                          
                                        <th>Tertiary Insurance/Policy ID</th>                          
                                        <th>Address Line 1</th>                          
                                        <th>Address Line 2</th>                          
                                        <th>City</th>                          
                                        <th>State</th>                          
                                        <th>Zip Code</th>            
                                        <th>Created Date</th>       
                                        <th>User</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient_demographics_filter as $list)
                                    <?php
                                        $total_adj = 0;
                                        $patient_total = 0;
                                        $insurance_total = 0;
										
                                        $primary_ins = $secondary_ins = $tertiary_ins = $primary_policy_id = $secondary_policy_id = $tertiary_policy_id = $primary_ins_short_name = $secondary_ins_short_name = $tertiary_ins_short_name = '';
                                        if(isset($list->ins_category) && $list->ins_category != '') {
                                            $insurance = explode("^^", $list->ins_category);
                                            foreach($insurance as $ins_val) {
                                                if($ins_val != ''){
                                                    $det = explode("$$", @$ins_val);
                                                    if(($det[0]) == 'Primary'){
                                                        $primary_ins = $det[1];
                                                        $primary_policy_id = $det[2];
                                                        $primary_ins_short_name = $det[3];
                                                    }elseif(($det[0]) == 'Secondary'){
                                                        $secondary_ins = $det[1];
                                                        $secondary_policy_id = $det[2];
                                                        $secondary_ins_short_name = $det[3];
                                                    }else {
                                                        $tertiary_ins = $det[1];
                                                        $tertiary_policy_id = $det[2];
                                                        $tertiary_ins_short_name = $det[3];
                                                    }
                                                }
                                            }
                                        }
                                   
                                        $pat_category = $guar_l_name = $guar_f_name = $guar_m_name = $emrg_l_name = $emrg_f_name = $emrg_m_name = $emrg_hm_phone = $emrg_cl_phone = $emp_name = '';
                                        if(isset($list->pat_contact_category) && $list->pat_contact_category != '') {
                                            $patient_contacts = explode("^^", $list->pat_contact_category);
                                            foreach($patient_contacts as $patient_contacts_val){
                                                if($patient_contacts_val != ''){
                                                    $contact_list = explode("$$", $patient_contacts_val);
                                                    $pat_category = $contact_list[0];
                                                    if(($contact_list[0]) == 'Guarantor'){
                                                        $guar_l_name = $contact_list[1];
                                                        $guar_f_name = $contact_list[2];
                                                        $guar_m_name = $contact_list[3];
                                                    }elseif(($contact_list[0]) == 'Emergency Contact'){
                                                        $emrg_l_name = $contact_list[4];
                                                        $emrg_f_name = $contact_list[5];
                                                        $emrg_m_name = $contact_list[6];
                                                        $emrg_hm_phone = $contact_list[7];
                                                        $emrg_cl_phone = $contact_list[8];
                                                    }elseif(($contact_list[0]) == 'Employer') {
                                                        $emp_name = $contact_list[9];                                                        
                                                    }
                                                }
                                            }
                                        }
                                    ?>

                                    <tr style="cursor:default;">
                                        <td>{!! !empty($list->last_name)? @$list->last_name : '-Nill-' !!}</td>
                                        <td>{!! !empty($list->first_name)? @$list->first_name : '-Nill-' !!}</td>
                                        <td>{!! !empty($list->middle_name)? @$list->middle_name : '-Nill-' !!}</td>
                                        <td>{!! !empty($list->gender)? @$list->gender : '-Nill-' !!}</td>
                                        <td>{{ !empty($list->dob)? App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') : '-Nill-' }}</td>
                                        <td>@if(@$list->ssn != '') {!! @$list->ssn !!} @else -Nil- @endif</td>
                                        <td>@if(@$list->account_no !=''){!! @$list->account_no !!} @else -Nil- @endif</td>
                                        @if(@$list->is_self_pay == "No")
                                        <td>Insurance</td>
                                        @else
                                        <td>Self Pay</td>
                                        @endif
                                        <td>@if(@$list->phone != '') {!! @$list->phone !!} @else -Nil- @endif </td>
                                        <td>@if(@$list->email != '') {!! @$list->email !!} @else -Nil- @endif </td>
                                        <?php /* patients contacts details  */
                                        if(isset($list->pat_contact_category) && $list->pat_contact_category != ''){
                                            $guarantor = $guar_l_name.', '.$guar_f_name.' '.$guar_m_name;
                                            $guarantor_name = isset($guarantor) ? $guarantor : '';
                                            $emergency = $emrg_l_name.', '.$emrg_f_name.' '.$emrg_m_name;
                                            $emergency_contact_name = isset($emergency) ? $emergency : '';        
                                        ?>
                                        <td>@if($guarantor_name != ''){!! $guarantor_name !!} @else -Nil- @endif</td>
                                        <td>@if($emergency_contact_name !=''){!! $emergency_contact_name !!} @else -Nil- @endif</td>
                                        <td>@if($emrg_hm_phone != ''){!! $emrg_hm_phone !!} @else -Nil- @endif</td>
                                        <td>@if(emrg_cl_phone != ''){!! $emrg_cl_phone !!} @else -Nil- @endif</td>
                                        <td>@if($emp_name != '') {!! $emp_name !!} @else -Nil- @endif</td>
                                        <?php 
                                            } else {
                                        ?>
                                        @if(count((array)@$list->contact_details) > 0)
                                        <?php
                                            $contacts = json_decode(json_encode($list->contact_details), true);
                                            $guarenter_index = array_search("Guarantor", array_column($contacts, 'category'));
                                            $emergency_index = array_search("Emergency Contact", array_column($contacts, 'category'));
                                            $emp_index = array_search("Employer", array_column($contacts, 'category'));
                                            $emgname=App\Http\Helpers\Helpers::getNameformat(@$contacts[$emergency_index]['emergency_last_name'],@$contacts[$emergency_index]['emergency_first_name'],@$contacts[$emergency_index]['emergency_middle_name']);
                                            $gname=App\Http\Helpers\Helpers::getNameformat(@$contacts[$guarenter_index]['guarantor_last_name'],@$contacts[$guarenter_index]['guarantor_first_name'],@$contacts[$guarenter_index]['guarantor_middle_name']);                                      
                                        ?>                         
                                        <td>@if($gname != ''){!!@$gname !!} @else -Nil- @endif</td>
                                        <td>@if($emgname != '') {!!@$emgname!!} @else -Nil- @endif</td>
                                        <td>
                                            @if(@$contacts[$emergency_index]['emergency_home_phone'] != '') 
                                            {!! @$contacts[$emergency_index]['emergency_home_phone'] !!}
                                            @else
                                            -Nil-
                                            @endif
                                        </td>
                                        <td>
                                            @if(@$contacts[$emergency_index]['emergency_cell_phone'] != '')
                                            {!! @$contacts[$emergency_index]['emergency_cell_phone'] !!}
                                            @else
                                            -Nil-
                                            @endif
                                        </td>
                                        <td>
                                            @if(@$contacts[$emp_index]['employer_name'] != '')
                                            {!! @$contacts[$emp_index]['employer_name'] !!}
                                            @else
                                            -Nil-
                                            @endif
                                        </td>
                                        @else
                                        <td>-Nil-</td>
                                        <td>-Nil-</td>
                                        <td>-Nil-</td>
                                        <td>-Nil-</td>
                                        <td>-Nil-</td>
                                        @endif
                                            <?php } ?>
                                        <?php /* patients insurance details  */
                                            if(isset($list->ins_category) && $list->ins_category != '') {
                                        ?>
                                        <td>
                                            @if($primary_ins != '')
                                            {!! $primary_ins ."/". $primary_policy_id!!}
                                            @else
                                            -Nil-
                                            @endif
                                        </td>
                                        <td>
                                            @if($secondary_ins != '')
                                            {!! $secondary_ins ."/". $secondary_policy_id!!}
                                            @else
                                            -Nil-
                                            @endif
                                        </td>
                                        <td>
                                            @if($tertiary_ins != '')
                                            {!! $tertiary_ins ." / ". $tertiary_policy_id!!}
                                            @else
                                            -Nil-
                                            @endif
                                        </td>
                                        
                                        <?php 
                                            } else {
                                        ?>        
                                          
                                        @if(@$list->patient_insurance)
                                        <?php 
                                                $contacts = json_decode(json_encode($list->patient_insurance), true);

                                                $primary_index = array_search("Primary", array_column($contacts, 'category'));
                                                $secondary_index = array_search("Secondary", array_column($contacts, 'category'));
                                                $tertiary_index = array_search("Tertiary", array_column($contacts, 'category')); // echo '<pre>';print_r($contacts[$emp_index1]['insurance_id']);

                                        ?>
                                        <td>@if($contacts[$primary_index]['category'] == 'Primary')
                                               <?php   $insurance_id = $contacts[$primary_index]['insurance_id']; 
                                             $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName($insurance_id,'');?>
                                            {!!$insurance_name.'/'. $contacts[$primary_index]['policy_id']!!} @else -Nil- @endif
                                        </td>
                                        <td>@if($contacts[$secondary_index]['category'] == 'Secondary')
                                             <?php   $insurance_id = $contacts[$secondary_index]['insurance_id']; 
                                             $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName($insurance_id,'');?>
                                            {!!$insurance_name.'/'. $list->patient_insurance[0]->policy_id!!} @else -Nil- @endif</td>
                                        <td>@if($contacts[$tertiary_index]['category'] == 'Tertiary')
                                          <?php   $insurance_id = $contacts[$tertiary_index]['insurance_id']; 
                                             $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName($insurance_id,'');?>
                                            {!!$insurance_name.'/'. $contacts[$tertiary_index]['policy_id']!!} @else -Nil- @endif</td>
                                          @else
                                        <td>-Nil-</td>
                                        <td>-Nil-</td>
                                        <td>-Nil-</td>                                                               
                                        @endif 
                                            <?php } ?>

                                        <td>@if($list->address1 != '') {!! @$list->address1 !!} @else -Nil- @endif</td>
                                        <td>@if(@$list->address2 != '') {!! @$list->address2 !!} @else -Nil- @endif</td>
                                        <td>@if($list->city != '') {!! @$list->city !!} @else -Nil- @endif</td>
                                        <td>@if($list->state != '') {!! @$list->state !!} @else -Nil- @endif</td>
                                        <td>@if($list->zip5 != ''){!! @$list->zip5.' - '.$list->zip4 !!} @else -Nil- @endif</td>
                                        <td>{{ !empty($list->created_at)? App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') : '-Nill-' }}</td>
                                        <td>
                                            @if($list->created_by != 0 && isset($user_names[@$list->created_by]) )
                                            {!! !empty($list->created_by)? $user_names[@$list->created_by] : '-Nill-' !!}
                                            @endif                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <input type="hidden" id="pagination_prt" value="string"/>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->