<?php
    if(Cache::has('default_view'))
        $default_view = Cache::get('default_view');
    else
        $default_view = Config::get('siteconfigs.scheduler.default_view_facility');
?>
<div class="col-md-4 no-padding">
    <div class="box no-shadow no-border margin-b-10" >
        <div class="box-header transparent">
            <i class="fa @if($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) {{Config::get('cssconfigs.Practicesmaster.user')}} @else {{Config::get('cssconfigs.Practicesmaster.facility')}} @endif"></i>
            <h3 class="box-title">               
                {{@$default_view}}
            </h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool default-cursor"><i class="fa fa-minus"></i></button>
            </div>
        </div>

        <div class="box-body chat chat-scheduler" id="js-resource-listing1">
            <ul class="list-group list-group-unbordered no-bottom">
                <?php $i = 1; ?>
                @foreach($default_view_list_arr as $keys=>$default_view_list)
                    <?php
						$default_view_list_selection = null;
						if ($default_view_list->id == $default_view_list_id) {
							$default_view_list_selection = true;
						}					
                    ?>
                    <li class="list-group-item m-b-m-15 margin-t-m-8">
                        {!! Form::radio('default_view_list', $default_view_list->id,$default_view_list_selection,['class'=>' js-scheduler_calendar js-sch_cal_default_view_list','id'=>'s'.$keys]) !!} 
                        @if($default_view == Config::get('siteconfigs.scheduler.default_view_provider')) 
                            <label class="form-cursor" for="s{{$keys}}"><span class="med-orange">{{$default_view_list->short_name}}</span> - {{str_limit(@$default_view_list->provider_name,25,'..').' '.str_limit(@$default_view_list->degrees->degree_name,5,'..')}} </label> 
                        @else
                            <label class="form-cursor" for="s{{$keys}}"><span class="med-orange"> {{$default_view_list->short_name}}</span> - {{str_limit(@$default_view_list->facility_name,25,'..')}} </label> 
                        @endif
                    </li>
                @endforeach    
			
            </ul>
        </div><!-- /.chat -->
    </div><!-- /.box (chat box) -->
</div>

<div id="js-scheduler_resource_stats">
    @include('scheduler/scheduler/scheduler_resource_stats')
</div>