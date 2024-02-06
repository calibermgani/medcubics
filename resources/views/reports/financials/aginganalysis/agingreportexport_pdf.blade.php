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
        <?php
            $header = $result['header'];
            $aging_report_list = $result['aging_report_list'];
            $practice_id = $result['practice_id'];
            $heading_name = App\Models\Practice::getPracticeName($practice_id);
        ?>
        <div class="header">
            <table style="padding: 0px 0px; margin-top: 0px;">
                <tr style="line-height:8px;">
                    <td style="line-height:8px;"><h3 class="text-center">{{$heading_name}} - <i>{{ @$title }}</i></h3></td>
                </tr>
                <tr style="line-height:8px;">
                    <td style="line-height:8px;padding-left: 10px !important;padding-right: 15px !important;">
                        <p class="text-center" style="font-size:11px !important;">
                            @if($headers !='' && !empty($headers))
                            <?php $i = 1; ?>
                            @foreach($headers as $header_name => $header_val)
                            <span>
                                <?php $hn = $header_name; ?>
                                {{ @$header_name }}</span> : {{ @$header_val}}@if($i<count((array)$headers)) | @endif <?php $i++; ?>
                            @endforeach
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none"><span>Created Date :</span> <span style="color:#00877f">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important;"><span>User :</span> <span class="med-orange">@if(Auth::check() && isset(Auth::user()->short_name)) {{ Auth::user()->short_name }} @endif</span></th>
                </tr>              
            </table>
        </div>

        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">
            <div>
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;">
                    <thead>
                        <tr>
                            <?php $count_r = 1; ?>
                            @foreach($header as $header_name => $header_val)
                            @if($count_r ==1)
                            <th style="text-align:center !important;border-right:1px solid #fff !important; ">{{ @$header_val }}</th>
                            @elseif($count_r % 2 == 0)
                            <th style="text-align:center !important;border-right:1px solid #fff !important;border-left:1px solid #fff !important;" colspan="2">{{ @$header_val }}</th>
                            @endif
                            <?php $count_r++;?>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($aging_report_list) && !empty($aging_report_list))
                        <tr>                     
                            @foreach($aging_report_list->name as $key => $name)    
                            <!-- This condition added for displaying price format-->
                            @if(@$name=='Claims')
                            <td class="font600 text-left" style="padding-left:5px;">{!! @$name !!}</td>
                            @else
                            @if($key > 0)
                            <td class="font600 text-right" style="padding-right:5px;">{!! @$name !!}($)</td>
                            @else
                            <td class="font600 text-right" style="padding-right:5px;">{!! @$name !!}</td>
                            @endif
                            @endif                                  
                            @endforeach
                        </tr>
                        @if(isset($aging_report_list->patient)) 
                        <tr>                    
                            @foreach($aging_report_list->patient as $key => $val)    
                            <!-- This condition added for displaying price format-->
                            @if(@$key==0)
                            <td class="text-left" style="padding-right:5px;">{!! @$val !!}</td>
                            @elseif(@$key%2==0)
                            <td class="text-right" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$val) !!}</td>
                            @else
                            <td class="text-left" style="padding-left:5px;">{!! @$val !!}</td>
                            @endif                                  
                            @endforeach
                        </tr>
                            @endif
                         <?php 
                            $insurance_provider = array_except((array)$aging_report_list,['name','patient','total','total_percentage']);
                            ?>
                            @if(isset($insurance_provider) && !empty($insurance_provider))             
                                @foreach($insurance_provider as $list)  
                                <tr>        
                                    @foreach($list as $key => $val)  
                                        <!-- This condition added for displaying price format-->
                                    @if(@$key==0)
                                        <td class="text-left" style="padding-right:5px;">{!! @$val !!}</td>
                                    @elseif(@$key%2==0)
                                        <td class="text-right" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$val) !!}</td>
                                    @else
                                        <td class="text-left" style="padding-left:5px;">{!! @$val !!}</td>
                                    @endif    
                                    @endforeach                              
                                </tr>
                                @endforeach
                            @endif
                        <tr>                     
                            @foreach($aging_report_list->total as $key => $val)    
                            <!-- This condition added for displaying price format-->
                            @if(@$key==0)
                            <td class="font600 text-left" style="padding-right:5px;">{!! @$val !!}</td>
                            @elseif(@$key%2==0)
                            <td class="font600 text-right" style="padding-right:5px;">{!! App\Http\Helpers\Helpers::priceFormat(@$val) !!}</td>
                            @else
                            <td class="font600 text-left" style="padding-left:5px;">{!! @$val !!}</td>
                            @endif                                  
                            @endforeach
                        </tr>
                        <tr>                     
                            @foreach($aging_report_list->total_percentage as $key => $val)    
                            <!-- This condition added for displaying price format-->
                            @if(@$key==0)
                            <td class="font600 text-left" style="padding-right:5px;">{!! @$val !!}</td>
                            @elseif(@$key%2==0)
                            <td class="font600 text-right"style="padding-right:5px;">{!! @$val !!}</td>
                            @else
                            <td class="font600 text-left" style="padding-left:5px;">{!! @$val !!}</td>
                            @endif                                  
                            @endforeach
                        </tr>
                        @endif                
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>