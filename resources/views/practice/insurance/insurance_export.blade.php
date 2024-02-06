<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Insurance</title>
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
            @$insurances = $result['insurances']; 
            @$practice_details = App\Models\Practice::getPracticeDetails();
            @$heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="6" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">Insurance List</td>
            </tr>
            <tr>
                <td valign="center" colspan="6" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Short Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Insurance Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Address</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Phone</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Payer ID</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($insurances as $insurance)
                <?php 
                    $insurance_address = $insurance->address_1.', '.$insurance->city.', '.$insurance->state.', '.$insurance->zipcode5.'- '.$insurance->zipcode4;
                    $insurance_type = (isset($insurance->insurancetype->type_name)) ? $insurance->insurancetype->type_name : '';
                ?>
                <tr>
                    <td>{{ $insurance->short_name }}</td>
                    <td>{{ $insurance->insurance_name }}</td>
                    <td>{{ $insurance_type }}</td>
                    <td>{{ $insurance_address }}</td>
                    <td>{{ $insurance->phone1 }}</td>
                    <td class="text-left" style='text-align: left;'>{{ $insurance->payerid }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="6">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>