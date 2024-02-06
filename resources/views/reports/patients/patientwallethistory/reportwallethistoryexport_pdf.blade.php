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
            .text-right{text-align: right !important;padding-right: 5px;}
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
        <?php 
            $patient_wallethistory_filter = $result['patient_wallethistory_filter'];
            $search_by = $result['search_by'];
            $practice_id= $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td class="text-center" style="line-height:8px;"><h3>{{$heading_name}} - <i>Wallet History - Detailed</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;">
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
                    <th colspan="6" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="6" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            @if(!empty($patient_wallethistory_filter))
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>					
                            <th>Acc No</th>
                            <th>Patient Name</th>
                            <th>Payment ID</th>
                            <th>Mode of Pmt</th>
                            <th>Payment Date</th>
                            <th class="text-right">Total Payment($)</th>
                            <th class="text-right">Posted($)</th>  		
                            <th class="text-right">UnPosted($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patient_wallethistory_filter as $list)
                        <?php 
				$patient_name = App\Http\Helpers\Helpers::getNameformat(@$list->patient->last_name, @$list->patient->first_name, @$list->patient->middle_name);
				$bal_amt = @$list->pmt_amt - @$list->amt_used;
			?>
                        <tr>
                            <td>{!! @$list->patient->account_no !!}</td>
                            <td>{!! $patient_name !!}</td>
                            <td>{!! @$list->pmt_no !!}</td>
                            <td>{!! @$list->pmt_mode !!}</td>
                            <td>{{ App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') }}</td>
                            <td class="text-right" data-format="0.00" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$list->pmt_amt) !!}</td>
                            <td class="text-right" data-format="0.00" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$list->amt_used) !!}</td>
                            <td class="text-right" data-format="0.00" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$bal_amt) !!}</td>
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