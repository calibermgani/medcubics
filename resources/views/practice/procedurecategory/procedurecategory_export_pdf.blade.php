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
                    <td style="line-height:8px;"><p class="text-center" style="font-size:13px !important;"><i>Procedure Category List</i></p></td>
                </tr>
            </table>
            <table style="width:98%;">
                <tr>
                    <th colspan="3" style="border:none;text-align: left !important;"><span>Created Date :</span> <span style="">{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></th>
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
                            <th>S.No</th>
                            <th>Procedure Category</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Updated By</th>
                            <th>Updated On</th>
                        </tr>
                    </thead>    
                    <tbody>
                        @foreach($category as $keys=> $pro_cat)
                        <tr>
                            <td>{{$keys+1}}</td>
                            <td>{{ @$pro_cat->procedure_category }}</td>
                            <td>{{ @$pro_cat->status }}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($pro_cat->created_by) }}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname($pro_cat->updated_by) }}</td>
                            <td>
                                @if($pro_cat->updated_by !='' && $pro_cat->updated_by !='0')
                                    {{ App\Http\Helpers\Helpers::dateFormat($pro_cat->updated_at, 'date') }}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>