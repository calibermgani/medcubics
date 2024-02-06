<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-radius-4 tabs-border">
	@foreach($problemlist as $problemlist)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding border-bottom-dotted">	
			<p class="no-bottom"><span class="med-green">{{ App\Http\Helpers\Helpers::shortname(@$problemlist->created_by->id) }}</span>  <span class="pull-right med-orange">{{ App\Http\Helpers\Helpers::timezone(@$problemlist->created_at,'m/d/y H:i:s')}}</span></p>
			<p class="no-bottom"><span class="med-gray-dark">{{@$problemlist->description}}</span></p>
			<p class="no-bottom">Assigned To : <span class="med-green font600">{{ App\Http\Helpers\Helpers::shortname($problemlist->assign_user_id) }}</span></p>
			<p class="no-bottom">Followup date : <span class="med-orange font600">{{date("m/d/y", strtotime($problemlist->fllowup_date))}}</span></p>
			<p>  Status : <span class="{{@$problemlist->status}}">{{@$problemlist->status}}</span> | Priority : <span class="{{@$problemlist->priority}}" data-toggle="tooltip" data-original-title="{{@$problemlist->priority}}">
			@if($problemlist->priority == 'High')
				<i class="fa fa-arrow-up" aria-hidden="true"></i>
			@elseif($problemlist->priority == 'Low')
				<i class="fa fa-arrow-down" aria-hidden="true"></i>
			@else
				<i class="fa fa-arrows-h" aria-hidden="true"></i>
			@endif</span></p>							
		</div>
	@endforeach 
</div>