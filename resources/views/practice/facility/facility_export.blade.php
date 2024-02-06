<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Facility</title>
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
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="8" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="8" style="text-align:center;">Facility List</td>
            </tr>
            <tr>
                <td valign="center" colspan="8" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->short_name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Short Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>               
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Specialty</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">POS</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">City</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">State</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Phone</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Status</th>
                </tr>
            </thead>    
            <tbody>
                @foreach($facilitymodule as $facility)
		<tr>
                    <td>{{ $facility->short_name }}</td>
                    <td>{{ @$facility->facility_name }}</td>
                    <td>@if($facility->speciality_details){{ $facility->speciality_details->speciality }}@endif</td>
                    <td style="text-align: left;">@if($facility->pos_details){{ $facility->pos_details->code }}@endif</td>
                    <td>@if($facility->facility_address){{ $facility->facility_address->city }}@endif</td>
                    <td>@if($facility->facility_address){{ $facility->facility_address->state }}@endif</td>
                    <td>@if($facility->facility_address){{ $facility->facility_address->phone }}@endif</td>
                    <td>{{ $facility->status }}</td>
		</tr>
		@endforeach
            </tbody>
        </table>
        <div colspan="8"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>