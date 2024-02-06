<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Providers_Scheduler</title>
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
            $providers_scheduler = $result['providers_scheduler'];
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="5" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="5" style="text-align:center;">Providers Scheduler</td>
            </tr>
            <tr>
                <td valign="middle" colspan="5" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Short Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Provider</th>                        
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">NPI</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Scheduled</th>
                </tr>
            </thead>    
            <tbody>
                @if(count($providers_scheduler) > 0)
                @foreach($providers_scheduler as $provider)
                <?php 
                    $provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode');
                    $provider_types = App\Models\Provider_type::get_provider_types_name($provider->provider_types_id);
                    $scheduled_count = App\Models\ProviderScheduler::getScheduledCountByProviderId($provider_id,'Provider');
                    $scheduled = (isset($scheduled_count)&&($scheduled_count>0)) ? 'Yes' : 'No';
                ?>
                <tr>
                    <td>{{ @$provider->short_name }}</td>
                    <td>{{ $provider->provider_name.' '.@$provider->degrees->degree_name }}</td>
                    <td>{{ $provider_types }}</td>							
                    <td>{{ $provider->npi }}</td>
                    <td>{{ $scheduled }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="5">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>