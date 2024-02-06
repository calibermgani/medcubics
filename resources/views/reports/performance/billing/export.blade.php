<?php
try{ ?>
<!DOCTYPE html>
<html lang="en">
    <head>
    	<title>Weekly Billing</title>
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
            .med-red {color: #ff0000 !important;}
            .font600{font-weight:600;}
            h3{font-size:20px; color: #00877f; margin-bottom: 10px;}
        </style>
    </head>
    <body>
        <?php
			$billing = @$result['billing'];
			$createdBy = @$result['createdBy'];
			$practice_id = @$result['practice_id'];
			@$heading_name = App\Models\Practice::getPracticeDetails();

			$practice_Arr = array_map('trim', explode(" ", $heading_name['practice_name']));
			if(COUNT($practice_Arr) > 2 ) {
				$practice_prefix = $practice_Arr[0][0].$practice_Arr[1][0].$practice_Arr[2][0];
			} elseif(COUNT($practice_Arr) > 1 ) {
				$practice_prefix = $practice_Arr[0][0].substr($practice_Arr[1],0,2);
			} elseif(COUNT($practice_Arr) == 1) {
				$practice_prefix = substr($practice_Arr[0],0,2);
			} else {
				$practice_prefix = 'RPG'; // Default
			}
			$practice_prefix = strtoupper($practice_prefix);
		?>
        <table>
            <tr>
                <td colspan="42" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="42" style="text-align:center;">Weekly Billing Report</td>
            </tr>
            <tr>
                <td colspan="42" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{ Auth::check() ? Auth::user()->name : "" }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="42" style="text-align:center;">
                    @if($result['header'] !='' && count((array)$result['header']) > 0)
                    <?php $i = 1; ?>
                    @foreach($result['header'] as $header_name => $header_val)
                    <span>
                        <?php $hn = $header_name; ?>
                        {{ @$header_name }}
                    </span> : {{str_replace('-','/', @$header_val)}}
                    @if($i < count((array)$result['header'])) | @endif
                    <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>

		<table class="popup-table-border  table-separate table m-b-m-1">
			<tr>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Client ID</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Unique ID</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim No</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Acc No</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Post Date</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Post Month</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Post Year</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">POS </th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Loc Name</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Actual Rendering Provider</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Responsibility</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Ins Category</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Svc Date From</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Proc Code</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Proc Desc</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Proc Category</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Modifier1</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Modifier2</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Dx10Code1</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Dx10Desc1</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Dx10Code2</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Dx10Desc2</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Tot Proc Amt</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Tot Proc Pmt</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Tot Proc Adj</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Tot Proc Refund</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Balance</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Latest Trans Date</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Latest Trans Month</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Latest Trans Year</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Cur Ins Name1</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Cur Ins Name2</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Cur Ins Name3</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Denial Code</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Denial Desc</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Remark Codes</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Last Sub Date</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">First Sub Date</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Claim Note</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Note Entered Date</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">RPG Facility Name</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Rendering NPI</th>
				<th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">Facility NPI</th>
			</tr>
		@if(!empty($billing))
			<?php
				$temp_icd = $tmp_ins = $pat_ins = [];
			?>
			@foreach($billing as $key=>$b)
			<?php
				$uniqid = '';
				$today_time = date("ymdHis");
				$uniqid   =	time().$b['rendering_provider_id'].$b['billing_provider_id'].$key
			?>
			<tr>
				<td>{{ $practice_prefix }}</td>
				<td style="text-align:left;">{{ $practice_prefix.$b['claim_id'].$b['cpt_id'] }}</td>
				<td style="text-align:left;">{{ ($b['claim_number']!='')?$b['claim_number']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['account_no']!='')?$b['account_no']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['posted_date']!='')?$b['posted_date']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['posted_month']!='')?$b['posted_month']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['posted_year']!='')?$b['posted_year']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['code']!='')?$b['code']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['facility_name']!="")?$b['facility_name']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['rendering_name']!="")?$b['rendering_name']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['insurance_name']!="")?$b['insurance_name']:"Self" }}</td>
				<td style="text-align:left;">{{ ($b['type_name']!="")?$b['type_name']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['dos_from']!="")?$b['dos_from']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['cpt_code']!="")?$b['cpt_code']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['long_description']!="")?$b['long_description']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['procedure_category']!="")?$b['procedure_category']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['modifier1']!="")?$b['modifier1']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['modifier2']!="")?$b['modifier2']:"-Nil-" }}</td>
				<?php
				/*
				$cpt_icd = $map_key = [];
				?>
				@if($b['icd_codes']!='')
					<?php
						$i=0;
						$cpt_icd = explode(',', $b['icd_codes']);
						$map_key = explode(',', $b['cpt_icd_map_key']);
					?>
				@endif
				@if(!empty($cpt_icd))
					@foreach($cpt_icd as $key=>$map)
						<?php
							$icdDet = [];
							$tVal = @$cpt_icd[@$map_key[$key]-1];
							if($tVal != '') {
								if(isset($temp_icd[$tVal])) {
									$icdDet = $temp_icd[$tVal];
								} else {
									$icdDet = $temp_icd[$tVal] = App\Models\Icd::getIcdCodeAndDesc($tVal);
								}
							}
						?>
						<td style="text-align:left;">{{ @$icdDet['icd_code'] }}</td>
						<td style="text-align:left;">{{ @$icdDet['short_description'] }}</td>
						<?php
							if($i==1){
								break;
							}
							$i++;
						?>
						@if(count((array)$cpt_icd) == 1)
						<td>-Nil-</td>
						<td>-Nil-</td>
						@endif
					@endforeach
				@else
				<td>-Nil-</td>
				<td>-Nil-</td>
				<td>-Nil-</td>
				<td>-Nil-</td>
				@endif
				*/ ?>
				<td>@if(isset($b['icdDet'][0]['icd_code']) && $b['icdDet'][0]['icd_code'] != "") {{$b['icdDet'][0]['icd_code']}} @else -Nil- @endif</td>
				<td>@if(isset($b['icdDet'][0]['short_description']) && $b['icdDet'][0]['short_description'] != "")  {{$b['icdDet'][0]['short_description']}} @else -Nil- @endif</td>
				<td>@if(isset($b['icdDet'][1]['icd_code']) && $b['icdDet'][1]['icd_code'] != "") {{$b['icdDet'][1]['icd_code']}} @else -Nil- @endif</td>
				<td>@if(isset($b['icdDet'][1]['short_description']) && $b['icdDet'][1]['short_description'] != "")  {{$b['icdDet'][1]['short_description']}} @else -Nil- @endif</td>
				<td data-format="#,##0.00">{{ $b['charge'] }}</td>
				<td style="@if($b['tot_amt']<0) color:#ff0000; @endif" data-format="#,##0.00">{{ ($b['tot_amt']!="")?$b['tot_amt']:0 }}</td>
				<td style="@if($b['tot_adj']<0) color:#ff0000; @endif" data-format="#,##0.00">{{ ($b['tot_adj']!='')?$b['tot_adj']:0 }}</td>
				<td style="@if( (@$b['tot_refund'])<0) color:#ff0000; @endif" data-format="#,##0.00">{{ ($b['tot_refund']!='')?$b['tot_refund']:0 }}</td>
				<td style="@if($b['tot_ar']<0) color:#ff0000; @endif" data-format="#,##0.00">{{ $b['tot_ar'] }}</td>
				<td style="text-align:left;">{{ ($b['transanction_date'])?$b['transanction_date']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['transanction_month'])?$b['transanction_month']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['transanction_year'])?$b['transanction_year']:'-Nil-' }}</td>
				<?php
					$i=0;
					$insurances = isset($b['pat_insurances']) ? $b['pat_insurances'] : [];
				?>
				@while($i < 3)
					@if(isset($insurances[$i]))
						<td style="text-align:left;">{{@$insurances[$i]->short_name}}</td>
					@else
						<td>-Nil-</td>
					@endif
					<?php $i++;?>
				@endwhile

				<td style="text-align:left;">{{ ($b['denial_code']!="")? $b['denial_code']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['denials']!="")?$b['denials']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['remarks']!="")?$b['remarks']:"-Nil-" }}</td>
				<td style="text-align:left;">{{ ($b['last_submitted_date']!='' && $b['last_submitted_date']!='00/00/0000')?$b['last_submitted_date']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['first_submitted_date']!='' && $b['first_submitted_date']!='00/00/0000')?$b['first_submitted_date']:'-Nil-' }}</td>
				<td style="text-align:left;">{!! ($b['content']!='') ? $b['content'] :'-Nil-' !!}</td>
				<td style="text-align:left;">{{ ($b['note_date']!='' && $b['content']!='')?$b['note_date']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['facility_name']!='')?$b['facility_name']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['npi']!='')?$b['npi']:'-Nil-' }}</td>
				<td style="text-align:left;">{{ ($b['facility_npi']!='')?$b['facility_npi']:'-Nil-' }}</td>
			</tr>
			@endforeach
		</table>
		@endif
		<table>
			<tr>
		        <td colspan="42">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
			</tr>
		</table>
    </body>
</html>
<?php
}catch(Exception $e){
    \Log::info("Exception Msg".$e->getMessage());
	\Log::info("Error Details. ");\Log::info($e);
}
?>