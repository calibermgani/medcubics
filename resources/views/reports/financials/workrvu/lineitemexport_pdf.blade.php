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
            .c-paid, .Paid{color:#02b424;}
            .c-denied, .Denied{color:#d93800;}
            .m-ppaid, .c-ppaid{color:#2698F8}
            .ready-to-submit, .Ready{color:#5d87ff;}
            .Rejection{color: #f07d08;}
            .Hold{color:#110010;}
            .claim-paid{background: #defcda; color:#2baa1d !important;}
            .claim-denied{ color:#d93800 !important;}
            .claim-submitted{background: #caf4f3; color:#41a7a5 !important}
            .claim-ppaid{background: #dbe7fe; color:#2f5dba !important;}
            .Patient{color:#e626d6;}
            .Submitted{color:#009ec6;}
            .Pending{color:#313e50;}
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
            $workrvu_list = $result['workrvu_list'];
            $practice_id = $result['practice_id'];
            $search_by = $result['search_by'];
            $heading_name = App\Models\Practice::getPracticeName(@$practice_id);              
         ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{@$heading_name}} - <i>Work RVU Report</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            <?php $i = 0; ?>
                            @foreach($search_by as $key=>$val)
                            @if($i > 0){{' | '}}@endif
                            <span>{!! $key !!} : </span>{{ @$val }}                           
                            <?php $i++; ?>
                            @endforeach
                        </p>
                    </td>
                </tr>
            </table>

            <table style="width:98%;">
                <tr>
                    <th colspan="5" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y',@$practice_id) }}</span></th>
                    <th colspan="5" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(count($workrvu_list) > 0)
            <div>   
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse;">
                    <thead>
                      <tr>
                        <th>Transaction Date</th>
                        <th>DOS</th>
                        <th>Acc No</th>                        
                        <th>Patient Name</th>
                        <th>CPT/HCPCS</th>
                        <th>Description</th>
                        <th>Rendering</th>
                        <th>Facility</th>
                        <th>Units</th>
                        <th>Units Charge($)</th>
                        <th>Total Charge($)</th>
                        <th>Work RVU($)</th>                                                      
                    </tr> 
                    </thead>
                    <tbody>
                      @foreach($workrvu_list as  $result)
                    <?php                                                 
                        @$last_name = @$result->patient_details->last_name;
                        @$first_name = @$result->patient_details->first_name;
                        @$middle_name = @$result->patient_details->middle_name;
                        @$patient_name = App\Http\Helpers\Helpers::getNameformat($last_name, $first_name, $middle_name);
                        @$total_amt_charge = ($result->units > 0) ? (@$result->charge / @$result->units) : $result->charge;
                    ?>
                     <tr style="cursor:default;">
                        <td >{{ App\Http\Helpers\Helpers::timezone(@$result->transaction_date, 'm/d/y') }}</td> 
                        <td >{!! date('m/d/Y',strtotime(@$result->date_of_service)) !!}</td>
                        <?php 
                        if(isset($result->account_no) && $result->account_no != ''){
                        ?>
                        <td>{!! @$result->account_no !!}</td>
                        <td>{!! @$result->last_name .', '. @$result->first_name .' '. @$result->middle_name  !!}</td>
                        <?php 
                            } else {
                        ?>
                        <td >{{ @$result->patient_details->account_no}}</td>                         
                        <td >{!! @$patient_name !!}</td>
                        <?php } ?>
                        <td >{{ @$result->cpt_code}}</td>
                        <?php 
                        if(isset($result->medium_description) && $result->medium_description != ''){
                        ?>
                        <td>{{ @$result->medium_description }}</td> 
                        <td>{{ @$result->rendering_provider}}</td> 
                        <td>{{ @$result->facility}}</td>
                        <?php 
                            } else {
                        ?>
                        <td >{{ @$result->cptdetails->medium_description }}</td> 
                        <td >{{ @$result->claim_details->rend_providers->short_name}}</td> 
                        <td >{{ @$result->claim_details->facility->short_name}}</td>
                        <?php } ?>
                        <td >{{ @$result->units }}</td>
                        <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$total_amt_charge) !!}</td> 
                        <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge) !!}</td>
                        <?php 
                        if(isset($result->work_rvu) && $result->work_rvu != ''){
                        ?>
                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->work_rvu) !!}</td>
                        <?php 
                            } else {
                        ?>
                        <td class="text-right" data-format="0.00">{!! App\Http\Helpers\Helpers::priceFormat(@$result->cptdetails->work_rvu) !!}</td>
                        <?php } ?>
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