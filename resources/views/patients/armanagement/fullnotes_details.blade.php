<style>
    .Table
    {
        display: table;
        width: 100%;
    }
    .Title
    {
        display: table-caption;
        text-align: center;
        font-weight: bold;
        font-size: larger;
    }
    .Heading
    {
        display: table-row;
        font-weight: bold;
        text-align: center;
    }
    .Row
    {
        display: table-row;
        width: 100%;
    }
    .Cell
    {
        display: table-cell;
        padding-left: 5px;
        padding-right: 5px;
    }
</style>	
<?php
	if(!function_exists('formatDateStr')) {
		function formatDateStr($matches){					  
		  return $matches[1].'/'.$matches[2].'/'.substr($matches[3],-2);
		}	
	}	
?>
@foreach(@$claim_detail_val_arr as $notes_detail)
@if($notes_detail->patient_notes_type == 'claim_notes' || $notes_detail->patient_notes_type == 'claim_denial_notes')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-bottom-dotted margin-t-5 p-b-5 p-l-0">
    <p class="no-bottom font600"><span class="med-green">CN</span> - <span class="med-orange"> {{App\Http\Helpers\Helpers::dateFormat(@$notes_detail->created_at,'datetime')}}</span>
        <span class="pull-right font12"><span class=" med-green"> {{	@$notes_detail->user->short_name }} 
            </span></span></p>
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
            <p class="no-bottom margin-t-5" style="text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"><span class="med-green font600">Check No :</span> <span class="">&nbsp;{{@$denial_notes_arr['check_no']}}</span> </p>
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
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">	
        @if(!empty($claim_detail_val->date_of_service))
        DOS - {{ App\Http\Helpers\Helpers::dateFormat(@$claim_detail_val->date_of_service) }}
        @endif			
        <?php
        //$replace = date("$2/$1/$3"); //date('m/d/y');     	//'<new date>		
        //$output =  preg_replace('@(\d{2})/(\d{2})/(\d{4})@', $replace, $notes_detail->content);       
        $output = preg_replace_callback('@(\d{2})/(\d{2})/(\d{4})@', 'formatDateStr', $notes_detail->content);
        echo trim(stripslashes(html_entity_decode($output)));
        ?>  
    </div>
    @endif
</div>
@endif
@endforeach
@foreach(@$patient_notes as $patient_note)

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive border-bottom-dotted margin-t-5 p-b-5 p-l-0">
	<p class="no-bottom font600">
		<span class="med-green font600"> PN -</span><span class="med-orange">  {{App\Http\Helpers\Helpers::dateFormat(@$patient_note->created_at,'datetime')}}</span>
		<span class="pull-right font12">
			<span class=" med-green"> {{@$patient_note->user->short_name }}</span>
		</span>
	</p>

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
		<p class="margin-b-5"><?php
		//$output =  preg_replace('@(\d{2})/(\d{2})/(\d{4})@', $replace, $notes_detail->content);						
		$output = preg_replace_callback('@(\d{2})/(\d{2})/(\d{4})@', 'formatDateStr', $patient_note->content);
		echo trim(stripslashes(html_entity_decode($output)));
		?> 
		</p>
	</div> 	
</div>
@endforeach