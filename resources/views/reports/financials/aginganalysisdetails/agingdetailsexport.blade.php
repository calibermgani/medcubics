<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Aging Analysis</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;                
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>
    <body> 
        <?php
        @$aging_report_list = $result['aging_report_list'];
        @$group_by = $result['group_by'];
        @$show_flag = $result['show_flag'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_lable = $result['search_lable'];
        @$search_by = $result['search_by'];
        @$summaries = $result['summaries'];
        $heading_name = App\Models\Practice::getPracticeDetails();
        ?>
        <table>
            <tr>                   
                <td colspan="24" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="24" style="text-align:center;">Aging Analysis - Detailed</td>
            </tr>
            <tr>
                <td colspan="24" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="24" style="text-align:center;">
<?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span>{!! $key !!} : </span>{{ @$val[0] }}                           
<?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        @if(count((array)$aging_report_list) > 0)  
        <div class="box-body no-padding">  
            <div class="table-responsive mobile-lg-scroll mobile-md-scroll col-lg-12 no-padding">
                <table class="table table-striped table-bordered table-separate">  
                    <thead>
                        <tr>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Patient Name</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>                     
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOS</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Responsibility</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Category</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Insurance Type</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;"  >Policy ID</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Billing</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">First Submission Date</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Submission Date</th> 
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Charges ($)</th>
                            @if($show_flag == "All" || $show_flag == "Unbilled")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Unbilled ($)</th>
                            @endif
                            @if($show_flag == "All" || $show_flag == "0-30")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >0-30 ($)</th>
                            @endif
                            @if($show_flag == "All" || $show_flag == "31-60")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >31-60 ($)</th>
                            @endif
                            @if($show_flag == "All" || $show_flag == "61-90")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >61-90 ($)</th>
                            @endif
                            @if($show_flag == "All" || $show_flag == "91-120")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >91-120 ($)</th>
                            @endif
                            @if($show_flag == "All" || $show_flag == "121-150")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >121-150 ($)</th>
                            @endif
                            @if($show_flag == "All" || $show_flag == "150-above")
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" > >150 ($)</th>
                            @endif  
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Pat AR ($)</th>                                                     
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Ins AR ($)</th>                                                     
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >Tot AR ($)</th>                                                     
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" >AR Days</th>                                                     
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Status</th>
                            <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Sub Status</th>					  
                        </tr>
                    </thead>
                    <tbody>
<?php $temp_id = 0;
$cnt = 0;
$label = $search_lable . '_id'; ?>         
                        @foreach($aging_report_list as  $result)
                        @if( ($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility'))
<?php $cnt++; ?>
                        @if($temp_id!=0 && $temp_id != $result->$label)
                        <tr>
                            <th>Totals</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
<?php $id = $result->billing_provider_id; ?>
                            <th class="text-right" data-format='#,##0.00'>{{ $summaries->$temp_id->total_charge }}</th>
                            @if($show_flag == "All" || $show_flag == "Unbilled")                      
                            <th style="text-align:right;@if($summaries->$temp_id->unbilled <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->unbilled }}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "0-30")   
                            <th style="text-align:right;@if($summaries->$temp_id->days30 <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->days30 }}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "31-60") 
                            <th style="text-align:right;@if($summaries->$temp_id->days60 <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->days60 }}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "61-90") 
                            <th style="text-align:right;@if($summaries->$temp_id->days90 <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->days90 }}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "91-120")  
                            <th style="text-align:right;@if($summaries->$temp_id->days120 <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->days120 }}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "121-150") 
                            <th style="text-align:right;@if($summaries->$temp_id->days150 <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->days150 }}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "150-above") 
                            <th style="text-align:right;@if($summaries->$temp_id->daysabove <0) color:#ff0000; @endif" data-format='#,##0.00'>${!! $summaries->$temp_id->daysabove!!}</th>
                            @endif 
                            <th style="text-align:right;@if($summaries->$temp_id->total_pat <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->total_pat  }}</th>
                            <th style="text-align:right;@if($summaries->$temp_id->total_ins <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->total_ins  }}</th>
                            <th style="text-align:right;@if($summaries->$temp_id->total <0) color:#ff0000; @endif" data-format='#,##0.00'>{{ $summaries->$temp_id->total  }}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        <?php $temp_id = 0; ?>  
                        @endif
                        <?php
                        if ($search_lable == 'rendering_provider') {
                            $provider_id = $result->$label;
                            $provider_name = 'Rendering Provider - ' . App\Models\Provider::getProviderFullName(@$provider_id);
                        }
                        if ($search_lable == 'facility') {
                            $provider_id = $result->$label;
                            $provider_name = 'Facility - ' . App\Models\Facility::getFacilityName(@$provider_id);
                        }
                        if ($search_lable == 'billing_provider') {
                            $provider_id = $result->$label;
                            $provider_name = 'Billing Provider - ' . App\Models\Provider::getProviderFullName(@$provider_id);
                        }
                        ?>
                        @if( $temp_id==0 && $temp_id != $result->$label)
                        <tr style="border: none !important; cursor:default;">
                            <td colspan ="12" class="font600 med-green" style="font-weight:600;color:#00877f;">{{$provider_name}}</td>
                            @if($show_flag == "All" || $show_flag == "Unbilled")                      
                            <td></td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "0-30")   
                            <td></td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "31-60") 
                            <td></td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "61-90") 
                            <td></td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "91-120")  
                            <td></td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "121-150") 
                            <td></td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "150-above") 
                            <td></td>
                            @endif  
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
<?php $temp_id = $result->$label; ?>
                        @endif
                        @endif
                        <tr style="cursor:default;">

                            <td >{{ @$result->account_no}}</td> 
                            <td >{{ @$result->patient_name}}</td> 
                            <td >{{ @$result->claim_number}}</td> 
                            <td >{{ @$result->dos}}</td> 
                            <td >{{ @$result->responsibility_name}}</td> 
                            <td >{{ (isset($result->insurance_category) && !empty($result->insurance_category) ? $result->insurance_category : '-Nil-' )}}</td> 
                            <td >{{ (isset($result->type_name) && !empty($result->type_name) ? $result->type_name : '-Nil-' )}}</td>
                            <td style="text-align:left;">{{ @$result->policy_id}}</td>
                            <td >{{ App\Models\Provider::getProviderFullName(@$result->billing_provider_id)}}</td> 
                            <td >{{ App\Models\Provider::getProviderFullName(@$result->rendering_provider_id)}}</td> 
                            <td >{{ App\Models\Facility::getFacilityName(@$result->facility_id)}}</td>  
                            <td>{!! !empty($result->submited_date)? $result->submited_date : '-Nill-' !!}</td>
                            <td>{!! !empty($result->last_submited_date)? $result->last_submited_date : '-Nill-' !!}</td>
                            <td class="text-right" data-format='#,##0.00'>{{ $result->total_charge }}</td>
                            @if($show_flag == "All" || $show_flag == "Unbilled")                       
                            <td style="text-align:right;@if(@$result->unbilled <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->unbilled !!}</td>  
                            @endif  
                            @if($show_flag == "All" || $show_flag == "0-30")                    
                            <td style="text-align:right;@if(@$result->days30 <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->days30 !!}</td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "31-60") 
                            <td style="text-align:right;@if(@$result->days60 <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->days60 !!}</td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "61-90") 
                            <td style="text-align:right;@if(@$result->days90 <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->days90 !!}</td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "91-120") 
                            <td style="text-align:right;@if(@$result->days120 <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->days120 !!}</td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "121-150") 
                            <td style="text-align:right;@if(@$result->days150 <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->days150 !!}</td>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "150-above")      
                            <td style="text-align:right;@if(@$result->daysabove <0) color:#ff0000; @endif" data-format='#,##0.00'>{!! @$result->daysabove !!}</td> 
                            @endif
                            <td style="text-align:right;@if($result->pat_bal<0) color:#ff0000; @endif" data-format='#,##0.00'>{!! $result->pat_bal !!}</td>
                            <td style="text-align:right;@if($result->ins_bal<0) color:#ff0000; @endif" data-format='#,##0.00'>{!! $result->ins_bal !!}</td>    
                            <td style="text-align:right;@if($result->total_bal<0) color:#ff0000; @endif" data-format='#,##0.00'>{!! $result->total_bal  !!}</td> 
                            <td style="text-align:right;@if($result->ar_days<0) color:#ff0000; @endif" data-format='#,##0.00'>{{ ($result->ar_days!=0)?$result->ar_days:'0' }}</td>   
                            <td >{{ $result->status}}</td>
                            <td>@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                        </tr>
                        @if( ($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility'))
                        @if ($cnt == count((array)$aging_report_list))
                        <tr>
                            <th>Totals</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
<?php $id = $result->billing_provider_id; ?>
                            <th class="text-right" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->total_charge !!}</th>
                            @if($show_flag == "All" || $show_flag == "Unbilled")                      
                            <th style="text-align:right;@if($summaries->$temp_id->unbilled <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->unbilled !!}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "0-30")   
                            <th style="text-align:right;@if($summaries->$temp_id->days30 <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->days30 !!}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "31-60") 
                            <th style="text-align:right;@if($summaries->$temp_id->days60 <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->days60 !!}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "61-90") 
                            <th style="text-align:right;@if($summaries->$temp_id->days90 <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->days90 !!}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "91-120")  
                            <th style="text-align:right;@if($summaries->$temp_id->days120 <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->days120 !!}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "121-150") 
                            <th style="text-align:right;@if($summaries->$temp_id->days150 <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->days150 !!}</th>
                            @endif  
                            @if($show_flag == "All" || $show_flag == "150-above") 
                            <th style="text-align:right;@if($summaries->$temp_id->daysabove <0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->daysabove !!}</th>
                            @endif 
                            <th style="text-align:right;@if($summaries->$temp_id->total_pat<0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->total_pat  !!}</th>
                            <th style="text-align:right;@if($summaries->$temp_id->total_ins<0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->total_ins  !!}</th>
                            <th style="text-align:right;@if($summaries->$temp_id->total<0) color:#ff0000; @endif" data-format='"$"#,##0.00_-'>{!! $summaries->$temp_id->total  !!}</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        @endif
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            <td colspan="25">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>