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
                text-align:left !important;
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
            .text-right{text-align: right !important;padding-right:5px;}
            .text-left{text-align: left !important;}
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
        <?php 
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>{{$heading}} Workbench</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="7" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="6" style="border:none;text-align: right !important"><span>User :</span> <span class="">{{ Auth::user()->short_name }}</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">            
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th>DOS</th>
                            <th>Claim No</th>
                            <th>Patient Name</th>
                            <th>Provider</th>
                            <th>Facility</th>
                            <th>Billed To</th>
                            <th class="text-center">Billed Amt($)</th>
                            <th class="text-center">Paid($)</th>                        
                            <th class="text-center">AR Due($)</th>
                            <th>Status</th>
                            <th>Followup Dt</th>
                            <th>Assigned To</th>
                            <th>Priority</th>
							<th>Description</th>
                        </tr>
                    </thead>    
                    <tbody>
                        <?php 						
							$last_addin_problemlist = isset($last_addin_problemlist->problem_list_data)?$last_addin_problemlist->problem_list_data:$last_addin_problemlist;
						?>
                        @if(count((array)$last_addin_problemlist)>0 )
                        @foreach(@$last_addin_problemlist as $keys=>$last_addin_problemlist)
                        <?php
                            $patient = $last_addin_problemlist->patient;
                            $patient_name = App\Http\Helpers\Helpers::getNameformat($patient->last_name, $patient->first_name, $patient->middle_name);
                        ?>
                        <tr>
                            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$last_addin_problemlist->claim->date_of_service,'claimdate') }}</td>
                            <td>{{@$last_addin_problemlist->claim->claim_number}}</td>
                            <td>{{ @$patient_name }}</td>
                            <td>{{ @$last_addin_problemlist->claim->rendering_provider->short_name }}</td>
                            <td>{{ @$last_addin_problemlist->claim->facility_detail->short_name }}</td>
                            <td>
                                @if(@$last_addin_problemlist->claim->insurance_details)
                                    {!! App\Http\Helpers\Helpers::getInsuranceName(@$last_addin_problemlist->claim->insurance_details->id) !!}
                                @else
                                    Self
                                @endif
                            </td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$last_addin_problemlist->claim->total_charge) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$last_addin_problemlist->claim->total_paid) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$last_addin_problemlist->claim->balance_amt) !!}</td>
                            <td>{{ @$last_addin_problemlist->status }}</td>
                            <td>
                                <?php $fllowup_date = date("m/d/y", strtotime($last_addin_problemlist->fllowup_date)); ?>
                                @if(date("m/d/y") == $fllowup_date)
                                    <span class="">{{$fllowup_date}}</span>
                                @elseif(date("m/d/y") >= $fllowup_date)
                                    <span class="">{{$fllowup_date}}</span>
                                @else
                                    <span class="">{{$fllowup_date}}</span>
                                @endif
                            </td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($last_addin_problemlist->assign_user_id) }}</td>
                            <td>{{@$last_addin_problemlist->priority}}</td>
							<td>{{@$last_addin_problemlist->description}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>