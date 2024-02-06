<div class="box no-border no-shadow no-background no-bottom js_update_stats_search">
    <div class="">
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body p-t-0 p-l-0 p-r-0">

        <?php
        $fields = json_decode($search_fields->search_fields);
        $moreArr = [];

        try {
            ?>
             <div class="search_fields_container col-lg-9 col-md-9 col-sm-9 col-xs-12">
             @foreach($fields as $list)
              @if($list->show_type == 'show') 
                @if($list->type == 'select')
					<?php
						$data = [];
						if($list->name == 'Gender') {							
							$data['Male'] = 'Male';
							$data['Female'] = 'Female';
							$data['Others'] = 'Others';
						}elseif($list->name == 'Patients Type') {
							$data['New'] = 'New Patients';    
							$data['All'] = 'All Patients';
							$data['App'] = 'App Patients';											 
						}
					?>
					@if($list->name == 'Gender')
					<div id="{{$list->label_name}}" class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 250px;">
						{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
						{!! Form::select($list->label_name, []+(array)$data,$list->value,['class'=> $list->class_name. $list->type.' auto-generate select2 form-control form-select','data-field'=>$list->label_name,'multiple'=>'multiple','placeholder'=>'-- Select --','id'=>$list->label_name]) !!}
					</div>
					@elseif($list->name == 'Patients Type')
						<div id="{{$list->label_name}}" class="margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 250px;">
						{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
						{!! Form::select($list->label_name, []+(array)$data,$list->value,['class'=> $list->class_name. $list->type.' auto-generate select2 form-control form-select','data-field'=>$list->label_name,'placeholder'=>'-- Select --','id'=>$list->label_name]) !!}
					</div>                
					@endif
                @endif
                @if($list->type == 'text')
                <div id="{{$list->label_name}}" class="margin-b-4 margin-t-10 margin-r-5" style="float:left;">
                    {!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
                    {!! Form::text($list->label_name, null ,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate form-control form-select','id'=>$list->label_name]) !!}
                </div>          
                @endif
                @if($list->type == 'date')  
                <div id="{{$list->label_name}}" class="margin-b-4 margin-t-10 margin-r-5" style="float:left;">
                    {!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!}
                    {!! Form::text($list->label_name, null ,['class'=> $list->class_name. $list->type.' auto-generate js-date-range form-control form-select','id'=>$list->label_name]) !!}
                </div>
                @endif           
				
                @elseif($list->show_type == 'more')
                 <?php  $moreArr[$list->label_name] = $list->name; ?>            
					@if($list->type == 'select')
						<?php
							$data = [];
							if($list->name == 'Gender'){								
								$data['Male'] = 'Male';
								$data['Female'] = 'Female';
								$data['Others'] = 'Others';
							} elseif($list->name == 'Patients Type') {
								$data['All'] = 'All';
								$data['App'] = 'App';                 
							}
						?>
						@if($list->name == 'Gender')
						 <div class="{{$list->label_name}}_more hide">
							<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 250px;" id="{{$list->label_name}}">
								{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!}
								{!! Form::select($list->label_name, []+(array)$data,$list->value,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate select2 form-control form-select','multiple'=>'multiple','placeholder'=>'-- Select --','id'=>$list->label_name]) !!}
							</div>
						</div>
						@elseif($list->name == 'Patients Type')
						  <div class="{{$list->label_name}}_more hide">
							<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left; width: 250px;" id="{{$list->label_name}}">
								{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!}
								{!! Form::select($list->label_name, []+(array)$data,$list->value,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate select2 form-control form-select','placeholder'=>'-- Select --','id'=>$list->label_name]) !!}
							</div>
						</div>
						@endif
					@endif
					@if($list->type == 'text')
					<div class="{{$list->label_name}}_more text-suggest hide">
						<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left;" id="{{$list->label_name}}">
							{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!}
							{!! Form::text($list->label_name, null ,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate form-control form-select','id'=>$list->label_name]) !!}
						</div>
					</div>
					@endif
					@if($list->type == 'date')  
					<div class="{{$list->label_name}}_more text-suggest hide">
						<div class="more_auto_generate margin-b-4 margin-t-10 margin-r-5" style="float:left;" id="{{$list->label_name}}">
							{!! Form::label($list->label_name, $list->name, ['class'=>'control-label font600']) !!} 
							{!! Form::text($list->label_name, null ,['class'=> $list->class_name. $list->type.' adv-search-text auto-generate js-date-range form-control form-select','id'=>$list->label_name]) !!}
						</div>
					</div>
					@endif
                @endif
            
                @endforeach          
            </div>        

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 bg-f0f0f0 p-b-10 border-radius-4">

                <div id="more_generate" class="margin-b-4 margin-t-10 margin-r-5" >
                    <input type="checkbox" data-page-id={{ $search_fields->id }} name="remember" id="search_remember" /><label for="search_remember" class="font600 med-orange cur-pointer">&nbsp;Remember Search</label>
                    {!! Form::select('more',(array)$moreArr,null,['class'=>'more_generate filter adv-search-text select2 form-control form-select','multiple'=>'multiple','placeholder'=>'Choose More Options &#9662;']) !!}
                </div>
                <div class="remember_search"> 

                </div>
            </div>

            <?php
        } catch (Exception $e) {
            //dd($e->getMessage());
			\Log::info("Exception raised in patient search fields: Error ".$e->getMessage());
        }
        ?>
        {!! HTML::style('css/search_fields.css') !!}
    </div>
</div>