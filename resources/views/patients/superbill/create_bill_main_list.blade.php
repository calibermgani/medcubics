<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-b-20 mobile-scroll">                            
  
    <ul class="claims group no-padding line-height-26 border-radius-4 mobile-width" style="list-style-type:none; border: 1px solid #85e2e6;" id="sortable">
       <li class="claims-grid">
            <table class="table-striped-view superbill-claim">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 5%;">CPT</th>                                                
                        <th style="text-align: center;width: 5%">Mod 1</th>
                        <th style="text-align: center; width: 5%">Mod 2</th>
                        <th style="text-align: center; width: 3%">Units</th>

                        @for($i = 1; $i <= $max_count_icds_for_cpt; $i++)
                        <th style="text-align: center; width: 6%">ICD {!! $i !!}</th>
                        @endfor

                        <th style="text-align: center; width: 7%">Billed Amt</th>
                    </tr>
                </thead>
            </table>                                     
        </li>
        <?php $k = 0;?>
        @foreach ($bill_cpt_list as $bill_cpt_list_det_key=>$bill_cpt_list_det)
		<li class="claims-grid" id = "js-modifier-<?php echo $bill_cpt_list_det['cpt_hcpcs']; ?>">
            <table class="table-striped-view superbill-claim">
                <tbody>
                    <tr>
					
                        <td style="text-align: center; width: 5%; cursor: move;">{!! $bill_cpt_list_det['cpt_hcpcs'] !!}</td>                                                
                        <td style="text-align: center; width: 5%;" class="billing-select2-disabled-white">{!! Form::text('modifier1['.$k.']',null,['class'=>'select2 form-control input-sm-header-billing js-modifier', 'id' =>'modifier1-'.$i]) !!}    </td>
                        <td style="text-align: center; width: 5%;" class="billing-select2-disabled-white">{!! Form::text('modifier2['.$k.']', null,['class'=>'select2 form-control input-sm-header-billing js-modifier', 'id' =>'modifier2-'.$i, 'readonly' => 'readonly']) !!}     </td>
                        <td style="text-align: center; width: 3%">
                        {!! Form::text('unit['.$k.']', 1,['class'=>'no-border form-control input-sm-header-billing','maxlength'=>"2"]) !!}</td>

                        <?php 
						$icd_list_arr  = $cpt_icd_display_new_arr[$bill_cpt_list_det['cpt_hcpcs']]; 
						$def_sel_icd_val = "null";
						?>

                        @for($i = 1; $i <= $max_count_icds_for_cpt; $i++)
                        <?php $j  = 1; ?>	
                        @if($i>count($icd_list_arr))
                        <?php $icd_list_arr  = array(''=>'--'); ?>	
                        @else
                        @foreach ($icd_list_arr as $icd_list_arr_det_key=>$icd_list_arr_det_val)
                        @if($i==$j)
                        <?php $def_sel_icd_val = $icd_list_arr_det_key; ?>	
                        @endif
                        <?php $j  = $j+1; ?>	
                        @endforeach
                        @endif
                        <?php $billed_amt  = App\Models\cpt::where('cpt_hcpcs', $bill_cpt_list_det['cpt_hcpcs'])->value('billed_amount');
                              ?>
                        <td style="text-align: center; width: 6%" class="billing-select2-disabled-white"><div>{!! Form::select($bill_cpt_list_det_key.'_cpt_icd_display_'.$i, $icd_list_arr,$def_sel_icd_val,['id'=>'icd_'.$bill_cpt_list_det_key.'_'.$i,'class'=>'select2 form-control js_cpticdsel']) !!}</div></td>

                        @endfor

                        <td style="text-align: center; width: 7%"><span class='med-orange'>{!! Form::text('charge[]', $billed_amt,['class'=>'no-border border-radius-4 js_billed_amt input-sm-header-billing']) !!}</span></td>
                    </tr>
                </tbody>
            </table>                                     
        </li>
        <?php $k++;?>
        @endforeach
    </ul>
</div>


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 p-r-0">                            
    <div class="box box-view-border no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">Procedure List</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body superbill-list p-b-0">

            <ul class="cpt-grid no-padding line-height-26" style="list-style-type:none;" id="">

                @foreach ($bill_cpt_list as $bill_cpt_list_det)
                <li class="claim-cpt">
                    <table class="table-striped-view">
                        <tbody>
                            <tr>                                                            
                                <td style="width: 20%">{!! $bill_cpt_list_det['cpt_hcpcs'] !!}</td>
                                <td style="width: 80%">{!! $bill_cpt_list_det['short_description'] !!}</td>
                            </tr>
                        </tbody>
                    </table>                                    
                </li>
                @endforeach

            </ul>
        </div>                              
    </div>    

</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 p-r-0">                            
    <div class="box box-view-border no-shadow"><!--  Box Starts -->
        <div class="box-header-view">
            <i class="livicon" data-name="info"></i> <h3 class="box-title">ICD List</h3>

        </div><!-- /.box-header -->
        <div class="box-body superbill-list p-b-0">

            <ul class="cpt-grid no-padding line-height-26" style="list-style-type:none;" id="">

                @foreach ($bill_icd_list as $bill_icd_list_det)
                <li class="claim-cpt">
                    <table class="table-striped-view">
                        <tbody>
                            <tr>                                                            
                                <td style="width: 20%">{!! $bill_icd_list_det['icd_code'] !!}</td>
                                <td style="width: 80%">{!! $bill_icd_list_det['short_description'] !!}</td>
                            </tr>
                        </tbody>
                    </table>                                    
                </li>
                @endforeach

            </ul>
        </div>                              
    </div>    
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("select.select2.form-control").select2();
        $('ul#sortable').sortable({
            placeholder: '<li class="placeholder"></li>',
            start: function (event, ui) {
                var start_pos = ui.item.index();
                ui.item.data('start_pos', start_pos);
            },
            update: function (event, ui) {
                var start_pos = ui.item.data('start_pos');
                var end_pos = $(ui.item).index();
                cpt_position_change_track(start_pos, end_pos);
            }
        });
    });
    $('.superbill-list').slimScroll({
        height: '125px'
    });
</script>