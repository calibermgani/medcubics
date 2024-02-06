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
                    <td colspan="6"><h3 class="text-left" style="margin-left:10px;" >Patient Aging Analysis</h3> </td>
                    <td colspan="3" class=""><p style="float:right; text-align: right;margin-right: 30px;"><span>Page No :</span> <span class="med-green pagenum"></span></p> </td>
                </tr>
            </table>
            <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
                <tr>
                    <th colspan="3" style=""><span>Created:</span> <span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></th>
                    <th colspan="3" style=""><span>User :</span> <span class="med-green">{{ Auth::user()->name }}</span></th>
                    <th colspan="3" style=""><span>Transaction Date :</span>  <span class="med-green">{!! @$start_date !!}   To {!! @$end_date !!}</span></th>
                </tr>              
            </table>
        </div>
        @if($export == "pdf")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
        <div style="padding-top:10px;">			
            @php $req=$ageingday   @endphp
            <div class="print-table" style="">	
                <table class="table-bordered" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; margin-left: 10px;  ">
                    <thead>
                        <tr style="background: #00837C; color: #fff;">					
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>0-30($)</th>
                            <th>31-60($)</th>
                            <th>61-90($)</th>
                            <th>91-120($)</th>
                            <th>121-150($)</th>
                            <th>>150($)</th>
                            <th>Total Pat Bal($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($req == 'all')
                        @foreach($patient_data as $key=>$patient_data)
                        <tr>
                            <td>{{@$key}}</td>
                            <?php
                            $pname = App\Models\Patients\Patient::where('account_no', $key)->select('last_name', 'middle_name', 'first_name')->first();
                            $name = App\Http\Helpers\Helpers::getNameformat(@$pname->last_name, @$pname->first_name, @$pname->middle_name);
                            ?>
                            <td>{!!@$name!!}</td>
                            @if(@$patient_data->{'0-30'} != '')
                            <td class="text-right">{!!@$patient_data->{'0-30'}!!}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if(@$patient_data->{'31-60'} != '')
                            <td class="text-right">{!!@$patient_data->{'31-60'}!!}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if(@$patient_data->{'61-90'} != '')
                            <td class="text-right">{!!@$patient_data->{'61-90'}!!}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if(@$patient_data->{'91-120'} != '')
                            <td class="text-right">{!!@$patient_data->{'91-120'}!!}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if(@$patient_data->{'121-150'} != '')
                            <td class="text-right">{!!@$patient_data->{'121-150'}!!}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if(@$patient_data->{'150-above'} != '')
                            <td class="text-right">{!!@$patient_data->{'150-above'}!!}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            <?php
                            $a = array(@$patient_data->{'0-30'}, @$patient_data->{'31-60'}, @$patient_data->{'61-90'}, @$patient_data->{'91-120'}, @$patient_data->{'121-150'}, @$patient_data->{'150-above'});
                            @$sum_value = array_sum($a);
                            ?>
                            <td class="text-right">{!!	@$sum_value!!}</td>
                        </tr>
                        @endforeach
                        @else
                        @foreach($ar_filter_result as $list)
                        <tr>
                            <td>{{@$list->account_no}}</td>
                            <td>{{@$list->full_name}}</td>
                            @if($req == '0-30')
                            <td class="text-right">{{@$list->balance_amt}}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if($req == '31-60')
                            <td class="text-right">{{@$list->balance_amt}}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if($req == '61-90')
                            <td class="text-right">{{@$list->balance_amt}}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if($req == '91-120')
                            <td class="text-right">{{@$list->balance_amt}}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if($req == '121-150')
                            <td class="text-right">{{@$list->balance_amt}}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            @if($req == '150-above')
                            <td class="text-right">{{@$list->balance_amt}}</td>
                            @else
                            <td class="text-right">0.00</td>
                            @endif
                            <td class="text-right">{{@$list->balance_amt}}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>							
            </div>
            {{-- <div> <p><label> Practice : </label>&nbsp; {!! @$heading_name !!}</p> </div>--}}
        </div>
        @if($export == "xlsx" || $export == "csv")
        <div class="footer med-green" style="margin-left:10px;"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
        @endif
    </body>
</html>