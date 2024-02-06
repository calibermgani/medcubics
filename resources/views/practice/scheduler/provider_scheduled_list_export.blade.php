<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Provider Scheduled List</title>
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
            $provider = $result['provider'];
            $providerschedulers = $result['providerschedulers'];
            $practice_details = App\Models\Practice::getPracticeDetails();
            $heading_name = $practice_details['practice_name'];
        ?>
        <table>
            <tr>                   
                <td colspan="6" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name}}</td>                    
            </tr>
            <tr>
                <td colspan="6" style="text-align:center;">Provider Scheduled List</td>
            </tr>
            <tr>
                <td valign="middle" colspan="6" style="text-align:center;"><span>User :</span><span>{{ Auth::user()->name }}</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Facility</th>                                
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Schedule Type</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Start Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">End Date</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">No of Occurrence</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;width:15px;">Repeat Every</th>
                </tr>
            </thead>    
            <tbody>
                <?php $provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode'); ?>
                @if(count($providerschedulers) > 0)
                @foreach($providerschedulers as $provider_scheduler)
                <?php $provider_scheduler_facility_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider_scheduler->facility_id,'encode'); ?>
                <tr>
                    <td colspan="6" style=""><span class="font600">{{$provider_scheduler->facility->facility_name}}</span></td>
                </tr>
                <?php
                    $allproviderschedulers = App\Models\ProviderScheduler::getAllProviderSchedulerByFacilityId($provider_scheduler_facility_id, $provider_id);
                ?>
                @foreach($allproviderschedulers as $all_provider_scheduler)
                <tr>
                    <td></td>
                    <td><span>{{$all_provider_scheduler->schedule_type}}</span></td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat($all_provider_scheduler->start_date,'date') }}</td>
                    <td>@if($all_provider_scheduler->end_date_option != 'never'){{ App\Http\Helpers\Helpers::dateFormat($all_provider_scheduler->end_date,'date') }}@else Never @endif</td> 
                    <td class="text-left" style='text-align: left'>@if($all_provider_scheduler->end_date_option == 'after'){{$all_provider_scheduler->no_of_occurrence}}@else -- @endif</td>
                    <td>@if($all_provider_scheduler->repeat_every > 1){{$all_provider_scheduler->repeat_every}} @endif 
                        @if($all_provider_scheduler->schedule_type == 'Daily')Days 
                        @elseif($all_provider_scheduler->schedule_type == 'Weekly')Weeks 
                        @elseif($all_provider_scheduler->schedule_type == 'Monthly')Months 
                        @endif
                    </td>
                </tr>
                @endforeach
                @endforeach
                @endif
            </tbody>
        </table>
        <table>
            <tr>
                <td colspan="6">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
            </tr>
        </table>
    </body>
</html>