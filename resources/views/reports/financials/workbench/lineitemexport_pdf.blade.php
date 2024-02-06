<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important; padding: 10px;
            }
            .summary-table table tbody tr:nth-of-type(odd) td{
                border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;
            }
            th {
                text-align:center !important;
                font-size:10px !important;
                font-weight: 600 !important;
                border-top: 1px solid #ccc;
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
            .text-right{text-align: right; padding-right:5px;}
            .text-left{text-align: left;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #646464;font-weight: 600;}
            .margin-l-10{margin-left: 10px;} 
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center !important;}
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
        <?php $workbench_list = $request['workbench_list']; $heading_name = App\Models\Practice::getPracticeName($practice_id); ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>AR Workbench Report</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left:10px;padding-right:10px;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                            <span>{!! $key !!} : </span>{{ @$val[0] }}                           
                            <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="10" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="9" style="border:none;text-align: right !important"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(!empty($workbench_list))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
                    <thead>
                      <tr>
                        <th>Claim No</th>
                        <th>DOS</th>							
                        <th>Patient Name</th>							
                        <th>Rendering</th>                        							
                        <th>Billing</th>
                        <th>Facility</th>							
                        <th>Responsibility</th>							
                        <th>Category</th>							
                        <th>Charge Amt($)</th>
                        <th>Paid($)</th>
                        <th>Adj($)</th>
                        <th>Pat AR($)</th>
                        <th>Ins AR($)</th>
                        <th>AR Due($)</th>
                        <th>Claim Age</th>
                        <th>Claim Status</th>
						<th>Sub Status</th>
                        <th>Workbench Status</th>
                        <th>Followup Date</th>
                        <th>Assigned To</th>                                                      
                    </tr> 
                    </thead>
                    <tbody>
                        @foreach($workbench_list as  $result)
                        <?php                                          
                            $last_name = @$result->patient->last_name;
                            $first_name = @$result->patient->first_name;
                            $middle_name = @$result->patient->middle_name;
                            $patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                            $fin_details = @$result->claim->pmt_claim_fin_data;
                            $pat_due = ($result->claim->insurance_id == 0)?@$fin_details->total_charge-($fin_details->patient_paid+$fin_details->patient_adj+$fin_details->insurance_paid+$fin_details->insurance_adj+$fin_details->withheld):0;
							$ins_due = ($result->claim->insurance_id != 0) ?@$fin_details->total_charge-($fin_details->patient_paid+$fin_details->patient_adj+$fin_details->insurance_paid+$fin_details->insurance_adj+$fin_details->withheld):0;
							$tot_adj = @$fin_details->patient_adj + @$fin_details->insurance_adj+ @$fin_details->withheld;
							$tot_paid = @$fin_details->patient_paid + @$fin_details->insurance_paid;
							$ar_due = @$fin_details->total_charge-($fin_details->patient_paid+$fin_details->patient_adj+$fin_details->insurance_paid+$fin_details->insurance_adj+$fin_details->withheld);
                            $fllowup_date = date("m/d/y", strtotime($result->fllowup_date));
                            $responsibility = 'Patient';
                            $ins_category = 'Patient';
                            if($result->claim->insurance_details){
                                $responsibility = App\Http\Helpers\Helpers::getInsuranceName(@$result->claim->insurance_details->id);
                                $ins_category= @$result->insurance_category;
                            }
                        ?>
                        <tr>                       
                            <td>{{@$result->claim->claim_number}}</td>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$result->claim->date_of_service,'claimdate') }}</td>
                            <td>{{ $patient_name }}</td>
                            <td>
                                @if(@$result->claim->rendering_provider->short_name !='')
                                {{ str_limit(@$result->claim->rendering_provider->short_name,25,'...') }}
                                @else
                                -Nil-
                                @endif
                            </td>
                            <td>
                                @if(@$result->claim->billing_provider->short_name != '')
                                {{ str_limit(@$result->claim->billing_provider->short_name,25,'...') }}
                                @else
                                -Nil-
                                @endif
                            </td>
                            <td>
                                @if(@$result->claim->facility_detail->short_name != '')
                                {{ str_limit(@$result->claim->facility_detail->short_name,25,'...') }}
                                @else
                                -Nil-
                                @endif
                            </td>
                            <td>{{ $responsibility }}</td>
                            <td>{{ $ins_category }}</td>							
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->claim->total_charge) !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_paid) !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$tot_adj) !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$pat_due) !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$ins_due) !!}</td>
                            <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$ar_due) !!}</td>
                            <td>{{ @$result->claim->claim_age_days }}</td>
                            <td>{{ @$result->claim->status }}</td>
							<td>@if(isset($result->sub_status_desc) && $result->sub_status_desc !== null){{ $result->sub_status_desc}}@endif</td>
                            <td>{{ @$result->status }}</td>
                            <td>
                                @if(date("m/d/y") == $fllowup_date)
                                    <span class="med-orange">{{$fllowup_date}}</span>
                                @elseif(date("m/d/y") >= $fllowup_date)
                                    <span class="med-red">{{$fllowup_date}}</span>
                                @else
                                    <span class="med-gray">{{$fllowup_date}}</span>
                                @endif
                            </td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($result->assign_user_id) }}</td> 
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