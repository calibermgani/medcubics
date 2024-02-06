@if(isset($search_type) && $search_type == "dbsearch")
<div class="col-lg-12 col-md-12 col-sm-12 space20">   
    @if(!$imo_cpt_list->isEmpty()) 
    @foreach ($imo_cpt_list as $imo_cpt_list_val)   
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
            <li class="superbill">
                <table class="table-striped-view">
                    <tbody>
                        <tr>
                            <td style="padding: 0px 4px; width: 5%;">
                                <?php $unique_cpt_class_name = 'cpt_'.$imo_cpt_list_val->cpt_hcpcs; ?>
                                @if (in_array($imo_cpt_list_val->cpt_hcpcs, $sel_cpts_arr))
                                <?php $sel_opt_val  = 'checked'; ?>
                                @else
                                <?php $sel_opt_val  = ''; ?>
                                @endif
                                <input {{$sel_opt_val}} data-id="{{$unique_cpt_class_name}}" class="chk flat-red" name="imo_search_cpts[]" type="checkbox" 
                                value="{!! $imo_cpt_list_val->cpt_hcpcs !!}::
                                {!! $imo_cpt_list_val->short_description !!}::
                                {!! $imo_cpt_list_val->medium_description !!}::
                                {!! $imo_cpt_list_val->long_description !!}">
                            </td>                                                
                            <td style="width: 82%">{!! @$imo_cpt_list_val->short_description !!}</td>
                            <td style="width: 13%">{!! @$imo_cpt_list_val->cpt_hcpcs !!}</td>
                        </tr>
                    </tbody>
                </table>                                     
            </li>   
        </ul>
    </div>
    @endforeach
    @else
    {{ trans("practice/practicemaster/superbill.cptsearch")}}    
    @endif
</div>
@else
<div class="col-lg-12 col-md-12 col-sm-12 space20">
    <?php $temp_arr = array(); ?>
    @foreach ($imo_cpt_list as $imo_cpt_list_val)
    @if (!in_array($imo_cpt_list_val['@attributes']['CPT_CODE'], $temp_arr) && $imo_cpt_list_val['@attributes']['CPT_CODE']!='')
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <ul class="cpt-grid no-padding line-height-26 no-bottom" style="list-style-type:none;" id="">
            <li class="superbill">
                <table class="table-striped-view">
                    <tbody>
                        <tr>
                            <td style="padding: 0px 4px; width: 5%;">
                                <?php $unique_cpt_class_name = 'cpt_'.$imo_cpt_list_val['@attributes']['CPT_CODE']; ?>
                                @if (in_array($imo_cpt_list_val['@attributes']['CPT_CODE'], $sel_cpts_arr))
                                <?php $sel_opt_val  = 'checked'; ?>
                                @else
                                <?php $sel_opt_val  = ''; ?>
                                @endif
                                <input {{$sel_opt_val}} data-id="{{$unique_cpt_class_name}}" class="chk flat-red" name="imo_search_cpts[]" type="checkbox" value="{!! $imo_cpt_list_val['@attributes']['CPT_CODE'] !!}::{!! $imo_cpt_list_val['@attributes']['CPT_DESC_SHORT'] !!}::{!! $imo_cpt_list_val['@attributes']['CPT_DESC_MEDIUM'] !!}::{!! $imo_cpt_list_val['@attributes']['CPT_DESC_LONG'] !!}">
                            </td>                                                
                            <td style="width: 82%">{!! $imo_cpt_list_val['@attributes']['CPT_DESC_SHORT'] !!}</td>
                            <td style="width: 13%">{!! $imo_cpt_list_val['@attributes']['CPT_CODE'] !!}</td>
                        </tr>
                    </tbody>
                </table>                                     
            </li>   
        </ul>
    </div>
    <?php $temp_arr[] = $imo_cpt_list_val['@attributes']['CPT_CODE']; ?>
    @endif
    @endforeach
</div>
@endif
<script type="text/javascript">
    $(document).ready(function () {
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
    });
</script>