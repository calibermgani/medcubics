<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Employer</title>
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
        <?php $heading_name = App\Models\Practice::getPracticeName($practice_id); ?>
        <table>
            <tr>                   
                <td colspan="6" style="text-align:center;"><h3><center>{{$heading_name}}</center></h3></td>                    
            </tr>
            <tr style="height:20px !important;vertical-align: middle !important; display: table-cell !important;">
                <td colspan="6" style="text-align:center;">Employer Summary</td>
            </tr>
            <tr style="color:#000 !important;height:30px !important;">
                <td valign="center" colspan="6" style="text-align:center;vertical-align: baseline !important;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ date("m/d/y") }}</span></td>
            </tr>
        </table>
        <table>
            <thead style="border:none !important;font-size:10px;border-bottom: 5px solid #000 !important;">
                <tr>
                    <th style="width:10px;font-size: 10px !important;text-align:center;">Employer Name</th>
                    <th style="text-align:center;">Address Line 1</th>  		
                    <th style="text-align:center;">Address Line 2</th>  		
                    <th style="text-align:center;">City</th>  		
                    <th style="text-align:center;">ST</th>  		
                    <th style="text-align:center;">Zip Code</th>
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
        <div colspan="6"><p>Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</p></div>
    </body>
</html>