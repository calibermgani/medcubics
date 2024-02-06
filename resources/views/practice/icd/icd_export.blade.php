<!DOCTYPE html>
<html lang="en">
    <head>
        <title>ICD</title>
        <style>
            table tbody tr  td{
                font-size: 9px !important;
                border: none !important;
            }
            table tbody tr th {
                text-align:center !important;
                font-size:10px !important;                
                color:#000 !important;
                border:none !important;
                border-radius: 0px !important;
            }
            table thead tr th{border-bottom: 5px solid #000 !important;font-size:10px !important}
            .text-right{text-align: right;}
            .text-left{text-align: left;}
            .text-center{text-align: center;}
            .med-green{color: #00877f;}
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>	
    <body>
        <?php 
            @$icd_arr = $result['icd_arr'];
            @$export = $result['export'];
            @$practice_details = App\Models\Practice::getPracticeDetails();
            @$heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="5" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;">ICD List</td>
            </tr>
            <tr>
                <td valign="middle" colspan="5" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Code</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Short Description</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Gender</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Effective Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Inactive Date</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($icd_arr as $icd)
                <?php
                    if(isset($icd->effectivedate) && $icd->effectivedate != '0000-00-00' || $icd->effectivedate != ''){
                        $icd_effectivedate = App\Http\Helpers\Helpers::timezone($icd->effectivedate, 'm/d/y');
                    }else{
                        $icd_effectivedate = 'Nil';
                    }
                    if(isset($icd->inactivedate) && $icd->inactivedate != '0000-00-00' || $icd->inactivedate != ''){
                        $icd_inactivedate = App\Http\Helpers\Helpers::timezone($icd->inactivedate, 'm/d/y');
                    }else{
                        $icd_inactivedate = 'Nil';
                    }
                ?>
                <tr>
                    <td class="text-left">{{ $icd->icd_code }}</td>
                    <td>{{ $icd->short_description }}</td>
                    <td>{{ $icd->sex }}</td>
                    <td>{{ $icd_effectivedate }}</td>
                    <td>{{ $icd_inactivedate }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="5">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>