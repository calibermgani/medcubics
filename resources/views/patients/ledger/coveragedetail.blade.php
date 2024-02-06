<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Coverage Details & Insurance Starts -->
	<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 margin-t-m-10 p-r-0 "><!-- Coverage Details Starts -->
		<div class="box box-view-border no-shadow margin-t-m-10 no-border-radius margin-t-m-20 no-b-t"><!-- Coverage box Starts -->
			<div class="box-header-view-white no-border-radius" style="color: #697d94;">
				<i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i><span class="" style="font-weight: 700; font-family: 'Maven Pro', sans-serif;"> Coverage Details </span>                            
			</div><!-- /.box-header -->
			<div class="box-body m-b-m-10 ledger-ins pr-l-5"><!-- Coverage box body starts -->
			<?php $gur_class=''; ?>
			@if($patients->is_self_pay =="No")
				@if(!empty($patients->patient_insurance))
					@foreach($patients->patient_insurance as $insurance)
					<?php $category_identify = $insurance->category ?>
							@if($category_identify =="Primary" || $category_identify =="Secondary"|| $category_identify =="Tertiary")
							
		                    <hr class="margin-t-m-5 margin-b-4 hide">
		                    <strong class="med-green">{{@$category_identify}} </strong>
							<p class="text-muted">{{ @$insurance->insurance_details->insurance_name }}</p>
							
							
							@endif            
					@endforeach 
				@else
					<p style="margin-top:75px;text-align: center;" class="med-gray-dark">Record not found</p>
					<hr class="margin-t-m-5 margin-b-4">
				@endif  
			@else
				<p><strong class="med-green">Self</strong></p>
				<hr class="margin-t-m-5 margin-b-4">
			@endif
				
				
			</div><!-- Coverage box-body ends -->
		</div><!-- Coverage box Ends -->
	</div><!-- Coverage Details Ends -->

	<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 p-l-0"><!-- Insurance Col Starts -->
		<div class="box box-view-border no-shadow margin-t-m-20 no-border-radius no-b-t no-b-l"><!-- Insurance Box Starts -->
			<div class="box-header-view-white no-border-radius" style="color: #697d94;">
				<i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i> <span class="" style="font-weight: 700; font-family: 'Maven Pro', sans-serif;"> Insurance</span>
				<!--div class="box-tools pull-right">
					<button class="btn btn-box-tool" ><i class="fa fa-minus"></i></button>
				</div-->
			</div><!-- /.box-header -->
			<div class="box-body table-responsive chat ledger-ins p-r-10"><!-- Insurance box body starts -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive  no-padding">
					<table id="" class="table table-borderless no-border">	
						<thead>
							<tr style="border-bottom: 2px solid #96dcd8">
                                                                    <th class="med-green font600" style="background: #fff;">Name</th>                                
								<th class="med-green font600" style="background: #fff;">Policy ID</th>
								<th class="med-green font600" style="background: #fff;">Effective Date</th>
								<th class="med-green font600" style="background: #fff;">Termination Date</th> 
								<th class="med-green font600" style="background: #fff;">Status</th> 
							</tr>
						</thead>
						<tbody>
							<!--array_merge($patients->patient_insurance,$patients->patient_insurance_archive);-->
						<?php 
							$insurance = $patients->patient_insurance;

						?>
						@if(count($insurance)>0)
						@foreach($insurance as $key => $pat_ins)
							<?php 
								$category_identify = $pat_ins->category;
								$date = \App\Http\Helpers\Helpers::timezone(date("Y-m-d H:i:s"),'Y-m-d'); 
							?>
							@if(@$pat_ins->effective_date =="0000-00-00" && @$pat_ins->termination_date =="0000-00-00") 
								<?php $status = "-" ?>
							@elseif(strtotime($date) <= strtotime(@$pat_ins->termination_date)) 
								<?php $status = "Active" ?>
							@elseif(@$pat_ins->effective_date != '' && @$pat_ins->termination_date =="0000-00-00") 
								<?php $status = "Active" ?>
							@else
								<?php $status = "Inactive" ?>
							@endif									
							<tr>          
								<td><span class="@if($category_identify == 'Primary'){{'pri-bg'}}@elseif($category_identify == 'Secondary'){{'sec-bg'}}@elseif($category_identify == 'Tertiary'){{'ter-bg'}} @else {{'other-bg'}} @endif">@if(count($patients->patient_insurance)> $key) {{ @$category_identify[0]}}@else O @endif</span> {{ @$pat_ins->insurance_details->insurance_name}}</td>
								<td>{{ @$pat_ins->policy_id }}</td>                               
								<td>@if(@$pat_ins->effective_date !='0000-00-00')
										{{ App\Http\Helpers\Helpers::dateFormat($pat_ins->effective_date,'date') }} @else - @endif</td>
								<td>@if(@$pat_ins->termination_date !='0000-00-00')
										{{ App\Http\Helpers\Helpers::dateFormat($pat_ins->termination_date,'date') }} @else - @endif</td>
								<td> {{ @$status }}</td>
							</tr>

							<tr>                                                                
								<td colspan="5">{{ @$pat_ins->insurance_details->address_1 }}, {{ @$pat_ins->insurance_details->city }}, {{ @$pat_ins->insurance_details->state }}, {{ @$pat_ins->insurance_details->zipcode5}}@if(@$pat_ins->insurance_details->zipcode4 !="")-{{ @$pat_ins->insurance_details->zipcode4 }} @endif</td>                        
							</tr>
							@endforeach
							@else
                             <tr><td colspan="5" class="text-center"  ><p style="margin-top:75px;" class="med-gray-dark">{{ trans("common.validation.not_found_msg") }}</p></td></tr>
							@endif
						</tbody>
					</table>    
				</div> 
				
			</div><!-- Insurance /.box-body Ends -->
		</div><!-- Insurance /.box Ends -->
	</div><!-- Insurance Col Ends -->
</div><!-- Coverage Details & Insurance Ends -->