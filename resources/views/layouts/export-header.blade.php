<!DOCTYPE html>
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
            .p-r-10{padding-right: 10px !important;}
            .font13{font-size: 13px} 
            .font600{font-weight:600;}
            .padding-0-4{padding: 0px 4px;}
            .text-center{text-align: center;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
            .pagenum:before { content: counter(page); }            
        </style>
        <script>
            function subst() {
                var vars = {};
                var x = document.location.search.substring(1).split('&');
                for (var i in x) {
                    var z = x[i].split('=', 2);
                    vars[z[0]] = unescape(z[1]);
                }
                var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
                for (var i in x) {
                    var y = document.getElementsByClassName(x[i]);
                    for (var j = 0; j < y.length; ++j)
                        y[j].textContent = vars[x[i]];
                }
            }
        </script>
    </head>
    <body onload="subst()">
        <table style="background:#f0f0f0;padding: 0px 0px;">           
            <tr>
                <th>
                    <h3 class="" style="margin-left:10px;" >
                        @if($patients)
                        Patients
                        @elseif($charges)
                        Charges
                        @elseif($payments)
                        Payments
                        @endif
                    </h3>
                </th>
                <th><p style="text-align: right;margin-right: 30px;">Page <span class="page med-green"></span> of <span class="topage med-green"></span></p></th>
            </tr>
        </table>
        <table style="border-spacing: 0px;width:97%; margin-left: 10px; margin-top: 15px; border-bottom: 1px dashed #f0f0f0; padding-bottom: 15px;">
            <tr>
                <th><span>Created:</span> <span class="med-green">{{ date("m/d/y") }} - </span><span class="med-orange">{{ date("H:i A") }}</span></th>
                <th style="text-align: right;"><span>User :</span> <span class="med-green">{{ Auth::user()->name }}</span></th>
            </tr>
        </table>
    </body>
</html>