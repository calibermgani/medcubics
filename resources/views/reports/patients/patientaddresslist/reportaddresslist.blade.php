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
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Address Listing</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-0 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"> <?php $i = 0; ?>
                    @foreach($search_by as $key=>$val)
                     @if($i > 0){{' | '}}@endif
                           <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
                          <?php $i++; ?>
                     @endforeach </div>  
                                                     
                </div>
            </div>
        </div>  
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
            <div class="box box-info no-shadow no-bottom no-border">
                <div class="box-body no-padding">
                    <div class="table-responsive mobile-md-scroll">
						@if(count($patient_address_list_filter) > 0) 
                        <table class="table table-striped table-bordered mobile-md-width" id="sort_list_noorder">
                            <thead>
                                <tr>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>MI</th>
                                    <th>Gender</th>  
                                    <th>DOB</th>  		
                                    <th>SSN</th>  		
                                    <th>Acc No</th>  		
                                    <th>Address Line 1</th>  		
                                    <th>Address Line 2</th>  		
                                    <th>City</th>  		
                                    <th>ST</th>  		
                                    <th>Zip Code</th>  		
                                </tr>
                            </thead>
                            <tbody>                                 
                                <?php
									$total_adj = 0;
									$patient_total = 0;
									$insurance_total = 0;
								?>
                                @foreach($patient_address_list_filter as $list)
                                <tr style="cursor:default;">
                                    <td>{!! !empty($list->last_name)? @$list->last_name : '-Nil-' !!}</td>
                                    <td>{!! !empty($list->first_name)? @$list->first_name : '-Nil-' !!}</td>
                                    <td>{!! !empty($list->middle_name)? @$list->middle_name : '-Nil-' !!}</td>
                                    <td>{!! !empty($list->gender)? @$list->gender : '-Nil-' !!}</td>
                                    <td>{{ !empty($list->dob)? App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') : '-Nil-' }}</td>
                                    <td>@if($list->ssn != ''){!! @$list->ssn !!} @else -Nil- @endif</td>
                                    <td>{!! !empty($list->account_no)? @$list->account_no : '-Nil-' !!}</td>
                                    <td>@if($list->address1 != '') {!! @$list->address1 !!} @else -Nil- @endif</td>
                                    <td>@if($list->address2 != ''){!! @$list->address2 !!} @else -Nil- @endif</td>
                                    <td>@if($list->city != '') {!! @$list->city !!} @else -Nil- @endif</td>
                                    <td>@if($list->state != ''){!! @$list->state !!} @else -Nil- @endif</td>
                                    <td>@if($list->zip5 != ''){!! @$list->zip5 !!} @if(@$list->zip4){!! -@$list->zip4 !!} @endif @else -Nil- @endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>    
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-20 no-padding margin-b-20 dataTables_info">
                            Showing {{@$pagination->from}} to {{@$pagination->to}} of {{@$pagination->total}} entries
                        </div>
                        <input type="hidden" id="pagination_prt" value="string"/>
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9 text-right no-padding">{!! $pagination->pagination_prt !!}</div>
                    </div>
                </div>
            </div>
        </div>
        {{--   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <p class="no-bottom"><label class="med-green font600"> Practice : </label>&nbsp; {!! @$heading_name !!}</p>
    </div>--}}
        @else
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->