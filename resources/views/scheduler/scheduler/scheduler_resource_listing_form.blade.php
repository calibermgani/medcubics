<ul class="list-group list-group-unbordered no-bottom js-selected_resource_id">
    @foreach($resource_listing as $keys=>$resource)
    <li class="list-group-item transparent scheduler-checkbox">
        {!! Form::checkbox('resource', $resource->id,true,['class'=>' js-scheduler_calendar js-resource_id','id'=>'sa'.$keys]) !!} 
        <label class="form-cursor" for="sa{{$keys}}"><span class="med-orange"> {{$resource->short_name}}</span> - {{$resource->resource_name}}</label> 
        <i class="fa fa-circle  pull-right" style="color:{{$resource->rgb_color}};"></i>
    </li>
    @endforeach
</ul>