<style type="text/css">
    .text-right{
        text-align: right;
    }
    table tr th{
        text-align: left !important;
    }
</style>
<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->        

    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>

    <div class="box-body  bg-white"><!-- Box Body Starts -->      
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Adjustment Analysis - Detailed</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                        @foreach($header as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
                        <?php $i++; ?>
                        @endforeach </div>                   
                </div>                
            </div>  
        </div>
        @if(!empty($adjustment))
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-border no-bottom">
                <div class="box-body">

                    <div class="table-responsive">
                        @foreach(@$adjustment as $adjustment)
                        <?php 
							$patient_name = @$adjustment->title.' '.App\Http\Helpers\Helpers::getNameformat(@$adjustment->last_name,@$adjustment->first_name,@$adjustment->middle_name);
						?>
                        @if(!empty($adjustment->cpt))
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding tabs-border yes-border margin-t-10">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                <span class="bg-white med-orange margin-l-10 font13 padding-0-4 font600">Claim No: {{$adjustment->claim_number}}</span>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="name" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Patient Name</label>
                                    {{ $patient_name }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Acc No</label>
                                    {{ !empty($adjustment->account_no)? $adjustment->account_no : '-Nil-' }}
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Responsibility</label>
                                    @if($adjustment->self_pay =='Yes')
                                        Patient
                                    @else
										<?php $ins = !empty($adjustment->insurance_id)? App\Http\Helpers\Helpers::getInsuranceName(@$adjustment->insurance_id) : '-Nil-'; ?>
										{{ $ins }}
                                    @endif
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Billing </label>
                                    {{ !empty($adjustment->billing_name)? $adjustment->billing_name : '-Nil-' }}
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Rendering</label>
                                    {{ !empty($adjustment->rendering_name)? $adjustment->rendering_name : '-Nil-' }}
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Facility</label>
                                    {{ !empty($adjustment->facility_name)? str_limit($adjustment->facility_name) : '-Nil-' }}
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <label for="act no" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 med-green font600">Tot Adj($)</label>
                                    {!! !empty($adjustment->tot_adj)? App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten(json_decode(json_encode($adjustment->tot_adj), true)))) : '-Nil-' !!}
                                </div>
                            </div> 
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <table class="popup-table-wo-border table table-responsive">                    
                                    <thead>
                                        <tr>
                                            <th style="background: #d9f3f0; color: #00877f;">DOS </th>                                       
                                            <th style="background: #d9f3f0; color: #00877f;">CPT</th>
                                            <th style="background: #d9f3f0; color: #00877f;">Payer</th>
                                            <th style="background: #d9f3f0; color: #00877f;">Adj Date</th>
                                            <th style="background: #d9f3f0; color: #00877f;">Adj Reason</th>
                                            <th style="background: #d9f3f0; color: #00877f;">CPT Adj($)</th>
                                            <th style="background: #d9f3f0; color: #00877f;">Reference</th>
                                            <th style="background: #d9f3f0; color: #00877f;">User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($adjustment->cpt as $cpt)
                                                <tr>
                                                    <td valign="top">{{ !empty($cpt->dos_from)? App\Http\Helpers\Helpers::dateFormat($cpt->dos_from,'dob') : '-Nil-' }} </td>
                                                    <td valign="top">{{ !empty($cpt->cpt_code)? @$cpt->cpt_code : '-Nil-' }}</td>
                                                    <td>
                                                        <table> 
                                                            <tbody>
                                                                @if(!empty($cpt->payer))
                                                                    @foreach(array_flatten(json_decode(json_encode($cpt->payer),true))  as $key=>$adj)
                                                                        <tr><td>{{ (!empty($adj))? $adj: '-Nil-' }}</td></tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table> 
                                                            <tbody>
                                                                @if(!empty($cpt->adj_date))
                                                                    @foreach(array_flatten(json_decode(json_encode($cpt->adj_date),true)) as $key=>$adj)
                                                                        <tr><td>{{ (!empty($adj))? $adj: '-Nil-' }}</td></tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table> 
                                                            <tbody>
                                                                @if(!empty($cpt->adj_reason))
                                                                    @foreach(array_flatten(json_decode(json_encode($cpt->adj_reason),true)) as $key=>$adj)
                                                                        <tr><td>{{ (!empty($adj))? $adj: '-Nil-' }}</td></tr>
                                                                    @endforeach
                                                                @endif
                											</tbody>
                                                        </table>
                                                    </td>
                                                    <td> 
                                                        <table>
                                                            <tbody>
                                                                @if(!empty($cpt->adj_amt))
                                                                    @foreach(array_flatten(json_decode(json_encode($cpt->adj_amt),true)) as $key=>$adj)
                                                                        <tr><td  class="@if($adj<0) med-red @endif">{{ (!empty($adj))? $adj: '-Nil-' }}</td></tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table> 
                                                            <tbody>
                                                                @if(!empty($cpt->reference))
                                                                    @foreach(array_flatten(json_decode(json_encode($cpt->reference),true)) as $key=>$adj)
                                                                        <tr><td>{{ (!empty($adj))? $adj: '-Nil-' }}</td></tr>
                                                                    @endforeach
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td valign="top">{{ !empty($adjustment->created_by)? App\Http\Helpers\Helpers::user_names($adjustment->created_by) : '-Nil-' }}</td>
                                                </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        @endforeach 

                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! @$pagination->pagination_prt !!}</div>
                        </div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.box -->
        </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-top:1px solid #f0f0f0">
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-12 no-padding">

                <div class="box-header-view-white no-border-radius pr-t-5 margin-b-10">
                    <i class="fa fa-bars font20"></i><span class="med-orange font20"> Summary</span>                     
                </div><!-- /.box-header -->

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
                    <table class="table table-separate table-borderless pr-r-m-20 table-separate yes-border border-radius-4 summary-box" style="">    
                        <tbody>
                        <tr> 
                            <td class="">Transaction Date</td>                                            
                            <td class='font600 text-right'>
                                {{ (@$header->{'Transaction Date'}[0]) }}</td>                                       
                        </tr>
                        @if($instype == "all")
                        <tr> 
                            <td class="">Total Insurance Adjustments</td>                                            
                            <td class='font600 text-right'>                                 
                                ${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Insurance))) !!}</td>                                       
                        </tr>

                        <tr> 
                            <td class="">Total Patient Adjustments</td>                                            
                            <td class='font600 text-right'>${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Patient)))!!}</td>
                        </tr>
                        
                        <tr> 
                            <td class="font600">Total Adjustments</td> 
                            <td class='font600 text-right' >${!! App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Insurance))+array_sum(array_flatten((array)@$tot_adjs->Patient))) !!}</td>
                        </tr>
                        @endif
                        @if($instype == "insurance")
                        <tr> 
                            <td class="">Total Insurance Adjustments</td>                                            
                            <td class='font600 text-right'>${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Insurance))) !!}</td>
                        </tr>
                        @endif
                        @if($instype == "self")
                        <tr> 
                            <td class="">Total Patient Adjustments</td>                                            
                            <td class='font600 text-right'>${!!App\Http\Helpers\Helpers::priceFormat(array_sum(array_flatten((array)@$tot_adjs->Patient)))!!}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>   
                </div>
            </div>
        </div> 
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div>
</div><!-- /.box Ends-->