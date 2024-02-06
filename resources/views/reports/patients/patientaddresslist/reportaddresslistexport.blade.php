<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Address Listing</title>
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
        @$patient_address_list_filter = $result['patient_address_list_filter'];
        @$start_date = $result['start_date'];
        @$end_date = $result['end_date'];
        @$createdBy = $result['createdBy'];
        @$practice_id = $result['practice_id'];
        @$search_by = $result['search_by'];
        $heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>
                <td colspan="12" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">Address Listing</td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;"><span>User :</span><span>@if(Auth::check() && isset(Auth::user()->name)) {{ Auth::user()->name }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="12" style="text-align:center;">
                    <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span>{!! $key !!} :  </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">First Name</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">MI</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Gender</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">DOB</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">SSN</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Address 1</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Address 2</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">City</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">ST</th>
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Zip Code</th>
                </tr>
            </thead>
            <tbody>
                @if(count($patient_address_list_filter) > 0)
                <?php
                    @$total_adj = 0;
                    @$patient_total = 0;
                    @$insurance_total = 0;
                ?>
                @foreach($patient_address_list_filter as $list)
                <tr>
                    <td>{!!  @$list->last_name !!}</td>
                    <td>{!! @$list->first_name !!}</td>
                    <td>{!! @$list->middle_name !!}</td>
                    <td>{!! @$list->gender !!}</td>
                    <td>{{ App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') }}</td>
                    <td class="text-left" style="text-align:left;">@if($list->ssn != ''){!! @$list->ssn !!} @else -Nil- @endif</td>
                    <td>{!! @$list->account_no !!}</td>
                    <td>@if($list->address1 != '') {!! @$list->address1 !!} @else -Nil- @endif</td>
                    <td>@if($list->address2 != ''){!! @$list->address2 !!} @else -Nil- @endif</td>
                    <td>@if($list->city != '') {!! @$list->city !!} @else -Nil- @endif</td>
                    <td>@if($list->state != ''){!! @$list->state !!} @else -Nil- @endif</td>
                    <td class="text-left" style="text-align:left;">@if($list->zip5 != ''){!! @$list->zip5 !!} @if(@$list->zip4){!! -@$list->zip4 !!} @endif @else -Nil- @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <td colspan="12">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>