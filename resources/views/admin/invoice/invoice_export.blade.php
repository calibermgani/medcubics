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
                font-size:11px !important;
                font-weight: 600 !important;
            }
            
            .tab_border th{line-height: 26px !important; color: #fff; padding: 0px 6px;font-weight: 600 !important;background: #808080;
                line-height: 30px !important;}
            .tab_border td{line-height: 20px !important;  padding: 0px 6px;background: #f2f2f2 !important}
            .totals td{line-height: 30px !important;font-size: 14px !important;}
            td{font-size: 11px !important;}
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
            .font600{font-weight:600 !important;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center !important;}
            h3{font-size:12px !important; color: #00877f; margin-bottom: -5px;}
            .pagenum:before { content: counter(page); }
            .header {top: -100px; position: fixed;}
            .footer {bottom: -85px; position: fixed;}
            .box-border{border: 1px solid #ccc !important;border-top: 0px solid #fff !important;}
            .box-border:first-child{border-top: 1px solid #ccc !important;}
            .new-border:first-child{border-bottom: 1px solid #ccc !important;}
            .med-red {color: #ff0000 !important;}  
            .boxes{-ms-transform: skewX(20deg); /* IE 9 */
  -webkit-transform: skewX(20deg); /* Safari prior 9.0 */
  transform: skewX(20deg);}
        </style>
    </head>	
    <body>
        <div class="header" style="background:#111a22">
            <table style="width:98%;">
                <tr>
                    <td><img src="img/invoice.png"></td>
                    <td style="line-height:20px !important;color:#c2c6c9">
                        <p style="margin-bottom:10px;background:#040d16;width:30px;height:30px;color:#e12527 !important;border-radius:10px !important;text-align: center;vertical-align: middle;font-size: 16px !important;line-height: 35px !important">W</p>
                        www.medcubics.com<br>info@medcubics.com</td>
                    <td style="line-height:20px !important;color:#c2c6c9">
                        <p style="margin-bottom:10px;background:#040d16;width:30px;height:30px;color:#0384bc !important;border-radius:10px !important;text-align: center;vertical-align: middle;font-size: 16px !important;line-height: 35px !important">P</p>
                        1-804-402-0581<br>info@medcubics.com</td>
                    <td style="line-height:20px !important;color:#c2c6c9">
                        <p style="margin-bottom:10px;background:#040d16;width:30px;height:30px;color:#dca707 !important;border-radius:10px !important;text-align: center;vertical-align: middle;font-size: 16px !important;line-height: 35px !important">A</p>
                        23 Clyde Road,Suite 201,<br>Somerset, NJ - 08873
                    
                    </td>
                </tr>
            </table>
            <!--<?php $img = base64_decode($image); ?>
            <img style="padding:0%; width: 100%" alt="Red dot" src={{ $image }} > -->

        </div>   
        
        <div class="footer med-green" style="margin-left:0px; background: #111a22;color:#fff;padding:8px;"> 
        
            <i class="boxes" style="width:40px; height:18px !important;margin-top:-10px !important;position: absolute; background: #039cdf;right:320px;"> </i>
            <i class="boxes" style="width:40px; height:18px !important;margin-top:-10px !important;position: absolute; background: #ee2729;right: 270px;margin-left: 100px"> </i>
            <i class="boxes" style="width:40px; height:18px !important;margin-top:-10px !important;position: absolute; background: #f0b406;right: 220px;margin-left: 100px"> </i>
            <i class="boxes" style="width:40px; height:18px !important;margin-top:-10px !important;position: absolute; background: #20b34e;right: 170px;margin-left: 100px"> </i>
        </div>


        <div style="padding-top:30px;margin-top:30px;width: 95%"> 
            <h1 style="font-size:40px !important;text-align: right;color:#c4bfbf !important;font-weight:300 !important;">INVOICE</h1>
            
            
            <table style="width:50%;border-collapse: collapse;position: absolute;padding-left: 20px;">
                <tr>
                    <td style="line-height:10px !important;font-size: 12px !important;font-style: italic !important;color:#767171 !important;">Invoice To</td>
                </tr>
                <tr>                    
                    <td style="border:none;text-align: left !important;font-size: 16px !important;color:#0999cd !important;font-weight: 500;line-height: 30px !important;margin-bottom: 10px !important;">{{$practice->practice_name}}</td>
                </tr>
                
                <tr>
                    <td style="border:none;text-align: left !important;font-size: 13px !important;color:#646464 !important;line-height:24px !important">{{@$practice->mail_add_1}}, {{@$practice->mail_add_2}}</td>
                </tr>
                
                <tr>
                    <td style="border:none;text-align: left !important;font-size: 13px !important;color:#646464 !important;line-height:24px !important">{{$practice->mail_city}}, {{$practice->mail_state}} - {{$practice->mail_zip5}} {{$practice->mail_zip4}}.</td>
                </tr>
                
                <tr>
                    <td style="border:none;text-align: left !important;font-size: 13px !important;color:#646464 !important;line-height:24px !important">Phone: {{($practice->phone !="")?"+ ".$practice->phone:" - Nil - "}}</td>
                </tr>
            </table>
            
            
            
            
            <table style="width:86%;border-collapse: collapse;margin-top:15px;margin-left:345px;position: absolute">
                <tr style="line-height: 20px !important;">
                    
                    <td  style="border:none;text-align: left !important;font-size: 16px !important;color:#0999cd !important;font-weight: 500;"></td>
                    <td  style="padding-right: 0px !important;border:1px solid #ccc;background: #f2f2f2;padding: 5px !important;font-size: 11px !important;">Invoice Date</td>
                    <td  style="padding-right: 0px !important;border:1px solid #ccc;padding:5px 10px !important">{{$data['invoice_date']}}</td>
                </tr>
                <tr style="line-height: 20px !important;">
                    <td  style="border:none;text-align: left !important;"></td>
                    <td  style="padding-right: 0px !important;border:1px solid #ccc;background: #f2f2f2;padding: 5px !important;font-size: 11px !important;">Invoice #</td>
                    <td  style="padding-right: 0px !important;border:1px solid #ccc;padding: 5px 10px  !important">{{$data['invoice_no']}}</td>
                </tr>
                <tr style="line-height: 20px !important;">
                    <td  style="border:none;text-align: left !important;"></td>
                    <td  style="padding-right: 0px !important;border:1px solid #ccc;background: #f2f2f2;padding: 5px !important;font-size: 11px !important;">Invoice Period</td>
                    <td  style="border:1px solid #ccc;padding: 5px 10px  !important">{{$data['invoice_period']}}</td>
                </tr>
                <tr style="line-height: 20px !important;">
                    <td  style="border:none;text-align: left !important;"></td>
                    <td  style="padding-right: 0px !important;border:1px solid #ccc;background: #f2f2f2;padding: 5px !important;font-size: 11px !important;">Total Due Amount ($)</td>
                    <td  style="border:1px solid #ccc;padding: 5px 10px  !important;color:#f10c0c !important;font-weight: 600;font-size: 12px !important">{!! App\Http\Helpers\Helpers::priceFormat($data['total_amount']) !!}</td>
                </tr>
            </table>            
        </div>
        <div style="padding-top:30px;margin-top:180px;" >	
            <table class="tab_border" style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; padding-left: 20px;border-collapse: collapse !important;">
                <thead>
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Products</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th class="text-center">Total ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < count($data['product']); $i++) { ?>
                        <tr>
                            <td>{{$data['start_date'][$i]}}</td>
                            <td>{{$data['end_date'][$i]}}</td>
                            <td>{{$data['product'][$i]}}</td>
                            <td>{{$data['units'][$i]}}</td>
                            <td>{{$data['quantity'][$i]}}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($data['total'][$i]) !!}</td>
                        </tr><?php } ?>
                </tbody>
            </table>
            <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 96%; padding-left: 20px;border-collapse: collapse !important;" class="totals">
                <tr>
                    <td style="width:50%"> </td>
                    <td class="text-right" style="padding-left: 80px;">Amount Due ($) : </td>
                    <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat($data['due_amount']) !!}</td>
                </tr>
                <tr>
                    
                    <td></td>
                    <td class="text-right">Previous Amount due ($) : </td>
                    <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat($data['previous_amount']) !!}</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="text-right">Tax % : </td>
                    <td class="text-right"> {!! App\Http\Helpers\Helpers::priceFormat($data['tax']) !!}</td>
                </tr>
                <tr >
                    <td style="line-height: 22px !important;"></td>
                    <td class="text-right" style="color:#fff;background: #00b0f0;line-height: 22px !important;">Total Due Amount ($) : </td>
                    <td class="text-right font600" style="color:#fff;background: #00b0f0;line-height: 22px !important;"> {!! App\Http\Helpers\Helpers::priceFormat($data['total_amount']) !!}</td>
                </tr>
            </table>
            
            
        </div>
    </body>
</html>