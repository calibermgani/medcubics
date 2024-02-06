<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 border-radius-4 tabs-border">
	@foreach($assigned_document as $list)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding border-bottom-dotted">	
			<p class="no-bottom"><span class="med-green">{{ App\Http\Helpers\Helpers::shortname(@$list->created_by) }}</span>  <span class="pull-right med-orange">{{ App\Http\Helpers\Helpers::dateFormat(@$list->created_at,'datetime')}}</span></p>
			<p class="no-bottom"><span class="med-gray-dark">{{@$list->notes}}</span></p>
			<p class="no-bottom">Assigned To : <span class="med-green font600">{{ App\Http\Helpers\Helpers::shortname($list->assigned_user_id) }}</span></p>
			<p class="no-bottom">Followup date : <span class="med-orange font600">{{date("m/d/y", strtotime($list->followup_date))}}</span></p>
			<p>  Status : <span class="{{@$list->status}}">{{@$list->status}}</span> | Priority : <span class="{{@$list->priority}}" data-toggle="tooltip" data-original-title="{{@$list->priority}}">
			@if($list->priority == 'High')
				<i class="fa fa-arrow-up" aria-hidden="true"></i>
			@elseif($list->priority == 'Low')
				<i class="fa fa-arrow-down" aria-hidden="true"></i>
			@else
				<i class="fa fa-arrows-h" aria-hidden="true"></i>
			@endif</span></p>							
		</div>
	@endforeach 
</div>