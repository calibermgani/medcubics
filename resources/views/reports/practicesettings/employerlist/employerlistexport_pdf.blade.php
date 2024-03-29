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
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}}</h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Employer Summary</i></p></td>
                </tr>
            </table>

            <table style="width:98%;">
                <tr>
                    <th colspan="3" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ date("m/d/y") }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(isset($createdBy)){{ $createdBy }}@endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>					
                            <th>Employer Name</th>
                            <th>Address Line 1</th>  		
                            <th>Address Line 2</th>  		
                            <th>City</th>  		
                            <th>ST</th>  		
                            <th>Zip Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($employer_filter_result) > 0)  
                        @php 
							@$total_adj = 0;
							@$patient_total = 0;
							@$insurance_total = 0;			
						@endphp

                        @foreach($employer_filter_result as $list)
                        <tr>
                            <td>{!! @$list->employer_name !!}</td>
                            <td>{!! @$list->address1 !!}</td>
                            <td>{!! @$list->address2 !!}</td>
                            <td>{!! @$list->city !!}</td>
                            <td style="text-transform: uppercase;">{!! @$list->state !!}</td>
                            <td class="text-left">{!! @$list->zip5 !!} @if(@$list->zip4){!! -@$list->zip4 !!} @endif</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>							
            </div>
        </div>
    </body>
</html>