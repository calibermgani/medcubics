@extends('practice/practice/practicelist_default')
@section('practice')

<!--1st Data-->
<div class="table-responsive practicelist">
    <table id="example2" class="table table-bordered font13" style="width: 100%;" >
        <colgroup>
            <col span="1" style="background-color:#fefffc !important;">
            <col span="4" style="background-color:#fefbfa !important">
            <col span="2" style="background-color:#fefdfc !important">
            <col span="2" style="background-color:#fcfdfe !important">
        </colgroup>
		<thead>    
		<tr style="cursor:default !important;">
			<th colspan="1" rowspan="2" style="background: #D6F5F0; text-align: center; color: #007F78 !important; font-weight: bold"><p>Practice</p></th>
			<th colspan="4" style="background: #FBB9C0; text-align: center; color:#222 !important; font-weight: bold">To Date</th>
			<th colspan="2" style="background: #FDE47D; text-align: center; color:#222 !important; font-weight: bold">Month</th>
			<th colspan="2" style="background: #CFD4F9; text-align: center; color:#222 !important; font-weight: bold">Year</th>
		</tr>
        <tr style="cursor:default !important;">
            <th style="background: #FDDADE; color:#cb4b5a !important; font-weight: 600;">Unbilled ($)</th>
            <th style="background: #FDDADE; color:#cb4b5a !important; font-weight: 600;">Rejections ($)</th>            
            <th style="background: #FDDADE; color:#cb4b5a !important; font-weight: 600;">Workbench</th>
            <th style="background: #FDDADE; color:#cb4b5a !important; font-weight: 600;">Outstanding AR ($)</th>
            <th style="background: #FCF2CB; color:#a98c1d !important; font-weight: 600;">Charges ($)</th>
            <th style="background: #FCF2CB; color:#a98c1d !important; font-weight: 600;">Collections ($)</th>
            <th style="background: #E4E7FD; color:#535fb9 !important; font-weight: 600;">Charges ($)</th>
            <th style="background: #E4E7FD; color:#535fb9 !important; font-weight: 600;">Collections ($)</th>
        </tr>                       
        </thead>

        <tbody>
		<?php $admin_practice_id = Auth::user()->admin_practice_id;  ?>
			@foreach($db_details as $db_id => $db_details)
			<?php 
				$practice_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($db_id,'encode'); 
				$month_charges = ltrim($db_details["month_charges"], '$');
				$month_collection = ltrim($db_details["month_collection"], '$');
				$year_charges = ltrim($db_details["year_charges"], '$');
				$year_collection = ltrim($db_details["year_collection"], '$');
				$year_outstanding = ltrim($db_details["year_outstanding"], '$');
			?>
            <tr style="cursor:default">                     
                <td><a href="{{ url('admin/customer/setpractice/'.$practice_id) }}">{{ $db_details["name"] }}</a></td>
                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($db_details["unbilled"]) !!}</td>
                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($db_details["rejection"]) !!}</td>
                <td class="text-left">{!! $db_details["problem_list"] !!}</td>
                <td class="text-right">{!! $year_outstanding !!}</td>
                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($month_charges) !!}</td>
                <td class="text-right">{!! $month_collection !!}</td>
                <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat($year_charges) !!}</td>
                <td class="text-right">{!! $year_collection !!}</td>
            </tr>
            @endforeach 
        </tbody>
    </table>
</div>
@stop