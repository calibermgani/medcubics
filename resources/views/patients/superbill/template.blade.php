{!! Form::hidden('temp_superbill_name',$superbill_name,['class'=>'form-control input-sm','id'=>'temp_superbill_name']) !!}
<div class="box box-view no-shadow no-border" style="border-bottom:1px solid #ccc;"><!--  Box Starts -->


    <div class="box-body pateint-esuperbill-scroll no-bottom no-padding">

        @foreach ($superbill_arr['get_list_order'] as $superbill_arr_key => $superbill_arr_value)
        <?php $header_list = explode(",",$superbill_arr['order_header']); ?>
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12" style="padding-right: 0px; padding-left: 0px;">                            
            <ul class="checkbox-grid" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #00877f;  margin-bottom: 0;">
                <li class="superbill">
                    <table class="table-striped-view" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="padding: 0px 4px; width: 5%;"></th>
                                <th style="width: 69%">{{ $header_list[$superbill_arr_key] }}</th>
                                @if($superbill_arr_value == "skin_procedures" || $superbill_arr_value == "medications")<th style="text-align: center; width: 13%">Units</th> @endif
                                <th style="text-align: center; width: 13%">Code</th>
                            </tr>
                        </thead>
                    </table>                                     
                </li>

                @foreach ($superbill_arr[$superbill_arr_value] as $superbill_arr_item_key => $superbill_arr_item_value)
                <li class="superbill">
                    <table class="table-striped-view">
                        <tbody>
                            <tr>
                                <!--<td style="padding: 0px 4px; width: 5%;"><input type="checkbox" id="chkBoxHelp" class="chk flat-red">-->
                                <td style="padding: 0px 4px; width: 5%;">
                                    @if (in_array($superbill_arr_item_value['id'], $sel_cpts_vals))
                                    <?php $sel_opt_val  = true; ?>
                                    @else
                                    <?php $sel_opt_val  = null; ?>
                                    @endif
                                    <?php $unique_cpt_class_name = 'cpt_'.$superbill_arr_item_value['cpt_hcpcs']; ?>
                                    {!! Form::checkbox('cpt_codes_seleted[]', $superbill_arr_item_value['id'], $sel_opt_val, ['class'=>'chk flat-red','data-id'=>"$unique_cpt_class_name"]) !!}
                                </td>                                                
                                <td style="width: 69%">{{ $superbill_arr_item_value['short_description'] }}</td>
                                @if($superbill_arr_value == "skin_procedures" || $superbill_arr_value == "medications")
                                <td style="width: 13%">
                                    @if($superbill_arr_value == "medications")
                                    {{ $superbill_arr['medications_units'][$superbill_arr_item_key] }} 
                                    @elseif($superbill_arr_value == "skin_procedures")
                                    {{ $superbill_arr['skin_procedures_units'][$superbill_arr_item_key] }} 
                                    @endif
                                </td>
                                @endif
                                <td style="width: 13%">{{ $superbill_arr_item_value['cpt_hcpcs'] }}</td>
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                @endforeach


            </ul>
        </div>

        @endforeach

    </div>

</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
    });
</script>