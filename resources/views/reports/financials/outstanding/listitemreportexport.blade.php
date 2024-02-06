<html>
    <head>
        <style>
            table{
                width:100%;
                font-size:13px; font-family:'Open Sans', sans-serif !important;
            } 
            .print-table table  tbody tr{ line-height: 26px !important; }
            .print-table table  tbody tr:nth-of-type(even) {
                background: #f3fffe !important;   
            }
            table tbody tr:nth-of-type(even) td{border-bottom: 1px solid #d7f4f2; border-top: 1px solid #d7f4f2;}
            th {
                text-align:left;
                font-size:13px;
                font-weight: 100 !important;
                border-radius: 0px !important;
            }
            tr, tr span, th, th span{line-height: 20px;}
            @page { margin: 110px -20px 100px 0px; }
            body { 
                margin:0;                 
                font-size:13px; font-family:'Open Sans', sans-serif;
                color: #646464;
            }
            .text-right{text-align: right;}
            .margin-t-m-10{margin-top: -10px;z-index: 999999;}
            .bg-white{background: #fff;} 
            .med-orange{color:#f07d08} 
            .med-green{color: #00877f;}
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
            .text-center{text-align: center;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -50px; position: fixed;}
        </style>
    </head>	
    <body>
        <div class="header">
            <table style="background:#f0f0f0; padding: 0px 0px; margin-top: -20px;">
                <tr style="background:#f0f0f0;">
                    <td colspan="10"><h3 class="text-left" style="margin-left:10px;" >Outstanding AR</h3> </td>
                    <td colspan="3" class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
                </tr>
            </table>
            <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
                <tr>
                    <th colspan="5" style=""><span>Created:</span> <span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></th>
                    <th colspan="5" style=""><span>User :</span> <span class="med-green">{{ Auth::user()->name }}</span></th>
                    @if($header !='' && count($header)>0)
                    @foreach($header as $header_name => $header_val)
                    <th colspan="3" style=""><span>{{ $header_name }} :</span>  <span class="med-green">{{ $header_val }}</span></th>
                    @endforeach
                    @endif
                </tr>              
            </table>
        </div>
        @if($export == "pdf")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
        <div style="padding-top:10px;">
            <div class="print-table" style="">	
                <table class="table-bordered" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; margin-left: 10px;  ">
                    <thead>
                        <tr style="background: #00837C; color: #fff;width:14px;">
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Claim No</th>
                            <th style="text-transform: uppercase;">DOS</th>
                            @if(@$column->insurance =='')<th>Insurance</th>@endif
                            @if(@$column->billing =='')<th>Billing</th>@endif 
                            @if(@$column->rendering =='')<th>Rendering</th>@endif
                            @if(@$column->facility =='')<th>Facility</th>@endif 
                            <th>Billed($)</th>
                            @if(@$column->bal_patient =='')<th>Ins Paid($)</th>@endif
                            <th>Pat Paid($)</th>
                            <th>Adj($)</th>
                            <th>Total Bal($)</th>
                        </tr>
                    </thead>    
                    <tbody>
                        @php $count = 1;   @endphp  
                        @foreach($claims as $claims_list)                       
                        @php
							$patient = $claims_list->patient;
							$set_title = (@$patient->title)? @$patient->title.". ":'';
							$patient_name = 	$set_title.App\Http\Helpers\Helpers::getNameformat(@$patient->last_name,@$patient->first_name,@$patient->middle_name); 
						@endphp 
                        <tr>                              
                            <td>{{ @$claims_list->patient->account_no }}</td>                           
                            <td>{{ $patient_name }}</td>                           
                            <td>{{@$claims_list->claim_number}}</td>
                            <td>{{date('m/d/Y',strtotime($claims_list->date_of_service))}}</td>
                            @if(@$column->insurance =='')
                            @if(empty($claims_list->insurance_details))
                            <td>Self</td>  
                            @else                               
                            <td>{{str_limit(@$claims_list->insurance_details->insurance_name,15,' ...')}}</td>
                            @endif  
                            @endif 
                            @if(@$column->billing =='')
                            <td> {{str_limit(@$claims_list->billing_provider->provider_name,15,' ...')}}</td>
                            @endif
                            @if(@$column->rendering =='')
                            <td>{{str_limit(@$claims_list->rendering_provider->provider_name,15,' ...')}}</td>
                            @endif

                            @if(@$column->facility =='')
                            <td>{{str_limit(@$claims_list->facility_detail->facility_name,15,' ...')}}</td> 
                            @endif
                            <td style="text-align:right;">{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_charge)}}</td>
                            @if(@$column->bal_patient =='')
                            <td style="text-align:right;">{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->insurance_paid)}}</td>
                            @endif
                            <td style="text-align:right;">{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->patient_paid)}}</td>
                            <td style="text-align:right;">{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->total_adjusted)}}</td>
                            <td style="text-align:right;">{{App\Http\Helpers\Helpers::priceFormat(@$claims_list->balance_amt)}}</td>
                        </tr>
                        @php $count++;   @endphp 
                        @endforeach
                    </tbody>   
                </table>		
            </div>
        </div>
        @if($export == "xlsx" || $export == "csv")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
    </body>
</html>