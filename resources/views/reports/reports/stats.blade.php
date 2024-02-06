<div class="box no-border no-shadow no-background no-bottom margin-t-m-20 js_update_stats">
    <div class="box-header no-border no-background">
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="col-lg-12" style="text-align: center;">
        @php  
			$statslist = App\Http\Controllers\Api\StatsApiController::getStatsDetail(@$module); 
			$j =0;  
		@endphp
        <input type="hidden" id="js_page_name" value="{{ @$module }}" >
		<input type="hidden" id="js_message" value="{{ @$message }}" >
		<input type="hidden" name="_token" id="csrf_token" value="{{ Session::token() }}" />
        @for($i=0; $i<6; $i++) 
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4 practice-statis js_position_count" data-index='{{$i}}' > 
                <div class="practice-icons">
					@if(@$statslist['statsdetail_obj'][$i]['statslist']['name']=='') 
						{!! HTML::image('img/no-image.png') !!} 
					@else
						{!! HTML::image('img/'.@$statslist['statsdetail_obj'][$i]['statslist']['image_name'].'.png',null, array()) !!} 		
					@endif
                </div>
				
                <h4 style="color:#F07D08;">{!! @$statslist['collect_count'][$statslist['statsdetail_obj'][$i]['statslist']['id']] !!}</h4>
                <div class="btn-group">
                    @if($j < 1)
						<a class="" href="" data-toggle="dropdown" data-hover="dropdown">
						<h4 style="margin-top: -5px;">{{ (@$statslist['statsdetail_obj'][$i]['statslist']['name']=='')? 'Add' : @$statslist['statsdetail_obj'][$i]['statslist']['name'] }}</h4></a>
					@else
						<h4 style="margin-top: -5px;">{{ (@$statslist['statsdetail_obj'][$i]['statslist']['name']=='')? 'Add' : @$statslist['statsdetail_obj'][$i]['statslist']['name'] }}</h4>
					@endif
					
					@if($j < 1)
					<ul class="dropdown-menu-stats" >
						<li class="js_stats_select_option"><a tabindex="-1" href="#"><i class="fa"></i>(None)</a></li>
						@foreach($statslist['stats_list'] as $stats_list_val)
							<li class="js_stats_select_option"><a tabindex="-1" href="#">
							{!! HTML::image('img/'.$stats_list_val->image_name.'.png',null, array('class' => $stats_list_val->image_name)) !!}{{ @$stats_list_val->name}}</a></li>
						@endforeach                                                
					</ul>
					@endif
					@php  (@$statslist['statsdetail_obj'][$i]['statslist']) ? $j : $j++ @endphp
                </div>                
            </div>
        @endfor
        </div>
    </div>
</div>