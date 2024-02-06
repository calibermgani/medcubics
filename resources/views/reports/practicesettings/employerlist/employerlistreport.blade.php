<?php $heading_name = App\Models\Practice::getPracticeName(); ?>
<div class="box box-view no-shadow"><!--  Box Starts -->
   
       <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date: {{ date("m/d/y") }}</h3>
        </div>
    </div>

    <div class="box-body  bg-white"><!-- Box Body Starts -->      
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-t-m-10 margin-b-25 med-orange">Employer Summary</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-20 text-center">               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center"><?php $i = 0; ?>
                        @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!}: </span>{{ @$val[0] }}                           
                        <?php $i++; ?>
                        @endforeach 
					</div>                 
                </div>                
            </div>
        </div>
        
        <div class="table-responsive col-lg-12 margin-t-20">
            <table class="table table-striped table-bordered" id="sort_list_noorder">
                <thead>
                    <tr>
                        <th>Employer Name</th>
                        <th>Address Line 1</th>  		
                        <th>Address Line 2</th>  		
                        <th>City</th>  		
                        <th>ST</th>  		
                        <th>Zip Code</th>  		
                    </tr>
                </thead>
                <tbody>
                    @if(count($employer_filter_result) > 0)  
                    @php 
						@$total_adj = 0;
						@$patient_total = 0;
						@$insurance_total = 0;
					@endphp
                    @foreach($employer_filter_result as $list)
                    <tr style="cursor:default;">
                        <td>{!! @$list->employer_name !!}</td>
                        <td>{!! @$list->address1 !!}</td>
                        <td>{!! @$list->address2 !!}</td>
                        <td>{!! @$list->city !!}</td>
                        <td>{!! @$list->state !!}</td>
                        <td>{!! @$list->zip5 !!} @if(@$list->zip4){!! -@$list->zip4 !!} @endif</td>
                    </tr>
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
        {{--   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <p class="no-bottom"><label class="med-green font600"> Practice : </label>&nbsp; {!! @$heading_name !!}</p>
    </div>--}}
        @endif
    </div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->