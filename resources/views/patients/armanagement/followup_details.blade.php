<?php
	if(!function_exists('formatDateStr')) {
		function formatDateStr($matches){					  
		  return $matches[1].'/'.$matches[2].'/'.substr($matches[3],-2);
		}	
	}	
?>

@foreach(@$claim_detail_val_arr as $notes_detail) 
	@if($notes_detail->title == 'ArManagement' && ( $notes_detail->patient_notes_type != 'claim_denial_notes' && $notes_detail->patient_notes_type != 'alert_notes' && $notes_detail->patient_notes_type != 'patient_notes' && $notes_detail->patient_notes_type != 'claim_notes' && $notes_detail->patient_notes_type != 'payment_notes') )
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive margin-t-5 p-b-5" style="border-bottom: 1px solid #9fe5e1;">
			
			@if(@$notes_detail->patient_notes_type=='claim_denial_notes')
				<?php $denial_notes_arr = App\Models\Patients\Patient::getARDenialNotes(@$notes_detail->content); ?>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 p-l-0">
						<p class="no-bottom margin-t-5"><span class="med-green font600">Denial Date :</span> <span class="">&nbsp;{{App\Http\Helpers\Helpers::dateFormat(@$denial_notes_arr['denial_date'],'date')}}</span> </p>	
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 p-l-0">
						<p class="no-bottom margin-t-5"><span class="med-green font600">Billed To :</span> <span class="">&nbsp;{{ str_limit(@$denial_notes_arr['denial_insurance'], 15,'..')}}</span> </p>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 p-l-0">
						<p class="no-bottom margin-t-5"><span class="med-green font600">Check No :</span> <span class="">&nbsp;{{@$denial_notes_arr['check_no']}}</span> </p>
					</div>
					@if($denial_notes_arr['reference'] != '')
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
						<p class="no-bottom margin-t-5"><span class="med-green font600">Reference :</span> <span class="">&nbsp;{{@$denial_notes_arr['reference']}}</span> </p>
					</div>
					@endif
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
						@foreach($denial_notes_arr['denial_code_result'] as $denial_code_result_key=>$denial_code_result_val)                        
							<p class="margin-b-5">{{@$denial_code_result_val}}</p>
						@endforeach
					</div>
				</div>
			@else
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"> @if(date('Y-m-d') <= date('Y-m-d',strtotime($notes_detail->created_at)))	<i data-id="{{ $notes_detail->id }}" class="fa fa-copy js-convert-claim-notes" title="Convert to claim note"></i>	@endif		
					<?php 					
						//$replace = date("$2/$1/$3"); //date('m/d/y');     	//'<new date>		
						//$output =  preg_replace('@(\d{2})/(\d{2})/(\d{4})@', $replace, $notes_detail->content);       
						$output =  preg_replace_callback('@(\d{2})/(\d{2})/(\d{4})@', 'formatDateStr', $notes_detail->content);
						echo trim(stripslashes(html_entity_decode($output)));
						
					?>  
				</div>
			@endif
		</div>
	@endif
@endforeach