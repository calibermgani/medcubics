<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2  margin-b-20 med-orange">Wallet History - Detailed</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">                    
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">   <?php $i = 0; ?>
						@foreach($search_by as $key=>$val)
							@if($i > 0){{' | '}}@endif
							<span class="med-green">{!! $key !!} :  </span>{{ @$val[0] }}                           
							<?php $i++; ?>
						@endforeach
					</div>	
                </div>
            </div>
        </div>
        @if(count($patient_wallethistory_filter) > 0)
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive">
                        <div class="ajax_table_list hide"></div>
                        <div class="data_table_list" id="js_ajax_part">
                            <table id="sort_list_noorder" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Acc No</th>
                                        <th>Patient Name</th>
                                        <th>Payment ID</th>
                                        <th>Mode of Pmt</th>
                                        <th>Payment Date</th>        
                                        <th>Total Payment($)</th>        
                                        <th>Posted($)</th>         
                                        <th>UnPosted($)</th>         
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($patient_wallethistory_filter as $list)
                                    <?php
									$patient_name = App\Http\Helpers\Helpers::getNameformat(@$list->patient->last_name, @$list->patient->first_name, @$list->patient->middle_name);
									$bal_amt = @$list->pmt_amt - @$list->amt_used;
                                ?>
									<?php //dd($list); ?>
                                    <tr style="cursor:default;">
                                        <?php /* patients details from SP  */
                                        if(isset($list->account_no) && $list->account_no != ''){
                                        ?>
                                        <td>{!! !empty($list->account_no )? @$list->account_no : '-Nill-' !!}</td>
                                        <td>{!! !empty($list->patient_name)? @$list->patient_name : '-Nill-'  !!}</td>
                                        <?php 
                                            } else {
                                        ?>
                                        <td>{!! !empty($list->patient->account_no)? @$list->patient->account_no : '-Nill-' !!}</td>
                                        <td>{!! !empty($patient_name)? $patient_name : '-Nill-' !!}</td>
                                        <?php } ?>
                                        <td>{!! !empty($list->pmt_no)? @$list->pmt_no : '-Nill-' !!}</td>
                                        <td>{!! !empty($list->pmt_mode)? @$list->pmt_mode : '-Nill-' !!}</td>										
                                        <td>{{ !empty($list->created_at)? App\Http\Helpers\Helpers::timezone(@$list->created_at, 'm/d/y') : '-Nill-' }}</td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->pmt_amt) !!}</td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$list->amt_used) !!}</td>
                                        <td class="text-right">{!! App\Http\Helpers\Helpers::priceFormat(@$bal_amt) !!}</td>                                       
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
            </div>
            <input type="hidden" id="pagination_prt" value="string"/>
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
        </div>
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->