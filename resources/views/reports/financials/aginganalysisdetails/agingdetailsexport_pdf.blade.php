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
            .padding-0-4{padding: 0px 4px;}
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
        <?php $heading_name = App\Models\Practice::getPracticeName($practice_id); ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>Aging Analysis - Detailed</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
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
                    <th colspan="8" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="7" style="border:none;text-align: right !important"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                </tr>
            </table>
        </div>

        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">           
            @if(!empty($aging_report_list))  
      <div>  
        <div>
            <table style="width: 98%;">  
                <thead>
                    <tr>
                        <th>Acc No</th>
                        <th>Patient Name</th>
                        <th>Claim No</th>                     
                        <th>DOS</th> 
                        <th>Responsibility</th> 
                        <th>Policy ID</th> 
                        <th>Billing</th> 
                        <th>Rendering</th> 
                        <th>Facility</th> 
                        <th>First Submission Date</th> 
                        <th>Last Submission Date</th> 
                        <th>Charges ($)</th>
                        @if($show_flag == "All" || $show_flag == "Unbilled")
                        <th>Unbilled ($)</th>
                        @endif
                        @if($show_flag == "All" || $show_flag == "0-30")
                        <th>0-30 ($)</th>
                        @endif
                        @if($show_flag == "All" || $show_flag == "31-60")
                        <th>31-60 ($)</th>
                         @endif
                        @if($show_flag == "All" || $show_flag == "61-90")
                        <th>61-90 ($)</th>
                          @endif
                        @if($show_flag == "All" || $show_flag == "91-120")
                        <th>91-120 ($)</th>
                          @endif
                        @if($show_flag == "All" || $show_flag == "121-150")
                        <th>121-150 ($)</th>
                          @endif
                        @if($show_flag == "All" || $show_flag == "150-above")
                        <th> >150 ($)</th>
                       @endif  
                       <th>Pat AR ($)</th>                                                     
                       <th>Ins AR ($)</th>                                                     
                       <th>Tot AR ($)</th>                                                     
                       <th>AR Days</th>                                                     
                      <th>Claim Status</th>                     
                    </tr>
                </thead>
                <tbody>
                    <?php $temp_id = 0; $cnt = 0; $label = $search_lable.'_id'; ?>         
                  @foreach($aging_report_list as  $result)
                    @if(($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility'))
                      <?php $cnt++;?>
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
                          <?php $id = $result->billing_provider_id;?>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_charge)!!}</th>
                          @if($show_flag == "All" || $show_flag == "Unbilled")                      
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->unbilled)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "0-30")   
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days30)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "31-60") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days60)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "61-90") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days90)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "91-120")  
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days120)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "121-150") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days150)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "150-above") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->daysabove)!!}</th>
                          @endif 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_pat) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_ins) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total) !!}</th>
                          <th></th>
                          <th></th>
                        </tr>
                        <?php $temp_id = 0;?>  
                      @endif
                      <?php
                      if($search_lable == 'rendering_provider'){
                        $provider_id = $result->$label;
                        $provider_name = 'Rendering Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);
                      }
                      if($search_lable == 'facility'){
                        $provider_id = $result->$label;
                        $provider_name = 'Facility - '.App\Models\Facility::getFacilityName(@$provider_id);
                      }
                      if($search_lable == 'billing_provider'){
                         $provider_id = $result->$label;
                         $provider_name = 'Billing Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);
                      }
                      ?>
                      @if( $temp_id==0 && $temp_id != $result->$label)
                      <tr style="border: none !important; cursor:default;">
                        <td colspan ="12" class="font600 med-green">{{$provider_name}}</td>
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
                      </tr>
                      <?php $temp_id = $result->$label;?>
                      @endif
                    @endif
                     <tr>
                       
                         <td >{{ @$result->account_no}}</td> 
                         <td >{{ @$result->patient_name}}</td> 
                         <td >{{ @$result->claim_number}}</td> 
                         <td >{{ @$result->dos}}</td> 
                         <td >{{ @$result->responsibility}}</td> 
                         <td >{{ @$result->policy_id}}</td> 
                         <td >{{ App\Models\Provider::getProviderFullName(@$result->billing_provider_id)}}</td> 
                         <td >{{ App\Models\Provider::getProviderFullName(@$result->rendering_provider_id)}}</td> 
                         <td >{{ App\Models\Facility::getFacilityName(@$result->facility_id)}}</td> 
                         <td >{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->submited_date,'','Nil') !!}</td> 
                         <td >{!! App\Http\Helpers\Helpers::checkAndDisplayDateInInput(@$result->last_submited_date,'','Nil') !!}</td>
                         <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->total_charge)!!}</td> 
                        @if($show_flag == "All" || $show_flag == "Unbilled")                       
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->unbilled) !!}</td>  
                        @endif  
                        @if($show_flag == "All" || $show_flag == "0-30")                    
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days30)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "31-60") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days60)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "61-90") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days90)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "91-120") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days120)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "121-150") 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->days150)!!}</td>
                        @endif  
                        @if($show_flag == "All" || $show_flag == "150-above")      
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->daysabove)!!}</td> 
                        @endif 
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->pat_bal)!!}</td>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->ins_bal)!!}</td>    
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($result->total_bal) !!}</td>    
                        <td class="text-left @if($result->ar_days<0) med-red @endif">{{ ($result->ar_days!=0)?$result->ar_days:'0' }}</td>
                        <td >{{ $result->status}}</td> 
                    </tr>
                    @if( ($search_lable == 'billing_provider' || $search_lable == 'rendering_provider' || $search_lable == 'facility'))
                      @if ($cnt == count($aging_report_list))
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
                          <?php $id = $result->billing_provider_id;?>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_charge)!!}</th>
                          @if($show_flag == "All" || $show_flag == "Unbilled")                      
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->unbilled)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "0-30")   
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days30)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "31-60") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days60)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "61-90") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days90)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "91-120")  
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days120)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "121-150") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->days150)!!}</th>
                          @endif  
                          @if($show_flag == "All" || $show_flag == "150-above") 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->daysabove)!!}</th>
                          @endif 
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_pat) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total_ins) !!}</th>
                          <th class="text-right">${!! App\Http\Helpers\Helpers::priceFormat($summaries->$temp_id->total) !!}</th>
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
        </div>
    </body>
</html>