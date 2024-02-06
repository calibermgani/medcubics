<?php 
try{
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Demographic Sheet</title>
    </head>
    <body>
        <?php 
            @$patient_demographics_filter = $result['patient_demographics_filter'];
            @$start_date = $result['start_date'];
            @$end_date = $result['end_date'];
            @$createdBy = $result['createdBy'];
            @$practice_id = $result['practice_id'];
            @$user_names = (array)$result['user_names'];
            @$search_by = $result['search_by'];
            @$heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>
                <td colspan="25" style="text-align:center;color: #00877f;font-weight: 800;font-size:13.5px;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="25" style="text-align:center;font-size:12px;">Demographic Sheet</td>
            </tr>
            <tr>
                <td colspan="25" style="text-align:center;font-size:12px;">User :@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif | Created :{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</td>
            </tr>
            <tr>
                <td colspan="25" style="text-align:center;font-size:12px;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        {!! $key !!} :  {{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(isset($patient_demographics_filter) && $patient_demographics_filter !='')
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">First Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">MI</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Gender</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOB</th>        
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">SSN</th>        
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>         
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Responsibility</th>         
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Home Phone</th>          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Email ID</th>   
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Guarantor Name</th>                         
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">ER Contact Person</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">ER HomePhone</th> 
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">ER CellPhone</th>                       
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Employer Name</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Primary Insurance/Policy ID</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Secondary Insurance/Policy ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Tertiary Insurance/Policy ID</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Address Line 1</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Address Line 2</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">City</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">State</th>                          
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Zip Code</th>           
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Created Date</th>       
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">User</th>  
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
                <tr>
                    <td>{!! @$list->last_name !!}</td>
                    <td>{!! @$list->first_name !!}</td>
                    <td>{!! @$list->middle_name !!}</td>
                    <td>{!! @$list->gender !!}</td>
                    <?php /* from stored procedure  */
                        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0){
                    ?>
                    <td style="width:15px;">{!! @$list->dob !!}</td>
                    <?php 
                        } else {
                    ?>
                    <td style="width:15px;">{{ App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') }}</td>
                    <?php } ?>
                    <td style="text-align:left;width:15px;">@if(@$list->ssn != '') {!! @$list->ssn !!} @else -Nil- @endif</td>
                    <td style="text-align:left;">{!! @$list->account_no !!}</td>
                    @if(@$list->is_self_pay == "No")
                    <td>Insurance</td>
                    @else
                    <td>Self Pay</td>
                    @endif
                    <td style="text-align:left;">@if(@$list->phone != '') {!! @$list->phone !!} @else -Nil- @endif </td>
                    <td>@if(@$list->email != '') {!! @$list->email !!} @else -Nil- @endif</td>
                    <?php /* patients contacts details  */
                    if(isset($list->pat_contact_category) && $list->pat_contact_category != ''){
                        $guarantor = ($guar_l_name != '' && $guar_f_name != '')? ($guar_l_name.', '.$guar_f_name.' '.$guar_m_name) : '-Nil-';
                        $guarantor_name = isset($guarantor) ? $guarantor : '';
                        $emergency =($emrg_l_name != '' && $emrg_f_name != '') ? ($emrg_l_name.', '.$emrg_f_name.' '.$emrg_m_name) : '-Nil-';
                        $emergency_contact_name = isset($emergency) ? $emergency : '';
                    ?>
                    <td>@if($guarantor_name != ''){!! $guarantor_name !!} @else -Nil- @endif</td>
                    <td>@if($emergency_contact_name !=''){!! $emergency_contact_name !!} @else -Nil- @endif</td>
                    <td>@if($emrg_hm_phone != ''){!! $emrg_hm_phone !!} @else -Nil- @endif</td>
                    <td>@if($emrg_cl_phone != ''){!! $emrg_cl_phone !!} @else -Nil- @endif</td>
                    <td>@if($emp_name) {!! $emp_name !!} @else -Nil- @endif</td>
                    <?php 
                        } else {
                    ?>
                    @if(isset($list->contact_details) && $list->contact_details !='')
                    <?php
                        $contacts = json_decode(json_encode($list->contact_details), true);
                        $guarenter_index = array_search("Guarantor", array_column($contacts, 'category'));
                        $emergency_index = array_search("Emergency Contact", array_column($contacts, 'category'));
                        $emp_index = array_search("Employer", array_column($contacts, 'category'));
                        $emgname = App\Http\Helpers\Helpers::getNameformat(@$contacts[$emergency_index]['emergency_last_name'], @$contacts[$emergency_index]['emergency_first_name'], @$contacts[$emergency_index]['emergency_middle_name']);
                        $gname = App\Http\Helpers\Helpers::getNameformat(@$contacts[$guarenter_index]['guarantor_last_name'], @$contacts[$guarenter_index]['guarantor_first_name'], @$contacts[$guarenter_index]['guarantor_middle_name']);
                    ?>                         
                    <td>@if($gname != ''){!!@$gname !!} @else -Nil- @endif</td>
                    <td>@if($emgname != '') {!!@$emgname!!} @else -Nil- @endif</td>
                    <td style="text-align:right;">
                        @if(@$contacts[$emergency_index]['emergency_home_phone'] != '') 
                        {!! @$contacts[$emergency_index]['emergency_home_phone'] !!}
                        @else
                        -Nil-
                        @endif
                    </td>
                    <td style="text-align:right;">
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
                        $tertiary_index = array_search("Tertiary", array_column($contacts, 'category'));   
                    ?>
                    <td>
                        @if($contacts[$primary_index]['category'] == 'Primary')
                        <?php
                        $insurance_id = $contacts[$primary_index]['insurance_id'];
                        $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName(@$insurance_id, '');
                        echo  $insurance_name.'/'. $contacts[$primary_index]['policy_id'];
                        ?>
                        @else 
                        -Nil- 
                        @endif
                    </td>
                    <td>@if($contacts[$secondary_index]['category'] == 'Secondary')
                        <?php
                        $insurance_id = $contacts[$secondary_index]['insurance_id'];
                        $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName(@$insurance_id, '');
                        echo $insurance_name.'/'. $list->patient_insurance[0]->policy_id;
                        ?>
                        @else 
                            -Nil- 
                        @endif
                    </td>
                    <td>@if($contacts[$tertiary_index]['category'] == 'Tertiary')
                        <?php
                        $insurance_id = $contacts[$tertiary_index]['insurance_id'];
                        $insurance_name = App\Http\Helpers\Helpers::getInsuranceFullName(@$insurance_id, '');
                        echo $insurance_name.'/'. $contacts[$tertiary_index]['policy_id']; 
                        ?>
                        @else 
                            -Nil- 
                        @endif
                    </td>
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
                    <td>{{ App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') }}</td>
                    <td>
                        @if($list->created_by != 0 )
                            {!! \App\Http\Helpers\Helpers::user_names($list->created_by) !!} - {!! \App\Http\Helpers\Helpers::getUserFullName($list->created_by) !!}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <table>
            <tr>
                <td colspan="25">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>

<?php 
} catch(Exception $e) {
    \Log::info($e);
}
?>