@if(empty($patient_id))
<?php $patient_id = Route::current()->parameters['id']; ?>
@endif   
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner Content for full width Starts -->
    {!!Form::hidden('claim_detail_id',@$claims->claim_detail_id,['class' => 'claimdetail'])!!}
    {!!Form::hidden('claim_other_detail_id',@$claims->claim_detail_id,['class' => 'claimotherdetail'])!!}
    {!!Form::hidden('ambulance_billing_id',@$claims->claim_detail_id,['class' => 'claimbilling'])!!}
    {!!Form::hidden('claim_id',@$claims->id, ['id' => 'js-claim-id'])!!}
    @if(!empty($claims->claim_ids))
    <?php
    $claims_count = explode(',', $claims->claim_ids);
    if (count($claims_count) > 1) {
        $key = array_search($claims->id, $claims_count);
        if ($key + 1 <= count($claims_count)) {
            ?>
            {!!Form::hidden('next_id',@$claims_count[$key+1])!!}
        <?php
        }
    }
    ?>
    @endif    
    
    
    <!-- For batch process from E-superbill batch-->
    <div class="box-body-block"  style="padding-top:20px;"><!--Background color for Inner Content Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding margin-t-5 textbox-bg-yellow" ><!-- General Details Full width Starts -->
            <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 tab-r-b-1 no-padding"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tab-r-b-1 textbox-bg-yellow"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="font600 padding-4">General Details</span>
                        </div>
                        <span id="ajax-charge-loader"></span>
                        <div class="box-body form-horizontal"><!-- Box Body Starts -->
                            <div class="form-group-billing">
                                {!!Form::hidden('patient_id',$patient_id)!!}
                                @if(empty($claims))
                                {!!Form::hidden('charge_add_type','billing')!!}
                                @endif
                                {!! Form::label('Rendering Provider', 'Rend Prov', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600','id'=>'demo']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-whitebg @if($errors->first('rendering_provider_id')) error @endif">
                                    {!! Form::select('rendering_provider_id', array('' => '-- Select --') + (array)$rendering_providers,  @$claims->rendering_provider_id,['class'=>'select2 form-control', 'id' => 'providerpop', 'onChange' => 'getselecteddetail(this.id,this.value, \'Provider\');']) !!}  
                                    {!! $errors->first('rendering_provider_id', '<p> :message</p>')  !!}
                                </div>                                
                            </div>                            
                            <div class="form-group-billing">
                                {!! Form::label('', 'Refe Prov', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600','id'=>'ref_label']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 input-border @if($errors->first('referring_provider_id')) error @endif">
                                    {!! Form::text('refering_provider', 'John Willamson',['class'=>'form-control input-sm-header-billing js-remove-err autocomplete-ajax', 'id' => 'js-refer-provider','data-url' => 'api/getreferringprovider/provider']) !!} 
                                    {!! Form::hidden('refering_provider_id', @$claims->refering_provider_id, ['id' => 'refering_provider_id']) !!}     
                                    {!! $errors->first('referring_provider_id', '<p> :message</p>')  !!}
                                    <span style='display:none;'><small class='help-block med-orange' data-bv-validator='notEmpty' data-bv-for='document_title' data-bv-result='INVALID'>Choose valid provider</small></span>
                                </div>
                                
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Billing Provider', 'Bill Prov', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-whitebg @if($errors->first('billing_provider_id')) error @endif">
                                    {!! Form::select('billing_provider_id', array('' => '-- Select --') + (array)$billing_providers,  @$claims->billing_provider_id,['class'=>'select2 form-control','id' => 'billingprovider-pop','onChange' => 'getselecteddetail(this.id,this.value, \'Provider\');']) !!}  
                                    {!! $errors->first('billing_provider_id', '<p> :message</p>')  !!}
                                </div>                                
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-whitebg @if($errors->first('facility_id')) error @endif">  
                                    {!! Form::select('facility_id', array(''=>'-- Select --')+(array)$facilities,  @$claims->facility_id,['class'=>'select2 form-control','id'=>'facility_id', 'onChange' => 'changeselectval(this.value,\'Facility\', \'\');']) !!}   
                                    {!! Form::hidden('facility_clai_no')!!}
                                    {!! $errors->first('facility_id', '<p> :message</p>')  !!}
                                </div>                                                                 
                            </div>
                              <?php 
                            $insurance_data = (array)$insurance_data;
                            $search_text = 'Primary';
                            $promary_val = [];
                            $primary_val =  array_filter($insurance_data, function($el) use ($search_text) {
                                return ( strpos($el, $search_text) !== false );
                            });
                             ?>                               

                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->

                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" ><!--  2nd Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green no-padding margin-t-m-10">&emsp; </div>

                        <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">                         
                            
                            <div class="form-group-billing">
                                {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                @if($patients->is_self_pay == 'Yes' || empty($insurance_data))
                                 <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-whitebg">  
                                    {!! Form::select('self', ['1' => 'Self'], 1,['readonly' => 'readonly','class'=>'select2 form-control','id'=>'test_id', 'onChange' => 'changeselectval(this.value,\'Insurance\');']) !!}                                   
                                </div> 
                                @else                                                  
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-whitebg">  
                                    {!! Form::select('insurance_id', @$insurance_data,!empty($claims->insurance_id)?$claims->insurance_id:array_keys($primary_val),['readonly' => 'readonly','class'=>'select2 form-control','id'=>'insurance_id', 'onChange' => 'changeselectval(this.value,\'Insurance\');']) !!}                                   
                                    {!!Form::hidden('insurance_category')!!}
                                </div>
                                @endif     
                                
                            </div>
                            <div class="form-group-billing"> 
                                {!! Form::label('authorization', 'Auth#', ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!}                                                  
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 input-border">  
                                    {!! Form::text('auth_no',@$claims->auth_no,['maxlength'=>'25','id'=>'authorization','class'=>'form-control input-sm-header-billing']) !!}
                                    {!! Form::hidden('authorization_id',null,['id'=>'25','id'=>'auth_id']) !!}
                                </div>                                
                            </div>                            

                            <div class="form-group-billing">
                                {!! Form::label('mode', 'DOI',  ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-green font600']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 input-border">
                                    <i class="fa fa-calendar-o form-icon-billing"></i>                                       
                                    {!! Form::text('doi',(@$claims->doi && $claims->doi !='0000-00-00 00:00:00')?date('m/d/Y', strtotime($claims->doi)):'12/21/2015',['class'=>'form-control dm-date input-sm-header-billing', 'id' => 'date_of_injury']) !!}
                                </div>                          
                            </div>   
                            
                            <div class="form-group-billing">                              
                                {!! Form::label('pos', 'POS',  ['class'=>'col-lg-5 col-md-5 col-sm-5 col-xs-12 control-label-billing med-red font600']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 input-border">                                                                        
                                    {!! Form::text('pos_name',  @$claims->pos_name, ['class'=>'form-control input-sm-header-billing ', 'id' => 'pos_name' ,'readonly' => 'readonly','tabindex'=>'-1']) !!}
                                    {!! Form::hidden('pos_code', @$claims->pos_code, ['id' => 'pos_code']) !!}
                                </div>    
                            </div>

                                                                                   

                        </div><!-- /.box-body -->
                    </div><!--  2nd Content Ends -->
                    
                 
                </div><!--  Box Ends -->
            </div><!-- Only general details Content Ends -->
            <!-- Display ICD orders from E-superbill -->
           
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12" style="background:#fcfaf5;"><!--  2nd Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10 font600">Diagnosis - ICD 10</div>

                        <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">                         

                              
                     @if(!empty($claims)) 
<?php $icd_lists = array_flip(array_combine(range(1, count(explode(',', $claims->icd_codes))), explode(',', $claims->icd_codes))); ?>              
<?php $icd = App\Models\Icd::getIcdValues($claims->icd_codes); ?>

            @endif
                    
                    
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding" ><!--  2nd Content Starts -->                    
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '1',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd1',45646,['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '2',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd2',85676,['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '3',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd3','3R976',['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '4',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd4','54C.454',['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
                                </div>                                                     
                            </div>                     
                    </div><!--  2nd Content Ends --> 
                    
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding" ><!--  2nd Content Starts -->                    
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '5',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd1','53623',['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '6',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd2','5J.3535',['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '7',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd3',@$icd[3],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '8',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd4',@$icd[4],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
                                </div>                                                     
                            </div>                     
                    </div><!--  2nd Content Ends --> 
                    
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding" ><!--  2nd Content Starts -->                    
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '9',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd1',@$icd[1],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd1" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[1])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '10',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd2',@$icd[2],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[2])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '11',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd3',@$icd[3],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[3])}}@endif</span>
                                </div>                                                     
                            </div>
                            <div class="form-group-billing">                            
                                {!! Form::label('icd1', '12',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-2 control-label']) !!} 
                                <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 input-border">
                                    {!! Form::text('icd4',@$icd[4],['class'=>'form-control input-sm-header-billing js-icd', 'data-val'=>"1"]) !!}
                                    <span id="icd2" class="icd-hover">@if(!empty($icd[1])){{App\Models\Icd::getIcdDescription($icd[4])}}@endif</span>
                                </div>                                                     
                            </div>                     
                    </div><!--  2nd Content Ends --> 

                            
                        </div><!-- /.box-body -->
                    </div><!--  2nd Content Ends -->

          
            
        </div><!-- General Details Full width Ends -->
        
        
        

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white no-padding"><!-- Inner Content for full width Starts -->
    
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8 mobile-scroll">                            
        <ul class="billing line-height-26 border-radius-4 no-padding mobile-width billing-charge-table" style="border-color:#85e2e6">
                <li class="billing-grid">
                    <table class="table-billing-view">
                        <thead>
                            <tr>
                                <th style=" width: 2%;">&emsp;</th>
                                <th style=" width: 6%;">From</th>                                                
                                <th style=" width: 6%">To</th>                                
                                <th style=" width: 8%">CPT</th>
                                <th style=" width: 4%">M 1</th>
                                <th style=" width: 4%">M 2</th>
                                <th style=" width: 4%">M 3</th>    
                                <th style=" width: 4%">M 4</th>  
                                <th style=" width: 23%">ICD Pointers</th>
                                <th style=" width: 3%">Units</th>
                                <th style=" width: 6%">Charges ($)</th>
                            </tr>
                        </thead>
                    </table>                                     
                </li>
                <!-- Display CPT from E-superbill -->
                <?php
                $count = 10;
                $count_cnt = 0;
                if (!empty($claims)) {

                    $cpt_codes = explode(',', $claims->cpt_codes);
                    $count_cnt = count($cpt_codes);
                    if ($count_cnt > 6)
                        $count = 10;
                    $cpt_icd = explode('::', $claims->cpt_codes_icd);
                }
                ?>
                <!-- Display CPT from E-superbill -->
                @if(!empty($claims->dosdetails))
                    <?php if (count($claims->dosdetails) > $count) $count = count($claims->dosdetails); ?>
                <div class="js-append-parent">
                    @for($i=0;$i<$count;$i++)
                    <?php $date_to = '';
                    $date_from = ''; ?>
                    <?php
                    if (!empty($claims->dosdetails[$i]->dos_to)) {
                        $date_to = (@$claims->dosdetails[$i]->dos_to && $claims->dosdetails[$i]->dos_to != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claims->dosdetails[$i]->dos_to)) : '';
                    }
                    if (!empty($claims->dosdetails[$i]->dos_from)) {
                        $date_from = (@$claims->dosdetails[$i]->dos_from && $claims->dosdetails[$i]->dos_from != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claims->dosdetails[$i]->dos_from)) : '';
                    }
                    ?>
                    <?php
                    $icd_map = isset($claims->dosdetails[$i]->cpt_icd_map_key) ? array_combine(range(1, count(explode(',', $claims->dosdetails[$i]->cpt_icd_map_key))), explode(',', $claims->dosdetails[$i]->cpt_icd_map_key)) : '';
                    $style = '';
                    if ($i >= 6) {
                        $style = "style = display:none;";
                    }
                    ?>
                    <li id = "js-modifier-list-{{$i}}" class="billing-grid js-disable-div-{{$i}}" <?php echo $style; ?>>
                        <table class="table-billing-view superbill-claim">
                            <tbody>
                                <tr>
                                    <td class="text-center" style="width: 2%;"><input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight"></td>  
                                    <td class="text-center" style="width: 6%;"><input type="text" class="js_validate_date dm-date billing-noborder js_from_date" name=<?php echo "dos_from[" . $i . "]"; ?>   value = "{{@$date_from}}"   onchange="datevalidation(<?php echo $i; ?>)"></td>                                             
                                    <td class="text-center" style="width: 6%;"><input type="text" class="js_validate_date dm-date billing-noborder" name=<?php echo "dos_to[" . $i . "]"; ?>  value = "{{@$date_to}}" onchange="todatevalidation(<?php echo $i; ?>)"></td>                                   
                                    <td class="text-center" style="width: 8%;">
                                        <input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder" value = "{{@$claims->dosdetails[$i]->cpt_code}}"
                                               name= <?php echo "cpt[" . $i . "]"; ?> >
                                               <input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
                                    </td>
                                     
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier1-'.$i ]) !!}</td>
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier2-'.$i]) !!}</td>
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier3-'.$i]) !!}</td>
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'cpt_unit billing-noborder js-modifier', 'id' =>'modifier4-'.$i]) !!}</td>
                                
                                    <td style="text-align: center; width: 23%">
                                        @for($j=1;$j<=12;$j++)
                                        <input type="text" class="icd_pointer" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_map[$j] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>" style="border: 0px solid #e8e8e8;  width: 7%; padding: 0px; margin: 0px !important; border-radius: 4px; text-align: center;">
                                        <?php echo ($j != 12) ? '<span class="billing-pipeline" style="">|</span>' : '' ?>
                                        @endfor                                
                                    </td>
                                    <td style="text-align: center; width: 3%"><input class="cpt_unit billing-noborder" type="text" id="<?php echo $i ?>"  maxlength = 5 name=<?php echo "unit[" . $i . "]"; ?> value = "{{@$claims->dosdetails[$i]->unit}}" ></td>
                                    <td style="text-align: center; width: 6%"><input type="text" maxlength = 6 class = "js-charge form-control input-sm-header-billing billing-noborder" id= "charge_<?php echo $i ?>" 
                                                                                     name=<?php echo "charge[" . $i . "]"; ?> value = "{{@$claims->dosdetails[$i]->charge}}">
                                        <input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_allowed}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
                                        <input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$claims->dosdetails[$i]->cpt_icd_code}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()">
                                               <input type="hidden" class="cpt_icd_map_key billing-nb" value = "{{@$claims->dosdetails[$i]->cpt_icd_map_key}}" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> ></td>                              
                                </tr>
                            </tbody>
                        </table>                                     
                    </li>                
                    @endfor
                </div>
                @else
                <div class="js-append-parent">                    
                    <?php $dos_date = (!empty($claims)) ? date('m/d/Y', strtotime($claims->date_of_service)) : ''; ?>
                    @for($i=0;$i<$count;$i++)
                    <?php
                    $icd_val = isset($cpt_icd[$i]) ? App\Models\Icd::getIcdValues($cpt_icd[$i]) : '';
                    $icd_val_split = !empty($icd_val) ? implode(',', $icd_val) : '';
                    $icd_map = isset($cpt_icd[$i]) ? array_combine(range(1, count(explode(',', $cpt_icd[$i]))), explode(',', $cpt_icd[$i])) : '';
                    $style = '';
                    if ($i >= 6) {
                        $style = "style = display:none;";
                    }
                    ?>
                    <li id = "js-modifier-list-{{$i}}" class="billing-grid js-disable-div-{{$i}}" <?php echo $style; ?>>
                        <table class="table-billing-view superbill-claim">
                            <tbody>
                                <tr>
                                    <td class="text-center" style="width: 2%;" tabindex="0"><input tabindex = -1 type="checkbox" id="<?php echo $i; ?>"class="js-icd-highlight flat-red dm-date"></td>  
                                    <td class="text-center" style="width: 6%;"><input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="js_validate_date js_from_date dm-date billing-noborder" name=<?php echo "dos_from[" . $i . "]"; ?>  onchange="datevalidation(<?php echo $i; ?>)"></td>                                             
                                    <td class="text-center" style="width: 6%;"><input type="text" value = "<?php echo (isset($cpt_codes[$i]) && !empty($dos_date)) ? $dos_date : ''; ?>" class="js_validate_date dm-date billing-noborder" name=<?php echo "dos_to[" . $i . "]"; ?>  onchange="todatevalidation(<?php echo $i; ?>)"></td>                                   
                                    <td class="text-center" style="width: 8%;">
                                        <input type="text" id="<?php echo $i; ?>" readonly="readonly" class="js-cpt billing-noborder" tabindex = -1 value = "<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('cpt_hcpcs') : ''; ?>" 
                                               name= <?php echo "cpt[" . $i . "]"; ?> >
                                               <input type="hidden" class="billing-noborder cpt_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->charge}}" name="<?php echo "cpt_amt[" . $i . "]"; ?>">
                                    
                                   <td class="text-center" style="width: 4%">{!! Form::text('modifier1['.$i.']',@$claims->dosdetails[$i]->modifier1,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier1-'.$i]) !!}</td>
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier2['.$i.']' ,@$claims->dosdetails[$i]->modifier2,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier2-'.$i]) !!}</td>
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier3['.$i.']' ,@$claims->dosdetails[$i]->modifier3,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier3-'.$i ]) !!}</td>
                                    <td class="text-center" style="width: 4%">{!! Form::text('modifier4['.$i.']' ,@$claims->dosdetails[$i]->modifier4,['class'=>'cpt_unit billing-noborder js-modifier', 'maxlength' => 2, 'id' =>'modifier4-'.$i]) !!}</td>
                                
                                    <td style=" width: 23%; text-align: center;">
                                        <?php $a = array();
                                        $cpt_icd_key = '' ?>
                                        @for($j=1;$j<=12;$j++)
                                        <input type="text" class="icd_pointer billing-icd-pointers" tabindex = -1 readonly="readonly" name=<?php echo 'icd' . $j . '_' . $i; ?> value = "<?php echo isset($icd_map[$j]) ? $icd_lists[$icd_map[$j]] : ''; ?>" id="<?php echo 'icd' . $j . '_' . $i; ?>">
                                    <?php echo ($j != 12) ? ' <span class="billing-pipeline" style="">|</span>' : '' ?>
                                    <?php if (!empty($icd_map[$j]))
                                        $key = array_push($a, $icd_lists[$icd_map[$j]]);
                                    ?>
                                        @endfor             
                                    </td>
                                    <td style="text-align: center; width: 3%"><input class="cpt_unit billing-noborder" value= "<?php echo isset($cpt_codes[$i]) ? 1 : '' ?>" maxlength = 5 type="text" id="<?php echo $i ?>" name=<?php echo "unit[" . $i . "]"; ?> ></td>
                                    <td style="text-align: center; width: 6%"><input type="text" class = "js-charge billing-noborder" maxlength = 6 id= "charge_<?php echo $i ?>" name=<?php echo "charge[" . $i . "]"; ?> value="<?php echo isset($cpt_codes[$i]) ? App\Models\Cpt::where('id', $cpt_codes[$i])->value('billed_amount') : ''; ?>"><input type="hidden" class="cpt_icd_map billing-nb" value = "{{@$icd_val_split}}" name=<?php echo "cpt_icd_map[" . $i . "]"; ?>  onChange="modelvalue()"></td>
                                    <input type="hidden" class="cpt_allowed_amt_<?php echo $i; ?>" value = "{{@$claims->dosdetails[$i]->cpt_allowed}}" name="<?php echo "cpt_allowed[" . $i . "]"; ?>">
                                    <input type="hidden" class="cpt_icd_map_key billing-nb" value = "<?php echo!empty($a) ? implode(',', $a) : ''; ?>" name=<?php echo "cpt_icd_map_key[" . $i . "]"; ?> ></td>
                                </tr>
                            </tbody>
                        </table>                                     
                    </li>                
                    @endfor
                </div>
                @endif
<?php
$display_class = 'style="display:none;"';
if ($count_cnt >= 6 || !empty($claims->dosdetails) && count($claims->dosdetails) >= 6) {
    $display_class = '';
}
?>               

                {!!Form::hidden('appentvalue', $i,['id' => 'js-appendrow'])!!}

            </ul>

            <div class="margin-t-m-8">                
                <span class="append cur-pointer font600 med-green" <?php echo $display_class; ?>><i class="fa fa-plus"></i> Add</span>                
            </div>
        </div>
        
    <div>
        
        <p class="pull-right no-bottom">
            <span class=" med-green font600">Total Charges ($) </span>
            <span class="med-orange font600 margin-l-20">  <input type="text" readonly = "readonly"name = "total_charge" class="js-total" style="border: 0px solid #e8e8e8; width: 50%; border-radius: 4px; text-align: right;"></span>
        </p>
    </div>
		<?php
        if(!empty($claims->claim_detail_id)){ 
			$claim_detail_url = url() . '/patients/claimdetail/edit/' . $claims->claim_detail_id;
        } else {
			$claim_detail_url = url() . '/patients/claimdetail/create/' . $patient_id;
        }
        if(!empty($claims->ambulance_billing_id)) {
			$claim_billing_url = url() . '/patients/claimbilling/edit/' . $claims->ambulance_billing_id;
        } else {
			$claim_billing_url = url() . '/patients/claimbilling/create/' . $patient_id;
        }
        if(!empty($claims->claim_other_detail_id)) {
			$claim_other_detail_url = url() . '/patients/claimotherdetail/edit/' . $claims->claim_other_detail_id;
        } else {
			$claim_other_detail_url = url() . '/patients/claimotherdetail/create/' . $patient_id;
        }        
    </div>       
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white margin-t-0 no-padding"><!-- Inner Content for full width Starts -->
            <p class="no-bottom margin-t-m-20 med-orange font600" style=" margin-bottom:-4px;">Payment Posting</p>
            <div class="box-body-block no-border no-padding"  style="margin-top:0px; " ><!--Background color for Inner Content Starts -->

      
        


        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding hide">

    <div class="box box-info no-border no-shadow">
       
        <div class="box-body table-responsive ">
            <table class="table table-bordered table-striped" style="border-collapse:separate;">	

                <thead>
                    <tr>
                        <th>DOS</th>
                        <th>CPT</th>                                
                        <th>ICD</th>                               
                        <th>Provider</th>
                        <th>Insurance</th>
                        <th>Billed</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                <tr>
                        <td><a href="{{ url('patients/payments/1/edit') }}">03-28-2015</a></td>
                        <td>95675</td>  
                        <td>09223</td>
                        <td>Victoria Bellot</td> 
                        <td>Empire Blue</td>
                        <td>$ 123.00</td>
                        <td>$ 114.00</td>
                        <td>$ 24.00</td>                       
                        <td class='claim-denied'>Denied</td>
                    </tr>


                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">03-28-2015</a></td>
                    <td>95675</td>  
                    <td>09223</td>
                    <td>Empire Blue</td>                               
                    <td>Victoria Bellot</td>                                
                    <td>$ 123.00</td>
                    <td>$ 114.00</td>                        
                    <td>$ 27.00</td>
                    <td class='claim-paid'>Paid</td>
                </tr>

                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">07-16-2014</a></td>
                    <td>67654</td>  
                    <td>93432</td>
                    <td>Cigna</td>                                
                    <td>Brooke Bair</td>                                
                    <td>$ 205.00</td>
                    <td>$ 150.00</td>                       
                    <td>$ 355.00</td>
                    <td class='claim-paid'>Paid</td>
                </tr>

                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">07-16-2014</a></td>
                    <td>67654</td>  
                    <td>93432</td>
                    <td>Cigna</td>                                
                    <td>Brooke Bair</td>                                
                    <td>$ 205.00</td>
                    <td>$ 150.00</td>                       
                    <td>$ 355.00</td>
                    <td class='claim-submitted'>Submitted</td>
                </tr>

                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">03-13-2015</a></td>
                    <td>65756</td>  
                    <td>85675</td>
                    <td>Cigna</td>                                
                    <td>Emmanuel Loucas</td>                                
                    <td>$313.00</td>
                    <td>$ 242.00</td>
                    <td>$ 112.00</td>                        
                    <td class='claim-paid'>Paid</td>
                </tr>

                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">05-26-2015</a></td>
                    <td>67654</td>  
                    <td>93432</td>
                    <td>Cigna</td>                                
                    <td>Brooke Bair</td>                                
                    <td>$ 205.00</td>
                    <td>$ 150.00</td>                       
                    <td>$ 355.00</td>
                    <td class='claim-denied'>Denied</td>
                </tr>

                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">11-18-2016</a></td>
                    <td>65756</td>  
                    <td>85675</td>
                    <td>Atena</td>                                
                    <td>Emmanuel Loucas</td>                                
                    <td>$313.00</td>
                    <td>$ 242.00</td>
                    <td>$ 112.00</td>                        
                    <td class='claim-denied'>Denied</td>
                </tr>

                <tr>
                    <td><a href="{{ url('patients/payments/1/edit') }}">03-28-2015</a></td>
                    <td>95675</td>  
                    <td>09223</td>
                    <td>Empire Blue</td>                               
                    <td>Victoria Bellot</td>                                
                    <td>$ 123.00</td>
                    <td>$ 114.00</td>                        
                    <td>$ 27.00</td>
                    <td class='claim-ppaid'>P.Paid</td>
                </tr>

                </tbody>
            </table>

        </div><!-- /.box-body -->
    </div><!-- /.box -->


</div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8">                            
           <ul class="billing" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                <li class="billing-grid">
                    <table class="table-billing-view" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 3%;">&emsp;</th>                                                                             
                                <th style="text-align: center;width: 6%">DOS</th>                                
                                <th style="text-align: center; width: 6%">CPT</th>
                                <th style="text-align: center; width: 6%">Billed</th>
                                <th style="text-align: center; width: 6%">Allowed</th>                               
                                <th style="text-align: center; width: 6%">Paid</th>
                                <th style="text-align: center; width: 6%">Co-Ins</th>
                                <th style="text-align: center; width: 6%">Co-Pay</th>
                                <th style="text-align: center; width: 6%">Deductible</th>
                                <th style="text-align: center; width: 6%">With Held</th>
                                <th style="text-align: center; width: 10%">Adj</th>
                                <th style="text-align: center; width: 8%">Denial Code</th>
                                <th style="text-align: center; width: 8%">Billed To</th> 
                                <th style="text-align: center; width: 6%">Balance</th>                                                               
                            </tr>
                        </thead>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>

                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class="flat-red"></td>  
                                
                                <td style="text-align: center; width: 6%;"><input type="text" class="dm-date" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>

                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%" class="billing-select2-disabled-white">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 form-control']) !!}</td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                                                                         
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class="flat-red"></td>  
                                
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>

                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%" class="billing-select2-disabled-white">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 form-control']) !!}</td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                                                                          
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class="flat-red"></td>  
                                
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>

                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%" class="billing-select2-disabled-white">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 form-control']) !!}</td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                        

                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class="flat-red"></td>  
                                
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>

                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 8%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%" class="billing-select2-disabled-white">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 form-control']) !!}</td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                          

                            </tr>
                        </tbody>
                    </table>                                     
                </li>



                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class="flat-red"></td>  
                                
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>

                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%" class="billing-select2-disabled-white">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 form-control']) !!}</td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                         

                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class="flat-red"></td>  
                                
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>

                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                               <td style="text-align: center; width: 8%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 8%" class="billing-select2-disabled-white">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 form-control']) !!}</td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                         

                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                
                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;border-radius: 0px 0px 0px 4px" class="bg-aqua"></td>  
                                
                                <td style="text-align: center; width: 6%;" class="bg-aqua"></td>                                   
                                <td style="text-align: center; width: 6%;" class="bg-aqua"> </td>  
                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 323.00</span></td>
                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 250.00</span></td>

                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 223.00</span></td>
                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 100.00</span></td>
                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 0.00</span></td> 
                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 0.00</span></td> 
                                <td style="text-align: center; width: 6%" class="bg-aqua"><span class="med-green font600">$ 0.00</span></td> 
                                <td style="text-align: center; width: 10%" class="bg-aqua"><span class="med-green font600">$ 0.00</span></td>
                               <td style="text-align: center; width: 8%" class="bg-aqua"></td> 
                                <td style="text-align: center; width: 8%" class="bg-aqua"></td>
                                <td style="text-align: center; width: 6%; border-radius: 0px 0px 4px 0px" class="bg-aqua"><span class="med-orange font600">$ 100.00</span></td>                         

                            </tr>
                        </tbody>
                    </table>                                     
                </li>                                                                
                
            </ul>                                    
        </div>   

    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="btn-group">
                <a href="#js-model-popup" data-toggle="modal" data-target="#js-model-popup" class="claimdetail font600" style="border-right:1px solid #ccc; padding-right: 10px;"  data-url="{{$claim_detail_url}}">Claim Details</a>
                <a href="#js-model-popup" data-toggle="modal" data-target="#js-model-popup" class=" claimbilling font600" style="border-right:1px solid #ccc; padding: 0px 10px; "  data-url="{{$claim_billing_url}}">Ambulance Billing</a>
                <a href="#js-model-popup" data-toggle="modal" data-target="#js-model-popup" class=" claimotherdetail font600" style="padding-left:10px;"  data-url="{{$claim_other_detail_url}}">Other Details</a>
            </div>

           <div class="btn-group pull-right">
                <a  class="claimdetail font600 form-cursor" style="border-right:1px solid #ccc; padding-right: 10px;"  data-toggle = "collapse" data-target = "#view_transaction" ><i class="fa fa-file-text-o"></i> View Transaction</a>
                <a href="" data-toggle="modal" data-target="#" class=" claimbilling font600" style="border-right:1px solid #ccc; padding: 0px 10px; " ><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
                <a href="" data-toggle="modal" data-target="#" class=" claimbilling font600" style="border-right:1px solid #ccc; padding: 0px 10px; " ><i class="fa fa-file-pdf-o"></i> CMS 1500</a>
                <a href="#js-model-popup" data-toggle="modal" data-target="#js-model-popup" class=" claimotherdetail font600" style="padding-left:10px;" ><i class="fa fa-repeat"></i> Re-Submit</a>
            </div>
        </div>


<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 m-b-m-8   md-display hide">
                <p ><span class="med-orange font600">Payment Details </span></p>
                <p class="space-m-t-7"><span class="med-green font600">Patient Due </span><span class="pull-right font600 med-gray-dark">$ 235.55</span></p>
                <p class="space-m-t-7"><span class="med-green font600">Insurance Due </span> <span class="pull-right font12 font600 med-gray-dark">$ 1341.00</span></p>
                <p class="space-m-t-7"><span class="med-green font600">Total Due </span> <span class="pull-right font12 font600 bg-date">$ 1576.55</span></p>
               
            </div>

<div class="col-md-12 padding-t-5"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            
           
            

              <div id = "view_transaction" class="collapse out col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                <div class="box-body-block no-padding"><!--Background color for Inner Content Starts -->

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" style="border:1px solid #b4f7f7; margin-top: 10px; margin-bottom:10px;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10" style="margin-bottom:6px;">
                            <span class="bg-white med-orange" style="padding:0px 4px;"> Transaction Details</span>
                        </div>

                        <table class="popup-table-wo-border table table-responsive" style="margin-bottom:0px;">                    
                            <thead>
                                <tr>                                               
                                    <th>Date</th>                                
                                    <th>Description</th>                               
                                    <th>Amt</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>                                                
                                    <td>12-12-15</td> 
                                    <td>Lorem Ipsum is simply dummy </td>
                                    <td>$ 23,000.00</td>                                        
                                </tr>
                                <tr>                                                
                                    <td>01-10-16</td> 
                                    <td>Lorem Ipsum is simply dummy  text of the printing </td>
                                    <td>$ 400.00</td>                                        
                                </tr>

                                <tr>                                                
                                    <td>01-12-16</td> 
                                    <td>Lorem Ipsum is simply dummy text of the printing and typesetting </td>
                                    <td>$ 240.00</td>                                        
                                </tr>    
                                <tr>                                                
                                    <td>01-18-16</td> 
                                    <td>Lorem Ipsum is simply dummy   </td>
                                    <td>$ 217.00</td>                                        
                                </tr>

                                <tr>                                                
                                    <td>01-22-16</td> 
                                    <td>Lorem Ipsum is simply dummy text of the printing </td>
                                    <td>$ 40.00</td>                                        
                                </tr>    
                            </tbody>
                        </table>
                    </div>
                </div><!-- Inner Content for full width Ends -->
            </div><!--Background color for Inner Content Ends -->
            
        </div>
        
        
        <div class="box-footer space20">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
                <a href="{{ url('contactdetail')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>

            </div>
        </div><!-- /.box-footer -->
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->



<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding hide">

            <div class="col-lg-7 col-md-5 col-sm-12 col-xs-12">

                <div class="form-group-billing margin-t-8">       
                    <div class="col-lg-1 col-md-2 col-sm-12 med-orange no-padding">Alert</div>

                    <div class="col-lg-5 col-md-7 col-sm-12 col-xs-12">
                        {!! Form::text('notes',@$claims->notes,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Alert Message']) !!}
                    </div>                                     
                </div>            

            </div>
            <div class="col-lg-5 col-md-7 col-sm-12 col-xs-12 pull-right no-padding">
                <div class="form-group margin-t-8">                            
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding p-r-0">
                        <div class="col-lg-2 col-md-3 col-sm-2 col-xs-6 med-orange no-padding p-l-0">Anesthesia</div>
                        <div class="col-lg-3 col-md-2 col-sm-3 col-xs-5 bootstrap-timepicker">
                            <i class="fa fa-clock-o form-icon-billing"></i> 
                            {!! Form::text('anesthesia_start',@$claims->anesthesia_start,['class'=>'form-control input-sm-modal-billing timepicker1 dm-time','placeholder'=>'Start Time', 'id' => 'anesthesia_start']) !!}

                        </div>

                        <div class="col-lg-3 col-md-2 col-sm-2 col-xs-10 bootstrap-timepicker">
                            <i class="fa fa-clock-o form-icon-billing"></i> 
                            {!! Form::text('anesthesia_stop',@$claims->anesthesia_stop,['class'=>'form-control input-sm-modal-billing timepicker1 dm-time','placeholder'=>'Stop Time','id' => 'anesthesia_stop']) !!}                    
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 ">
                            {!! Form::text('anesthesia_minute',@$claims->anesthesia_minute,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Min', 'readonly' => 'readonly']) !!}                    
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 ">
                            {!! Form::text('anesthesia_unit',@$claims->anesthesia_unit,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Units', 'readonly' => 'readonly']) !!}                    
                        </div>   
                    </div>
                </div>
            </div>
        </div>
        
    </div><!--Background color for Inner Content Ends -->
</div><!-- Inner Content for full width Ends -->

@push('view.scripts')
<script type="text/javascript">
    $('#authorization').attr('autocomplete','off');
</script>
@endpush