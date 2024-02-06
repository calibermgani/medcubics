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
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>CPT / HCPCS - Favorites List</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="4" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
                    <th colspan="3" style="border:none;text-align: right !important"><span>User :</span> <span class="">{{ Auth::user()->short_name }}</span></th>
                </tr>
            </table>
        </div>
        <div class="footer med-green" style="margin-left:15px;">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.<a style="text-align:right;right: 45px;position: absolute"><span>Page No :</span> <span class="med-green pagenum"></span></a></div>
        <div style="padding-top:10px;">            
            <div>	
                <table style="overflow: hidden;border-spacing: 0px; font-weight:normal;width: 98%; padding-left: 10px;border-collapse: collapse !important;">
                    <thead>
                        <tr>
                            <th>CPT / HCPCS</th>
                            <th>Short Description</th>
                            <th class="text-center">Billed Amount($)</th>
                            <th class="text-center">Allowed Amount($)</th>
                            <th>POS</th>
                            <th>Modifier</th>
                            <th>Type of Service</th>
                        </tr>
                    </thead>    
                    <tbody>
                        @foreach($cpt_favorites as $cpt_favorite)
                        <?php
                            $pos = (isset($cpt_favorite->cpt->pos_id) && ($cpt_favorite->cpt->pos_id != 0)) ? $cpt_favorite->cpt->pos_id : 'Nil';
                            $modifier = (isset($cpt_favorite->cpt->modifier_id) && $cpt_favorite->cpt->modifier_id != 0 || $cpt_favorite->cpt->modifier_id != '') ? $cpt_favorite->cpt->modifier_id : 'Nil';
                        ?>
                        <tr>
                            <td class="text-left">{{ $cpt_favorite->cpt->cpt_hcpcs }}</td>
                            <td>{{ $cpt_favorite->cpt->short_description }}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$cpt_favorite->cpt->billed_amount) !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$cpt_favorite->cpt->allowed_amount) !!}</td>
                            <td class="text-left">{{ $pos }}</td>
                            <td>{{ $modifier }}</td>
                            <td>{{ $cpt_favorite->cpt->type_of_service }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>