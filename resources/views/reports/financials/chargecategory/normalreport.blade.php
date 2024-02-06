<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<style type="text/css">
.text-left{
    text-align: left;
}
</style>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">   
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body  bg-white"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Charge Category Report</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                    @if($i > 0){{' | '}}@endif
                    <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}
                        <?php $i++; ?>
                    @endforeach </div>
                </div>
            </div>
        </div>
        
        @if(count($charges_list) > 0)
        <div class="box-body">
            <div class="table-responsive col-lg-12">
                <table class="table table-striped table-bordered table-separate" id="sort_list_noorder_report">   
                    <thead>
                        <tr>
                            <th>CPT/HCPCS Category</th>
                            <th>CPT/HCPCS</th>
                            <th>Description</th>
                            <th>Rendering</th>
                            <th>Units</th>
                            <th class="text-right">Charge Amt($)</th>
                            <?php /*
                            <th class="text-right">Payments($)</th>
                             */ ?>
                            <th class="text-right">Work RVU($)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $temp = 0; $inc = 0;
                        $total_arr = json_decode(json_encode($total_arr), true);
                        $total_amt_charge = 0;$total_amt_payment=0;
                    ?>
                    @foreach($temp_array as  $result)
                    <?php 
                        $inc++;
                        $provider_id = $result->provider_id;                 
                        $provider_name = (isset($result->provider_name)) ? 'Rendering Provider - '.$result->provider_name : 'Rendering Provider - '.App\Models\Provider::getProviderFullName(@$provider_id);

                    if($temp != $provider_id){ ?>
                        <tr style="border: none !important; cursor:default;">
                            <td colspan="12" class="font600 med-green" style="background:#d9f3f0;"><h5 class="margin-t-5 margin-b-5">{{$provider_name}}</h5></td> 
                        </tr>
                    <?php }?>

                        <tr style="cursor:default;">
                            <td>{!! @$result->procedure_category !!}</td>
                            <td class="text-left">{{ !empty($result->cpt_code)? $result->cpt_code : '-Nill-' }}</td>
                            <td class="text-left">{{ !empty($result->description)? $result->description : '-Nill-' }}</td>
                            <td>{{ !empty($result->provider_short_name)? $result->provider_short_name : '-Nill-' }}</td>
                            <td class="text-left">{!! !empty($result->units)? $result->units : '-Nill-' !!}</td>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->charge)!!}</td>
                            <?php /*
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->payment)!!}</td>
                             */ ?>
                            <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$result->work_rvu)!!}</td>
                        </tr>
                        @if($inc == @$total_arr[$provider_id]['rec_cnt'] 
                        || ($inc+(($pagination->current_page - 1) * $pagination->per_page) == @$total_arr[$provider_id]['rec_cnt']) 
                        || ($inc+(($pagination->current_page - 1) * $pagination->per_page) == @$total_arr[$provider_id]['last_rec'] +1)
                        )  

                        <tr>
                            <td class="med-orange font600">Totals</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-left med-green font600">{!! @$total_arr[$provider_id]['units'] !!}</td>
                            <td class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$total_arr[$provider_id]['charge']) !!}</td>
                            <?php /*<td class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$total_arr[$provider_id]['payment']) !!}</td>*/ ?>
                            <td class="text-right med-green font600">${!! App\Http\Helpers\Helpers::priceFormat(@$total_arr[$provider_id]['work_rvu']) !!}</td>
                        </tr>
                            <?php $inc = 0; ?>
                        @endif
                            <?php  $temp = $provider_id;?>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                    Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
            </div>
            @else
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
            @endif
        </div><!-- Box Body Ends -->
    </div><!-- /.box Ends-->
</div>