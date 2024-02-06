<div class="box-body no-padding m-b-m-15" style="margin-bottom: 3px;">                
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-4 ">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding ">
		<?php  	
			$plan_end_date = App\Http\Helpers\Helpers::getPatientPlanEndDate(@$ediEligibility->patient->id,$category); 
			if($plan_end_date == '0000-00-00' || $plan_end_date == '')
			{
				$getReachEndday = 0;
			}
			else 
			{
				$now = strtotime(date('Y-m-d')); // or your date as well
				$your_date = strtotime($plan_end_date);
				$datediff = $now - $your_date;

				$getReachEndday =  floor($datediff / (60 * 60 * 24));	
			} 	
		?>
           <p class="no-bottom font12">Coverage : <span class="med-orange">
		   @if($getReachEndday > 0)
			   {{ 'Inactive Coverage' }}
		   @else
				{{ @$ediEligibility->error_message }} 
		   @endif
		   </span> </p> 
		@if(@$ediEligibility->edi_eligibility_id!= '')
           <p class="no-bottom font12">Eligibility ID : <span class="med-green">{{ @$ediEligibility->edi_eligibility_id }} </span> </p> @endif        
        </div>
         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 no-padding"> 
             <divp class="pull-right">
        <p class="no-bottom font12"> Checked Date : <span class="bg-date">{{ App\Http\Helpers\Helpers::dateFormat(date('Y-m-d',strtotime(@$ediEligibility->created_at)),'date')}} </span></p>
       @if(@$ediEligibility->user->name!= '')
			<p class="no-bottom font12"> Checked By : <span class="med-green">{{ ucwords(@$ediEligibility->user->name) }} </span></p>
	   @endif
         </div>
        </div>        
    </div>    
   
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-6">
        <div class="table-responsive">
            <table  style="border:1px solid #f0f0f0;width:100%;line-height: 22px; font-size: 12px;">
                <tbody>
                    <tr >
                        <td style="width:30%;"><span class="margin-l-5 med-green">Patient Name</span></td><td style="width:5%;">:</td>
                        <td class="js_get_patient med-orange" >{{ @$ediEligibility->patient->last_name.', '.@$ediEligibility->patient->first_name }}</td>
                        <td></td>
                    </tr>
                    
                  
                    <tr>
                        <td><span class="margin-l-5 med-green">Policy ID </span></td><td>:</td>
                        <td class="js_get_policy">
						@if($page_type == 'pat_ins')
						{{ $policyid }}
						@else
						{{ ($patient_insurance!='')?$patient_insurance->policy_id :'' }}
						@endif
						</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td><span class="margin-l-5 med-green">Plan Type</span> </td><td>:</td>
                        <td>{{ @$ediEligibility->plan_type }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><span class="margin-l-5 med-green">Plan Name</span> </td><td>:</td>
                        <td>{{@$ediEligibility->plan_name }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><span class="margin-l-5 med-green">Plan Number</span> </td><td>:</td>
                        <td>{{@$ediEligibility->plan_number }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td><span class="margin-l-5 med-green">Group Name </span></td><td>: </td>
                        <td>{{ ucwords(@$ediEligibility->group_name) }}</td>
                        <td></td>
                    </tr>                  

                </tbody>
            </table>
        </div>
    </div>

		
		
	</div><!-- /.box-body -->
	<?php 
	$genderarray = ['M'=>'male','F'=>'Female'];
	 ?>
	 
	  @if(isset($ediEligibilitydemo->demo_type))
         <h6 class="med-orange">Demographics</h6> 	
         <div class="box box-view no-border no-shadow margin-b-25"><!--  Box Starts -->
             <div class="box-header-view no-border-radius line-height-10">
                 <h3 class="box-title">{{ ucwords(@$ediEligibilitydemo->demo_type) }}</h3>
                 <div class="box-tools pull-right">
                     <button class="btn btn-box-tool margin-t-m-4" data-widget="collapse"><i class="fa fa-minus "></i></button>
                 </div>
             </div><!-- /.box-header -->
             <div class="box-body no-padding">

                 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                     <div class="table-responsive">
                         <table  style="border:1px solid #E7FCFD;width:100%;line-height: 22px; font-size: 12px;">
                             <tbody>
                                 <tr >
                                     <td style="width:30%;"><span class="margin-l-5 med-green">Name</span></td><td style="width:5%;">:</td>
                                     <td class="js_get_patient med-orange" >{{ ucwords(strtolower(@$ediEligibilitydemo->first_name)).', '.ucwords(strtolower(@$ediEligibilitydemo->last_name)).' '.ucwords(strtolower(@$ediEligibilitydemo->middle_name)) }} </td>
                                     <td></td>
                                 </tr>

                                 @if(@$ediEligibilitydemo->dob!='' || @$genderarray[$ediEligibilitydemo->gender]!='')
                                 <tr>
                                     <td><span class="margin-l-5 med-green">D.O.B </span></td><td>:</td>
                                     <td class="js_get_insurance">{{ App\Http\Helpers\Helpers::dateFormat(@$ediEligibilitydemo->dob,'dob')}}{{(@$genderarray[$ediEligibilitydemo->gender]!='')?', '.$genderarray[$ediEligibilitydemo->gender]:'' }}</td>
                                     <td></td>
                                 </tr>		
                                 @endif

                                 @if(@$ediEligibilitydemo->address1!='')
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Address 1</span></td><td>:</td>
                                     <td>{{ @$ediEligibilitydemo->address1 }}</td>
                                 </tr>
                                 @endif

                                 @if(@$ediEligibilitydemo->address2!='')
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Address 2</span></td><td>:</td>
                                     <td>{{ @$ediEligibilitydemo->address2 }}</td>
                                 </tr>
                                 @endif

                                 @if(@$ediEligibilitydemo->city!='')
                                 <tr>
                                     <td></td><td></td>
                                     <td>{{ ucwords(strtolower(@$ediEligibilitydemo->city))}}{{(@$ediEligibilitydemo->state!='')?', '.$ediEligibilitydemo->state:'' }} {{ (@$ediEligibilitydemo->zip5!='0')?$ediEligibilitydemo->zip5:'' }} {{ (@$ediEligibilitydemo->zip4!='0')?'-'.$ediEligibilitydemo->zip4:'' }}</td>
                                 </tr>
                                 @endif

                                 <tr>
                                     <td><span class="margin-l-5 med-green">Member ID</span> </td><td>:</td>
                                     <td>{{ @$ediEligibilitydemo->member_id }}</td>							
                                 </tr>

                                 <tr>
                                     <td><span class="margin-l-5 med-green">Group Name</span> </td><td>:</td>
                                     <td>{{ ucwords(strtolower(@$ediEligibilitydemo->group_name)) }} </td>
                                 </tr>
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Group ID</span> </td><td>:</td>
                                     <td>{{ @$ediEligibilitydemo->group_id }}  </td>
                                 </tr>

                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div><!-- /.box-body -->
         @endif
         @if(isset($ediEliDemoDependent->demo_type))
         <div class="box box-view no-border no-shadow margin-t-m-20"><!--  Box Starts -->
             <div class="box-header-view no-border-radius line-height-10">
                 <h3 class="box-title">{{ ucwords(@$ediEliDemoDependent->demo_type) }}</h3>
                 <div class="box-tools pull-right">
                     <button class="btn btn-box-tool margin-t-m-4" data-widget="collapse"><i class="fa fa-minus "></i></button>
                 </div>
             </div><!-- /.box-header -->
             <div class="box-body no-padding">

                 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                     <div class="table-responsive">
                         <table  style="border:1px solid #E7FCFD;width:100%;line-height: 22px; font-size: 12px;">
                             <tbody>
                                 
                                					
						
                                  @if(@$ediEliDemoDependent->group_name!='')
                                 <tr >
                                     <td style="width:30%;"><span class="margin-l-5 med-green">Group Name</span></td><td style="width:5%;">:</td>
                                     <td>{{ @$ediEliDemoDependent->group_name }}</td>
                                     <td></td>
                                 </tr>
                                 @endif
                                 
                                   @if(@$ediEliDemoDependent->group_id!='')
                                 <tr >
                                     <td><span class="margin-l-5 med-green">Group ID</span></td><td>:</td>
                                     <td>{{ @$ediEliDemoDependent->group_id }}</td>
                                     <td></td>
                                 </tr>
                                 @endif
                                 
                                 @if(@$ediEliDemoDependent->relationship!='')
                                 <tr >
                                     <td><span class="margin-l-5 med-green">Relationship</span></td><td>:</td>
                                     <td>{{ @$ediEliDemoDependent->relationship }}</td>
                                     <td></td>
                                 </tr>
                                 @endif
                                 
                                
                                 <tr>
                                     <td style="width:30%;"><span class="margin-l-5 med-green">Name</span></td><td style="width:5%;">:</td>
                                     <td class="med-orange">{{ ucwords(strtolower(@$ediEliDemoDependent->first_name)).', '.ucwords(strtolower(@$ediEliDemoDependent->last_name)).' '.ucwords(strtolower(@$ediEliDemoDependent->middle_name)) }} </td>
                                     <td></td>
                                 </tr>
                                 
                                 <tr>
                                     <td><span class="margin-l-5 med-green">D.O.B </span></td><td>:</td>
                                     <td>@if(@$ediEliDemoDependent->dob!='') <i class="fa fa-birthday-cake"></i> {{ App\Http\Helpers\Helpers::dateFormat(@$ediEliDemoDependent->dob,'dob')}}@endif{{(@$genderarray[$ediEliDemoDependent->gender]!='')?', '.@$genderarray[$ediEliDemoDependent->gender]:'' }}</td>
                                     <td></td>
                                 </tr>
                                 
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Address </span></td><td>:</td>
                                     <td>{{ ucwords(strtolower(@$ediEliDemoDependent->address1)) }}</td>
                                     <td></td>
                                 </tr>
                                 
                                 @if(@$ediEliDemoDependent->address2!='')
                                 <tr>
                                     <td><span class="margin-l-5 med-green"> </span></td><td></td>
                                     <td>{{ @$ediEliDemoDependent->address2 }}</td>
                                     <td></td>
                                 </tr>
                                 @endif
                                 
                                 <tr>
                                     <td><span class="margin-l-5 med-green"> </span></td><td></td>
                                     <td>{{ ucwords(strtolower(@$ediEliDemoDependent->city))}}{{(@$ediEliDemoDependent->state!='')?', '.@$ediEliDemoDependent->state:'' }} {{ (@$ediEliDemoDependent->zip5!='0')?@$ediEliDemoDependent->zip5:'' }} {{ (@$ediEliDemoDependent->zip4!='0')?'-'.@$ediEliDemoDependent->zip4:'' }}</td>
                                     <td></td>
                                 </tr>
                                                                                              
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div><!-- /.box-body -->
		@endif
	
         
         
              
      
	<!--Insurance details -->  
         
     @if($ediContact_detail!='')      
        <p class="no-bottom font12 margin-t-m-10">Insurance : <span class="med-orange js_get_insurance">{{ @$ediEligibility->insurance_details->insurance_name }} </span> </p>                                       
        <p class="no-bottom font12"> Payer ID : <span class="med-green">{{ @$ediEligibility->insurance_details->payerid }}</span> </p>
       
     
	<div class="box box-view no-border no-shadow margin-t-0"><!--  Box Starts -->
	 <div class="box-header-view no-border-radius line-height-10">
                 <h3 class="box-title">Insurance Contact Details</h3>
                 <div class="box-tools pull-right">
                     <button class="btn btn-box-tool margin-t-m-4" data-widget="collapse"><i class="fa fa-minus "></i></button>
                 </div>
             </div><!-- /.box-header -->
             <div class="box-body no-padding">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
		
				
				@foreach($ediContact_detail as $ediContact_detail)
                    <div class="table-responsive">
                         <table  style="border:1px solid #E7FCFD;width:100%;line-height: 22px; font-size: 12px;">
                             <tbody>
                                 <tr>
                                     <td style="width:30%;"><span class="margin-l-5 med-green">Name</span></td><td style="width:5%;">:</td>
                                     <td>{{@$ediContact_detail->last_name}}{{@$ediContact_detail->first_name}}</td>
                                     <td></td>
                                 </tr>
                                 
                                 @if(@$ediContact_detail->address1 !='')
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Address</span></td><td>:</td>
                                     <td>{{@$ediContact_detail->address1 }}@if($ediContact_detail->address2 !=''), {{@$ediContact_detail->address2 }} @endif </td>
                                     <td></td>
                                 </tr>
                                 
                                 <tr>
                                     <td><span class="margin-l-5 med-green"></span></td><td></td>
                                     <td>{{@$ediContact_detail->city}}@if(@$ediContact_detail->state !=''), {{@$ediContact_detail->state }}@endif {{@$ediContact_detail->zip5}} @if(@$ediContact_detail->zip4 !='')-{{@$ediContact_detail->zip4}}@endif</td>
                                     <td></td>
                                 </tr>
                                 @endif
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Entity Code</span></td><td>:</td>
                                     <td>{{@$ediContact_detail->entity_code  }}</td>
                                     <td></td>
                                 </tr>
                                 @if(@$ediContact_detail->identification_code)
                                 <tr>
                                     <td><span class="margin-l-5 med-green">Identification Code</span></td><td>:</td>
                                     <td>{{@$ediContact_detail->identification_code  }}</td>
                                     <td></td>
                                 </tr>
                                  @endif
                                            
                                 
                                 
                               
                                 
                             </tbody>
                         </table>
                        @endforeach
						@endif
                    </div>
                    
                    
                
			</div>
			
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
				<table  style="border:1px solid #E7FCFD;width:100%;line-height: 22px; font-size: 12px;">
                    <tbody>
					@if($ediEli_Ins_SpPhy!='')
					@foreach($ediEli_Ins_SpPhy as $ediEli_Ins_SpPhy_details)
					@if($ediEli_Ins_SpPhy_details->insurance_type !="")
						<tr>
							<td style="width:30%;"><span class="margin-l-5 med-green">Insurance Type</span></td><td style="width:5%;">:</td>
							<td>{{@$ediEli_Ins_SpPhy_details->insurance_type  }}</td>
							<td></td>
						</tr>
						
						<tr>
							<td><span class="margin-l-5 med-green">Eligibility Code</span></td><td>:</td>
							<td>{{@$ediEli_Ins_SpPhy_details->eligibility_code  }}</td>
							<td></td>
						</tr>
						
						<tr>
							<td><span class="margin-l-5 med-green">Primary Care</span></td><td>:</td>
							<td>{{@$ediEli_Ins_SpPhy_details->primary_care  }}</td>
							<td></td>
						</tr>
					@endif	
					@endforeach
					@endif	
					</tbody>
				</table>
			</div>
		
		</div>
	</div><!-- /.box-body -->
	<!-- Insurance  -->
	@if(@$patient_eligibility->is_edi_atatched=='1' && $patient_eligibility->edi_filename!='')
		@if($patient_eligibility->patients_id == 0)
			<?php
			$patientencode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_eligibility->temp_patient_id,'encode'); 
			?>
		@else
			<?php
			$patientencode_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient_eligibility->patients_id,'encode'); 
			?>
		@endif		
	<div class="pull-right">	
		<a  class="btn btn-medcubics" onclick="window.open('{{ url('patients/getEligibilityMoreInfo/'.$patientencode_id.'/'.$patient_eligibility->edi_filename) }}', '_blank')">More Details </a>
		
	</div>	
	@endif
	
	@if($patient_id != '')	
		
		<button type="button" data-patientid="{{ $patient_id }}" @if($page_type == 'pat_ins') data-insuranceid="{{ $insuranceid }}" data-policyid="{{$policyid }}" data-page="pat_ins"  @endif data-category="{{ $category }}" class="btn btn-medcubics js_recheck_eligibility">Recheck Eligibility</button> <span class="coverloadingimg" id="coverimg" style="text-align:center;color:#00877f;display:none"><i class="fa fa-spinner fa-spin font20"></i> Processing</span> 
	@endif
<!--  Show Icon for Temp Popup  -->	
	@if($patient_id == '')	
		<button type="button" data-patientid="" data-category="{{ $category }}" class="btn btn-medcubics js_recheck_eligibility">Recheck Eligibility</button>  <span class="coverloadingimg" id="coverimg" style="text-align:center;color:#00877f;display:none"><i class="fa fa-spinner fa-spin font20"></i> Processing</span>
	@endif