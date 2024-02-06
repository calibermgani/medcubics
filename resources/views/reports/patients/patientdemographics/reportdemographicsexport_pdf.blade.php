<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;border-collapse:collapse !important;
            }
            th {
                text-align:left !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-bottom: 1px solid #ccc;
            }
            td{font-size: 9px !important;}
            tr, tr span, th, th span{line-height: 13px !important;}
            .table-summary tbody tr{line-height: 22px !important;}
            .table-summary tbody tr td{font-size:11px !important;}
            @page { margin: 100px -20px 100px 0px; }
            body {
                margin:0;
                font-size:9px !important; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;}
            .med-orange{color:#f07d08}
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;}
            .font13{font-size: 13px}
            .font600{font-weight:600;}
            .Patient{color:#e626d6;}
            .text-center{text-align: center;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}
        </style>
    </head>
    <body>
        <?php 
            $patient_demographics_filter = $result['patient_demographics_filter'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td class="text-center" style="line-height:8px;"><h3>{{$heading_name}} - <i>Demographic Sheet</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                                @if($i > 0){{' | '}}@endif
                                <span>{!! $key !!} :  </span>{{ @$val }}
                                <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="8" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="8" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(!empty($patient_demographics_filter))
            <div class="summary-table">
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse:collapse !important;">
                    <thead>
                        <tr >
                            <th>Acc No</th>
                            <th style="width:100px;">Patient Name</th>
                            <th>Gender</th>
                            <th>DOB</th>
                            <th>SSN</th>
                            <th>Home Phone</th>
                            <th>Email ID</th>
                            <th>Guarantor Name</th>
                            <th style="width:80px;">ER Contact</th>
                            <th>Employer Name</th>
                            <th>Pri Insurance</th>
                            <th>Sec Insurance</th>
                            <th>Ter Insurance</th>
                            <th>Address</th>
                            <th>Created Date</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody style="border-bottom: 1px solid #ccc !important;border-collapse:collapse !important;">
                        @foreach($patient_demographics_filter as $list)
                        <?php 
                        $total_adj = 0;
                        $patient_total = 0;
                        $insurance_total = 0;
                        
                        $set_title = (@$list->title)? @$list->title.". ":'';
                        $patient_name =    $set_title."". App\Http\Helpers\Helpers::getNameformat(@$list->last_name,@$list->first_name,@$list->middle_name);
                    ?>
                        <tr>
                            <td>{!! @$list->account_no !!}</td>
                            <td>{!! @$patient_name !!}</td>
                            <td>{!! @$list->gender !!}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') }}</td>
                            <td>{!! @$list->ssn !!}</td>
                            <td>{!! @$list->phone !!}</td>
                            <td>{!! @$list->email !!}</td>
                            @if(!empty(@$list->contact_details))
                            <?php
                            $contacts = json_decode(json_encode($list->contact_details), true);
                            $guarenter_index = array_search("Guarantor", array_column($contacts, 'category'));
                            $emergency_index = array_search("Emergency Contact", array_column($contacts, 'category'));
                            $emp_index = array_search("Employer", array_column($contacts, 'category'));
                            $emgname = App\Http\Helpers\Helpers::getNameformat(@$contacts[$emergency_index]['emergency_last_name'],@$contacts[$emergency_index]['emergency_first_name'],@$contacts[$emergency_index]['emergency_middle_name']);
                            $gname = App\Http\Helpers\Helpers::getNameformat(@$contacts[$guarenter_index]['guarantor_last_name'],@$contacts[$guarenter_index]['guarantor_first_name'],@$contacts[$guarenter_index]['guarantor_middle_name']);
                            ?>
                            <td>{!! @$gname !!}</td>
                            <td style="padding-top: 5px !important;">{!! @$emgname !!}<br>
                                {!! @$contacts[$emergency_index]['emergency_home_phone'] !!}<br>
                                {!! @$contacts[$emergency_index]['emergency_cell_phone'] !!}
                            </td>
                            <td>{!! @$contacts[$emp_index]['employer_name'] !!}</td>
                            @else
                            <td></td>
                            <td></td>
                            <td></td>
                            @endif
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
                                    $insurance_name = App\Http\Helpers\Helpers::getInsuranceName($insurance_id,'');
                                    ?>
                                {!!$insurance_name.'/'. $contacts[$primary_index]['policy_id']!!}
                                @endif
                            </td>
                            <td>
                                @if($contacts[$secondary_index]['category'] == 'Secondary')
                                    <?php
                                    $insurance_id = $contacts[$secondary_index]['insurance_id'];
                                    $insurance_name = App\Http\Helpers\Helpers::getInsuranceName($insurance_id,'');
                                    ?>
                                {!!$insurance_name.'/'. $list->patient_insurance[0]->policy_id!!}
                                @endif
                            </td>
                            <td>
                                @if($contacts[$tertiary_index]['category'] == 'Tertiary')
                                    <?php
                                    $insurance_id = $contacts[$tertiary_index]['insurance_id'];
                                    $insurance_name = App\Http\Helpers\Helpers::getInsuranceName($insurance_id,'');
                                    ?>
                                {!!$insurance_name.'/'. $contacts[$tertiary_index]['policy_id']!!}
                                @endif
                            </td>
                            @else
                            <td></td>
                            <td></td>
                            <td></td>
                            @endif
                            <td>{!! @$list->address1 !!}<br>{!! @$list->address2 !!}</td>
                            <td>{{ App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') }}</td>
                            <td>
                                @if($list->created_by != 0 && isset($user_names[@$list->created_by]) )
                                    {!! $user_names[@$list->created_by] !!}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>                            
            </div>
            @else
            <div><h5>No Records Found !!</h5></div>
            @endif
        </div>
    </body>
</html>