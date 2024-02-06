@extends('admin')

@section('toolbar')
<div class="row-fluid">
    <div class="span12 navbar-fixed-top2 search-top">
        <div class="widget-title1 toolbar-band">
            <div class="span8">
                <h3 class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.common.help')}} font14"></i> Help <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{$staticpage->type}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>{{ $staticpage->title }}</span></h3>
            </div>                                              
            <div class="span4 icon-space">
                <div class="fon">
                <li><a href="#" onclick="history.go(-1);return false;"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
                    <!--span><a href="javascript:void(0);" class="js-print"><i class="icon-print right1 tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></span-->
                </div>
            </div>
        </div>
    </div>
</div>       
@stop

@section('practice')
<div class="row-fluid space">
    {!! $staticpage->content !!}
</div>
@stop     