<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Reports - Aging</title>
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
		@$headers = $result['search_by'];
		@$header = $result['header'];
		@$aging_report_list = $result['aging_report_list'];
		@$title = $result['title'];
		@$createdBy = $result['createdBy'];
		@$practice_id = $result['practice_id'];
		$heading_name = App\Models\Practice::getPracticeDetails(); ?>
        <table>
            <tr>                   
                <td colspan="17" style="text-align:center;color: #00877f;font-weight:600;">{{$heading_name['practice_name']}}</td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;">{{ @$title }}</td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;"><span>User :</span><span>@if(isset($createdBy)) {{  $createdBy }} @endif</span> | <span>Created :</span><span>{{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</span></td>
            </tr>
            <tr>
                <td colspan="17" style="text-align:center;">
                    @if($headers !='' && count((array)$headers)>0)
                    <?php $i = 1; ?>
                    @foreach($headers as $header_name => $header_val)
                    <span>
                        <?php $hn = $header_name; ?>
                        {{ @$header_name }}</span> : {{ @$header_val}}@if($i < count((array)$headers)) | @endif <?php $i++; ?>
                    @endforeach
                    @endif
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <?php $count_r = 1; ?> 
                    @foreach($header as $header_name => $header_val)
                    @if($count_r ==1)
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;">{{ @$header_val }}</th>
                    @elseif($count_r % 2 == 0)
                    <th valign="center" style="text-align:center;border-bottom: 2px solid  #000!important;font-weight: 800;font-size:12px;" colspan="2">{{ @$header_val }}</th>
                    @endif
                    <?php $count_r++; ?>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if(isset($aging_report_list) && !empty($aging_report_list))
					<tr>                     
						 @foreach($aging_report_list->name as $key => $name)    
								<!-- This condition added for displaying price format-->
							@if(@$name=='Claims')
								<td class="font600 text-left" style="font-weight:600;text-align:left;border-top: 2px solid  #000!important;">{!! @$name !!}</td>
							@else
							@if($key > 0)
								<td class="font600 text-right" style="font-weight:600;text-align:right;border-top: 2px solid  #000!important;">{!! @$name !!}($)</td>
							@else
								<td class="font600 text-right" style="font-weight:600;text-align:right;border-top: 2px solid  #000!important;">{!! @$name !!}</td>
							@endif
							@endif                                  
						@endforeach
					</tr>
					<tr> 
					@if(isset($aging_report_list->patient))                     
						@foreach($aging_report_list->patient as $key => $val)    
								<!-- This condition added for displaying price format-->
							@if(@$key==0)
								<td class="text-left <?php echo($val)<0?'med-red':'' ?>" style="text-align:left;<?php echo($val)<0?'color:#ff0000;':'' ?>" data-format='#,##0.00'>{!! @$val !!}</td>
							@elseif(@$key%2==0)
								<td class="text-right <?php echo($val)<0?'med-red':'' ?>" style="text-align:right;<?php echo($val)<0?'color:#ff0000;':'' ?>" data-format='#,##0.00'>{!! @$val !!}</td>
							@else
								<td class="text-left" style="text-align:left;">{!! @$val !!}</td>
							@endif                                  
						@endforeach
					@endif
					</tr>
					 <?php 
					$insurance_provider = array_except((array)$aging_report_list,['name','patient','total','total_percentage']);
					?>
					@if(isset($insurance_provider) && !empty($insurance_provider))             
						@foreach($insurance_provider as $list)  
						<tr>        
							@foreach($list as $key => $val)  
								<!-- This condition added for displaying price format-->
							@if(@$key==0)
								<td class="text-left <?php echo($val)<0?'med-red':'' ?>" style="text-align:left;<?php echo($val)<0?'color:#ff0000;':'' ?>" data-format='#,##0.00'>{!! @$val !!}</td>
							@elseif(@$key%2==0)
								<td class="text-right <?php echo($val)<0?'med-red':'' ?>"  style="text-align:right;<?php echo($val)<0?'color:#ff0000;':'' ?>" data-format='#,##0.00'>{!! @$val !!}</td>
							@else
								<td class="text-left" style="text-align:left;">{!! @$val !!}</td>
							@endif    
							@endforeach                              
						</tr>
						@endforeach
					@endif
					<tr>                     
						@foreach($aging_report_list->total as $key => $val)    
								<!-- This condition added for displaying price format-->
							@if(@$key==0)
								<td class="font600 text-left <?php echo($val)<0?'med-red':'' ?>" style="font-weight:600;text-align:left;<?php echo($val)<0?'color:#ff0000;':'' ?>" data-format='#,##0.00'>{!! @$val !!}</td>
							@elseif(@$key%2==0)
								<td class="font600 text-right <?php echo($val)<0?'med-red':'' ?>" style="font-weight:600;text-align:right;<?php echo($val)<0?'color:#ff0000;':'' ?>" data-format='#,##0.00'>{!! @$val !!}</td>
							@else
								<td class="font600 text-left" style="font-weight:600;text-align:left;">{!! @$val !!}</td>
							@endif                                  
						@endforeach
					</tr>
					<tr>                     
						@foreach($aging_report_list->total_percentage as $key => $val)    
								<!-- This condition added for displaying price format-->
							@if(@$key==0)
								<td class="font600 text-left <?php echo($val)<0?'med-red':'' ?>" style="font-weight:600;text-align:left;<?php echo($val)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$val !!}</td>
							@elseif(@$key%2==0)
								<td class="font600 text-right <?php echo($val)<0?'med-red':'' ?>" style="font-weight:600;text-align:right;<?php echo($val)<0?'color:#ff0000;':'' ?>"  data-format='#,##0.00'>{!! @$val !!}</td>
							@else
								<td class="font600 text-left" style="font-weight:600;text-align:left;">{!! @$val !!}</td>
							@endif                                  
						@endforeach
					</tr>
				@endif                 
            </tbody>
        </table>
        <td colspan="17">Copyright &copy; {{date('Y')}} Medcubics. All rights reserved.</td>
    </body>
</html>