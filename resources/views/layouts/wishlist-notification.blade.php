   <?php 
   $url =Request::url('/');
   ?>
   <div class="page-fav">
                        
    <div class="">
        <ul style="list-style:none;margin-left: -30px;">
            @foreach($list[1] as $wish)
            <li class="dropdown" style="padding-top:10px;">
                <a href="{{ @$wish->url }}" class="js_next_process p-l-4"><i class="{{$wish->module}}"></i> 
                    @if($wish->mode_id !="")
                        {{$wish->mode_id}}
                    @endif
                    <?php $a = explode(',',$wish->sub_module); ?>
                    @if($a[0]!="")
                    @for($i=0; $i < count($a); $i++)
                    @if($i!=0)<i class="fa fa-angle-double-right"></i>@endif {{$a[$i]}}
                    @endfor
                    @else{{ucfirst($wish->mode)}}
                    @endif
                </a>
                <span data-val="{{ @$wish->url }}" class="heart pull-right med-green">
                    <i class="fa fa-heart" style="cursor: pointer;" data-placement="right" data-toggle="tooltip" title="Remove"></i>
                </span>
            </li>
            @endforeach
        </ul>
    </div>
 
    
    <p class="text-center">                                 
    <a style="cursor: pointer;" class="heart logout-btn-orange"  data-val="{{$url}}" > 
    @if(!in_array($url,$list[0]))
        <i class="fa fa-heart-o"></i> Add page to Quick Link
    @else
        <i class="fa fa-heart "></i> Remove page from Quick Link
    @endif
    </a>
    </p>
</div>
