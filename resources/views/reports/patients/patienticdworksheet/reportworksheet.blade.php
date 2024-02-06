<?php
 @$heading_name = App\Models\Practice::getPracticeName(); 
 @$icd_val = array();
?>	
<div class="box box-view no-shadow"><!--  Box Starts -->
    <div class="box-header-view">
        <i class="fa fa-user-secret" data-name="info"></i> <h3 class="box-title">User: @if(Auth::check() && isset(Auth::user()->short_name) ) {{  Auth::user()->short_name }} @endif</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y') }}</h3>
        </div>
    </div>
    <div class="box-body  bg-white"><!-- Box Body Starts -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <h3 class="text-center reports-heading p-l-2 margin-b-20 med-orange">ICD Worksheet</h3>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-0 text-center">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-6 no-padding">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 font600 text-center">
                        <?php $i = 0; ?>
                        @foreach($search_by as $key=>$val)
                        @if($i > 0){{' | '}}@endif
                        <span class="med-green">{!! $key !!} : </span>{{ @$val[0] }}                           
                        <?php $i++; ?>
                        @endforeach </div>                    
                </div>
            </div>
        </div>
        @if(isset($icd_result) && !empty($icd_result))  
            @foreach($icd_result as $list)
            <?php
				$icd_values = App\Models\Icd::getIcdValues(@$list->patient_icd);
				@$icd_val[]=count($icd_values);
			?>	
            @endforeach
            <?php @$maxval=max($icd_val);?>	
            <div class="table-responsive  col-lg-12">
                <table class="table table-striped table-bordered" id="sort_list_noorder">
                    <thead>
                        <tr>
                            <th>Acc No</th>  		
                            <th>Patient Name</th>
                            <th>DOB</th>        
                            <th>SSN</th>        
                            <th>Payer</th>
                            <?php /*@for($i=1;$i<=$maxval;$i++)
                            @endfor	*/?>
                            <th>ICD 10</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php
							@$total_adj = 0;
							@$patient_total = 0;
							@$insurance_total = 0;
						?>
                        @foreach($icd_result as $list)
                        <?php
							$icd_values = App\Models\Icd::getIcdValues(@$list->patient_icd);
							@$icd_val=count($icd_values);
							$name=App\Http\Helpers\Helpers::getNameformat(@$list->last_name,@$list->first_name,@$list->middle_name);
    					?>
                        <tr style="cursor:default;">
                            <td>{!! !empty($list->account_no)? @$list->account_no : '-Nill-' !!}</td>
                            <td>{!! !empty($name)? @$name : '-Nill-' !!}</td>
                            <td>{{ !empty($list->dob)? App\Http\Helpers\Helpers::dateFormat(@$list->dob,'dob') : '-Nill-' }}</td>
                            <td>@if(@$list->ssn != ''){{@$list->ssn}} @else -Nil- @endif</td>
                            <?php /* SP  */
                                if(isset($list->short_name) && $list->short_name != ''){
                            ?>
                            <td>{!! !empty($list->short_name)? @$list->short_name : '-Nill-' !!}</td>
                            <?php 
                                } else {
                            ?>
                            <td>
                                @if(isset($list->patient_insurance[0]))
                                    {{ (!empty($list->patient_insurance[0]->insurance_details->short_name))?$list->patient_insurance[0]->insurance_details->short_name:'-Nil-'}}
                                @else
                                    Self
                                @endif
                            </td>
                            <?php } 
                             @$icd_values = App\Models\Icd::getIcdValues(@$list->patient_icd);
							?>
                        <?php /*{{dd(implode(',', $icd_values))}}
                            @for($i=1;$i<=@$maxval;$i++)
                            @if(@$icd_values[$i]!='')
                            <td>{!!@$icd_values[$i]!!}</td> 
                            @else
                            <td>-</td> 	
                            @endif
                            @endfor	*/ ?>                    
                           <td> {{implode(', ', $icd_values)}} </td>                       
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
        @else
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green text-center margin-t-20"><h5 class="text-gray"><i>No Records Found</i></h5></div>
        @endif
    </div><!-- Box Body Ends --> 
</div>