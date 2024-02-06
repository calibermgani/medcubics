<table class="table table-striped-view" style="width:100%;">
    <tr>
     
        <td style="vertical-align: top; color:#00877f; font-size:11px;text-align:right;font-family:sans-serif; ">
            <div><span style="color:#868686;margin-left:-50px;">Eligibility ID :</span> {{@$result_array->eligible_id}} </div>
            <div style="margin-top:5px;"><span style="color:#868686;">Created Date :</span> {{App\Http\Helpers\Helpers::dateFormat(@$result_array->created_at,'datetime')}} </div>
        </td>        
    </tr>
</table>

<h4 style="color:#00877f;font-size:11px;font-family:sans-serif;margin-bottom:-30px;">Plan Information</h4>
<table class="table table-striped-view" style="border:1px solid #e3e6e6; background: #f4f4f4; width:100%; margin-bottom:20px; margin-top:-13px;">   
    <tr>        
        <td style="vertical-align: top; color:#646464; font-size:11px;font-family:sans-serif;  ">
            <ul style="line-height:20px; list-style-type:none; margin-left:-30px;margin-top:5px;   font-size:11px;font-family:sans-serif;">
                <li style="color:#F07D08"><span style="color:#00877f;">Coverage :</span> {{@$result_array->plan->coverage_status_label}}</li>
                <li><span style="color:#00877f;">Plan Type :</span> {{@$result_array->plan->plan_type_label}}</li>
                <li><span style="color:#00877f;">Plan Name :</span> {{@$result_array->plan->plan_name}}</li>
                <li><span style="color:#00877f;">Plan Number :</span> {{(@$result_array->plan->plan_number=='')? '  -  ': @$result_array->plan->plan_number }}</li>
            </ul>
        </td>

        <td style="vertical-align: top; color:#646464; font-size:11px;font-family:sans-serif; ">
            <ul style="line-height:20px; list-style-type:none; margin-left:-30px;margin-top:5px;   font-size:11px;font-family:sans-serif;">
                <li style="text-transform: capitalize"><span style="color:#00877f;">Group Name :</span> {{ (@$result_array->plan->group_name=='')? '  -  ': @$result_array->plan->group_name }}</li>	
                @if(!empty(@$result_array->plan->dates))		           
                    @foreach(@$result_array->plan->dates as $date_details)
    				
    					@if($date_details->date_type == 'plan_begin_begin')
    					<?php 	$plan_label = 'Plan Begin';  ?>
    					@elseif($date_details->date_type == 'plan_begin_end')
    					<?php	$plan_label = 'Plan End';  ?>
    					@else
    					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
    					@endif
                        <li><span style="color:#00877f;">{{ $plan_label }} :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                    @endforeach
                @endif
            </ul>
        </td>
    </tr>
</table>

<h4 style="color:#00877f;font-size:11px;font-family:sans-serif;margin-bottom:-30px;">Demographics</h4>
<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse;margin-bottom: 20px; margin-top:-20px; width:100%;">
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: middle; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Type</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: middle; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Subscriber/Dependent Information</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: middle; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Address</td>

        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: middle; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Group Name</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: middle; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Group ID</td>
    </tr>
    <tr style="border:1px solid #e3e6e6; color:#646464; border-collapse:collapse;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;font-size:11px;font-family:sans-serif;vertical-align:top;padding:5px 10px 2px 10px; ">Subscriber</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;vertical-align:top; "> 
            <ul style="line-height:18px; list-style-type:none; color:#646464; margin-left:-30px; margin-top:0px; font-size:11px;font-family:sans-serif;">		
                <li style="text-transform: capitalize;color:#f07d08">{{@$result_array->demographics->subscriber->last_name}}, {{@$result_array->demographics->subscriber->first_name}} {{@$result_array->demographics->subscriber->middle_name}}</li>
                @if(@$result_array->demographics->subscriber->dob || @$result_array->demographics->subscriber->gender )
                    <li> {{ App\Http\Helpers\Helpers::dateFormat(@$result_array->demographics->subscriber->dob,'dob') }} {{@$result_array->demographics->subscriber->gender}}</li>
                @endif

                @if(@$result_array->demographics->subscriber->member_id)
                    <li><span style="color:#00877f">Member ID:</span>{{@$result_array->demographics->subscriber->member_id}}</li>			           
                @endif
            </ul>                
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;vertical-align:top; "> 

            <ul style="line-height:18px; list-style-type:none; color:#646464; margin-left:-30px; margin-top:0px; font-size:11px;font-family:sans-serif;">			
                <li>{{@$result_array->demographics->subscriber->address->street_line_1}} </li>
                @if(@$result_array->demographics->subscriber->address->street_line_2)
                    <li>{{@$result_array->demographics->subscriber->address->street_line_2}}</li>			           
                @endif 

                @if(@$result_array->demographics->subscriber->address->city)
                    <li>{{@$result_array->demographics->subscriber->address->city}} @if(@$result_array->demographics->subscriber->address->state) - {{@$result_array->demographics->subscriber->address->state}} @endif</li>			           
                @endif 

                @if(@$result_array->demographics->subscriber->address->zip)
                    <li>{{@$result_array->demographics->subscriber->address->zip}}</li>			           
                @endif 
            </ul>				                                                          
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;font-size:11px;font-family:sans-serif;vertical-align:top;padding:5px 10px 2px 10px;line-height: 18px;text-transform: capitalize ">{{@$result_array->demographics->subscriber->group_name}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;font-size:11px;font-family:sans-serif;vertical-align:top;padding:5px 10px 2px 10px; ">{{@$result_array->demographics->subscriber->group_id}}</td>	
    </tr>

    @if(@$result_array->demographics->dependent!=null)
    <tr style="border:1px solid #e3e6e6;border-collapse:collapse">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;font-size:11px;font-family:sans-serif;vertical-align:top;padding:5px 10px 2px 10px;">Dependent</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align:top;"> 
            <ul style="line-height:18px; list-style-type:none; color:#646464; margin-left:-30px; margin-top:0px; font-size:11px;font-family:sans-serif;">
                <li style="text-transform: capitalize;color:#f07d08">{{@$result_array->demographics->dependent->last_name}}, {{@$result_array->demographics->dependent->first_name}} {{@$result_array->demographics->dependent->middle_name}}</li>
                
				<?php $dependant_dob = (@$result_array->demographics->dependent->dob!=null)? App\Http\Helpers\Helpers::dateFormat(@$result_array->demographics->dependent->dob,'dob') : ''  ?>
				
				<li>
                    <span style="color:#00877f">D.O.B : </span>{{@$dependant_dob}} {{@$result_array->demographics->dependent->gender}}
                </li>			
                @if(@$result_array->demographics->dependent->relationship)
                    <li><span style="color:#00877f">Relationship :</span>{{@$result_array->demographics->dependent->relationship}}</li>			           
                @endif 
            </ul>
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align:top;"> 

            <ul style="line-height:18px; list-style-type:none; margin-left:-30px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">			
                <li style="text-transform: capitalize;">{{@$result_array->demographics->dependent->address->street_line_1}} </li>
                @if(@$result_array->demographics->dependent->address->street_line_2)
                    <li style="text-transform: capitalize;">{{@$result_array->demographics->dependent->address->street_line_2}}</li>			           
                @endif 

                @if(@$result_array->demographics->dependent->address->city)
                    <li style="text-transform: capitalize;">{{@$result_array->demographics->dependent->address->city}} @if(@$result_array->demographics->dependent->address->state) - {{@$result_array->demographics->dependent->address->state}} @endif</li>			           
                @endif 

                @if(@$result_array->demographics->dependent->address->zip)
                    <li>{{@$result_array->demographics->dependent->address->zip}}</li>			           
                @endif 
            </ul>						                           
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;font-size:11px;font-family:sans-serif;vertical-align:top;padding:5px 10px 2px 10px; text-transform: capitalize; line-height: 18px;"> {{@$result_array->demographics->dependent->group_name}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;color:#646464;font-size:11px;font-family:sans-serif;vertical-align:top;padding:5px 10px 2px 10px;">{{@$result_array->demographics->dependent->group_id}}</td>
    </tr>
    @endif
</table>

<h4 style="color:#00877f;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:20px;">Insurance Information</h4>
<table style="width:100%;margin-top:-10px; margin-bottom: 20px;border: 1px solid #00877f; background:#eaf6f5;">
    <tr>
        <td style="color:#646464;font-size:11px;font-family:sans-serif;"><span style="color:#00877f">Insurance Name :</span> {{@$result_array->insurance->name}}</td>
        <td style="color:#646464;font-size:11px;font-family:sans-serif;"><span style="color:#00877f">ID :</span> {{@$result_array->insurance->id}}</td>
        <td style="color:#646464;font-size:11px;font-family:sans-serif;"><span style="color:#00877f">Payer Type :</span> {{@$result_array->insurance->payer_type_label}}</td>
    </tr>
</table>

<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:10px; margin-bottom:-30px;">Service Providers</h4>
<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%; margin-top:-40px; margin-bottom: 30px;">
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Insurance Info</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Additional Info</td>           
    </tr>
	@if(@$result_array->insurance->service_providers->physicians!=null)
    @foreach(@$result_array->insurance->service_providers->physicians as $service_provider)
    <tr>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse; vertical-align: top;"> 
            <ul style="line-height:18px; margin-top:0px; list-style-type:none; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			
                @if(@$service_provider->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type :</span> {{@$service_provider->insurance_type_label}}</li>			           
                @endif 

                @if(@$service_provider->eligibility_code_label)
                    <li><span style="color:#00877f">Eligibility Code :</span> {{@$service_provider->eligibility_code_label}}</li>			           
                @endif 

                @if(@$service_provider->primary_care)
                    <li><span style="color:#00877f">Primary Care :</span> {{@$service_provider->primary_care}}</li>			           
                @endif 		

                @foreach(@$service_provider->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }} :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach			
            </ul>			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;"> 
            @foreach(@$service_provider->contact_details as $contact_details)   
            <ul style="line-height:18px; list-style-type:none; margin-left:-30px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li style="color:#f07d08">{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif </li>			           
                @endif

                @if(@$contact_details->address->city)
                    <li>{{@$contact_details->address->city}} - {{@$contact_details->address->state}}@if(@$contact_details->address->zip), {{@$contact_details->address->zip}} @endif</li>
                @endif			

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}}</li>			           
                @endif 

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->entity_code_label}}</li>			           
                @endif 		

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
                    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach	
				
            </ul>	
            @endforeach			                                                          
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;"> 
            <ul style="line-height:18px; list-style-type:circle; margin-left:-25px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$service_provider->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{@$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
	
</table>

<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:40px; margin-bottom:0px;">Plan : Deductible</h4>

<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6;margin-top:-20px; border-collapse:collapse; width:100%;">
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Option</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Level</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Network</td>           
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Amount</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Option Information</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Additional Info</td>			
    </tr>

   @if(@$result_array->plan->financials->deductible->remainings->in_network)
        @foreach(@$result_array->plan->financials->deductible->remainings->in_network as $in_network)
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Remainings</td>
            <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; text-transform: capitalize">{{@$in_network->level}}</td>
            <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
            <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
            <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;"> 

                <ul style="line-height:18px; list-style-type:none; margin-top:0px;margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			
                    @if(@$in_network->insurance_type_label)
                        <li><span style="color:#00877f">Insurance Type :</span> {{@$in_network->insurance_type_label}} </li>			           
                    @endif

                    @if(@$in_network->description)
                        <li><span style="color:#00877f">Description :</span> {{@$in_network->description}} </li>			           
                    @endif

                    @if(@$in_network->pos_label)
                        <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}} </li>			           
                    @endif

                    @if(@$in_network->authorization_required)
                        <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>			
                    @endif

                    @if(@$in_network->quantity_code)
                        <li><span style="color:#00877f">Quantity Code :</span> {{ @$in_network->quantity_code }} </li>			           
                    @endif

                    @if(@$in_network->quantity)
                        <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}} </li>			           
                    @endif

                    @foreach(@$in_network->dates as $date_details)
    					@if($date_details->date_type == 'plan_begin_begin')
    					<?php 	$plan_label = 'Plan Begin';  ?>
    					@elseif($date_details->date_type == 'plan_begin_end')
    					<?php	$plan_label = 'Plan End';  ?>
    					@else
    					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
    					@endif
                        <li style="margin-left: -30px;"><span style="color:#00877f">{{ $plan_label  }}  :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>			           
                    @endforeach
                </ul>		
            </td>
            <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;"> 
                @foreach(@$in_network->contact_details as $contact_details)
                <ul style="line-height:18px;margin-top:0px; list-style-type:none; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                    @if(@$contact_details->last_name || @$contact_details->first_name)
                        <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                    @endif

                    @if(@$contact_details->address->street_line_1)
                        <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                    @endif

                    @if(@$contact_details->address->city || @$contact_details->address->zip)
                        <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                    @endif

                    @if(@$contact_details->entity_code_label)
                        <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                    @endif

                    @if(@$contact_details->identification_code_label)
                        <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                    @endif

                    @foreach(@$contact_details->contacts as $contact_key => $contact_value)
                        <li style="margin-left: -30px;"><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>			           
                    @endforeach
                </ul>									
                @endforeach			
            </td>

            <td>
                <ul style="line-height:18px;margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                    @foreach(@$in_network->comments as $comments)            
                        <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>			
                    @endforeach
                </ul>
            </td>
        </tr>
        @endforeach
    
	@else
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
        </tr>
    @endif
	
	@if(@$result_array->plan->financials->deductible->remainings->out_network)
    @foreach(@$result_array->plan->financials->deductible->remainings->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;">Remainings</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; text-transform: capitalize">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;"> 

            <ul style="line-height:18px; list-style-type:none; margin-top:0px;margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif

                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif

                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif

                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>		
                @endif

                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif

                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li style="margin-left: -30px;"><span style="color:#00877f">{{ $plan_label  }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>			           
                @endforeach
            </ul>										
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;"> 
            @foreach(@$out_network->contact_details as $contact_details)

            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>							
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <ul style="line-height:18px;margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">		
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>    
        </td>
    </tr>
    @endforeach
    @endif
	
	@if(@$result_array->plan->financials->deductible->spent->in_network)
    @foreach(@$result_array->plan->financials->deductible->spent->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Spent</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 

            <ul style="line-height:18px; list-style-type:none; margin-left:-30px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif

                @if(@$in_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$in_network->description}}  </li>			           
                @endif

                @if(@$in_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif

                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>			
                @endif

                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }}  :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>								
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;"> 
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none; margin-left:-30px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>		
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <ul style="line-height:18px; list-style-type:circle; margin-left:-25px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">	
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
	
	@if(@$result_array->plan->financials->deductible->spent->out_network)
    @foreach(@$result_array->plan->financials->deductible->spent->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Spent</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 

            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif

                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif

                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif

                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif

                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif

                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>									
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif
                
                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>		
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <ul style="line-height:18px;margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">		
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform:capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
	
	@if(@$result_array->plan->financials->deductible->totals->in_network)
    @foreach(@$result_array->plan->financials->deductible->totals->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Totals</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$in_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$in_network->description}}  </li>			           
                @endif
                
                @if(@$in_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif
                
                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}} </li>			
                @endif
                
                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>			
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;"> 
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>		
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <ul style="line-height:18px;list-style-type:circle; margin-left:-25px;margin-top:0px;color:#646464;font-size:11px;font-family:sans-serif;">	
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
	
	@if(@$result_array->plan->financials->deductible->totals->out_network)
    @foreach(@$result_array->plan->financials->deductible->totals->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Totals</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none; margin-top: 0px;margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif
                
                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif
                
                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif

                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>					
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;"> 
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif
                
                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
					<li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>		
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;"> 
            <ul style="line-height:18px; margin-top:0px;list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">		
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
</table>

<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:0px;">Plan : Stop Loss</h4>

<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%;margin-bottom:20px; margin-top:-20px;">
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Option</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Level</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Network</td>           
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Amount</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Option Information</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: center; font-size:11px;font-family:sans-serif;">Additional Info</td>			
    </tr>

	@if(@$result_array->plan->financials->stop_loss->remainings->in_network)
    @foreach(@$result_array->plan->financials->stop_loss->remainings->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Remainings</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$in_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$in_network->description}}  </li>			           
                @endif
                
                @if(@$in_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif
                
                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>			
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@else
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
        </tr>
    @endif

	@if(@$result_array->plan->financials->stop_loss->remainings->out_network)
    @foreach(@$result_array->plan->financials->stop_loss->remainings->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Remainings</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif
                
                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif
                
                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif

                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>				
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px;list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif

	@if(@$result_array->plan->financials->stop_loss->spent->in_network)
    @foreach(@$result_array->plan->financials->stop_loss->spent->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Spent</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; text-transform: capitalize">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$in_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$in_network->description}}  </li>			           
                @endif

                @if(@$in_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif
                
                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }}  :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>			           
                @endforeach
            </ul>			
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				 <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">						
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
	
	@if(@$result_array->plan->financials->stop_loss->spent->out_network)
    @foreach(@$result_array->plan->financials->stop_loss->spent->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Spent</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; text-transform: capitalize">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif

                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif

                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif

                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif

                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif

                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>				
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px;margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
	
	@if(@$result_array->plan->financials->stop_loss->totals->in_network)
    @foreach(@$result_array->plan->financials->stop_loss->totals->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Totals</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; text-transform: capitalize">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif

                @if(@$in_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$in_network->description}}  </li>			           
                @endif

                @if(@$in_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif

                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>			
                @endif

                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>			
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">
                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif

	@if(@$result_array->plan->financials->stop_loss->totals->out_network)
    @foreach(@$result_array->plan->financials->stop_loss->totals->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">Totals</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">

            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif

                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif

                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif

                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif
                
                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>				
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
                    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
</table>

<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:0px;">Plan : Coinsurance</h4>
<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%;margin-top:-20px;margin-bottom:20px;">
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Level</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Network</td>           
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Amount</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Coinsurance Information</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Additional Info</td>			
    </tr>		 

    @if(@$result_array->plan->financials->coinsurance->percents->in_network || @$result_array->plan->financials->coinsurance->percents->out_network)
		
	@if(@$result_array->plan->financials->coinsurance->percents->in_network)
    @foreach(@$result_array->plan->financials->coinsurance->percents->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">IN</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">${{@$in_network->percent}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif

                @if(@$in_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$in_network->description}}  </li>			           
                @endif

                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif
                
                @if(@$in_network->time_period)
                    <li><span style="color:#00877f">Time Period :</span> {{@$in_network->time_period}} ({{@$in_network->time_period_label}})</li>
                @endif
                
                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif

                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>			
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif

                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top: 0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
   @endif

    @if(@$result_array->plan->financials->coinsurance->percents->out_network)
    @foreach(@$result_array->plan->financials->coinsurance->percents->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">${{@$out_network->percent}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif
                
                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif
                
                @if(@$out_network->time_period)
                    <li><span style="color:#00877f">Time Period :</span> {{@$out_network->time_period}} ({{@$out_network->time_period_label}})</li>
                @endif
                
                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif
                
                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>				
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top: 0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px;list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
    @else
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
        </tr>
    @endif
</table>


<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:0px;">Plan : Co-Payment</h4>
<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%;margin-top:-20px; margin-bottom:20px;">
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Level</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Network</td>           
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Amount</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Co-Payment Info</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Additional Info</td>			
    </tr>		 

    @if(@$result_array->plan->financials->copayment->amounts->in_network || @$result_array->plan->financials->copayment->amounts->out_network)
		
	@if(@$result_array->plan->financials->copayment->amounts->in_network)
    @foreach(@$result_array->plan->financials->copayment->amounts->in_network as $in_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-transform: capitalize">{{@$in_network->level}}</td>
        <td style="border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">IN</td>
        <td style="border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">{{App\Http\Helpers\Helpers::priceFormat(@$in_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$in_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$in_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$in_network->description)
                    <li style="text-transform: capitalize"><span style="color:#00877f">Description :</span> {{@$in_network->description}}</li>
                @endif
                
                @if(@$in_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$in_network->pos_label}}</li>			           
                @endif
                
                @if(@$in_network->time_period)
                    <li><span style="color:#00877f">Time Period :</span> {{@$in_network->time_period}} ({{@$in_network->time_period_label}})</li>	
                @endif
                
                @if(@$in_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$in_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$in_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$in_network->quantity_code}}  </li>			           
                @endif

                @if(@$in_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$in_network->quantity}}  </li>			           
                @endif

                @foreach(@$in_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }}</li>
                @endforeach
            </ul>
        </td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">
            @foreach(@$in_network->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif

                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif

                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif
                
                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$in_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
    @endif
    
	@if(@$result_array->plan->financials->copayment->amounts->out_network)
    @foreach(@$result_array->plan->financials->copayment->amounts->out_network as $out_network)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$out_network->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">OUT</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{App\Http\Helpers\Helpers::priceFormat(@$out_network->amount,'yes')}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$out_network->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type  :</span> {{@$out_network->insurance_type_label}}</li>			           
                @endif
                
                @if(@$out_network->description)
                    <li><span style="color:#00877f">Description :</span> {{@$out_network->description}}  </li>			           
                @endif
                
                @if(@$out_network->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$out_network->pos_label}}</li>			           
                @endif
                
                <li><span style="color:#00877f">Time Period :</span> {{@$out_network->time_period}} ({{@$out_network->time_period_label}})</li>
                
                @if(@$out_network->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$out_network->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif
                
                @if(@$out_network->quantity_code)
                    <li><span style="color:#00877f">Quantity Code  :</span> {{@$out_network->quantity_code}}  </li>			           
                @endif

                @if(@$out_network->quantity)
                    <li><span style="color:#00877f">Quantity :</span> {{@$out_network->quantity}}  </li>			           
                @endif

                @foreach(@$out_network->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>				
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            @foreach(@$out_network->contact_details as $contact_details)
            <ul style="line-height:18px; margin-top:0px; list-style-type:none; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
				    <li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>
                @endforeach
            </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">						
                @foreach(@$out_network->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@endif
    @else
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
        </tr>
    @endif
</table>

<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:0px;">Plan : Noncovered</h4>

<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%;margin-top:-20px;margin-bottom:20px;">
    <tr style="border:1px solid #fff; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Level</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Network</td>           
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Information</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;text-align: center">Additional Info</td>			
    </tr>		 

	@if(@$result_array->plan->exclusions->noncovered)
    @foreach(@$result_array->plan->exclusions->noncovered as $noncovered)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$noncovered->level}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">{{@$noncovered->network}}</td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">

            <ul style="line-height:18px; list-style-type:none; margin-top:0px;margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$noncovered->type_label)
                    <li><span style="color:#00877f">Type  :</span> {{@$noncovered->type_label}}</li>			           
                @endif

                @if(@$noncovered->pos_label)
                    <li><span style="color:#00877f">POS :</span> {{@$noncovered->pos_label}}</li>			           
                @endif
                
                @if(@$noncovered->time_period)
                    <li><span style="color:#00877f">Time Period :</span> {{@$noncovered->time_period}} ({{@$noncovered->time_period_label}})</li>			
                @endif

                @if(@$noncovered->authorization_required)
                    <li><span style="color:#00877f">Authorization Required :</span> {{(@$noncovered->authorization_required == '1')?'Yes':'No'}}  </li>
                @endif

                @foreach(@$noncovered->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>
                @endforeach
            </ul>
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            @foreach(@$noncovered->contact_details as $contact_details)
                <ul style="line-height:18px; list-style-type:none;margin-top:0px; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			
                    @if(@$contact_details->last_name || @$contact_details->first_name)
                        <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>
                    @endif
                    
                    @if(@$contact_details->address->street_line_1)
                        <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                    @endif

                    @if(@$contact_details->address->city || @$contact_details->address->zip)
                        <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                    @endif

                    @if(@$contact_details->entity_code_label)
                        <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                    @endif

                    @if(@$contact_details->identification_code_label)
                        <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>
                    @endif

                    @foreach(@$contact_details->contacts as $contact_key => $contact_value)
    					<li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                    @endforeach
                </ul>									
            @endforeach			
        </td>

        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; margin-top:0px;list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">
                @foreach(@$noncovered->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>
    </tr>
    @endforeach
	@else
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
    </tr>
    @endif
</table>

<h4 style="color:#F07D08;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:0px;">Plan : Additional Insurance Policies</h4>

<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%;margin-top:-20px; margin-bottom: 20px;">
    <tr style="border:1px solid #fff; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Insurance Information</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Contact Details</td>
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Additional Info</td>			
    </tr>		 
    @if(@$result_array->plan->additional_insurance_policies)
    @foreach(@$result_array->plan->additional_insurance_policies as $additional_insurance_policies)
    <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; list-style-type:none; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$additional_insurance_policies->coverage_description)
                    <li><span style="color:#00877f">Coverage Description  :</span> {{@$additional_insurance_policies->coverage_description}}</li>			
                @endif

                @if(@$additional_insurance_policies->insurance_type_label)
                    <li><span style="color:#00877f">Insurance Type :</span> {{@$additional_insurance_policies->insurance_type_label}}</li>			           
                @endif

                @if(@$additional_insurance_policies->payer_type_label)
                    <li><span style="color:#00877f">Payer Type :</span> {{@$additional_insurance_policies->payer_type_label}} ({{@$additional_insurance_policies->payer_type}})</li>			           
                @endif

                @foreach(@$additional_insurance_policies->dates as $date_details)
					@if($date_details->date_type == 'plan_begin_begin')
					<?php 	$plan_label = 'Plan Begin';  ?>
					@elseif($date_details->date_type == 'plan_begin_end')
					<?php	$plan_label = 'Plan End';  ?>
					@else
					<?php	$plan_label = ucwords(str_replace('_',' ',@$date_details->date_type));  ?>
					@endif
                    <li><span style="color:#00877f">{{ $plan_label  }} :</span> {{ App\Http\Helpers\Helpers::dateFormat(@$date_details->date_value,'date') }} </li>	
                @endforeach		
            </ul>    	
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; "> 
            @foreach(@$additional_insurance_policies->contact_details as $contact_details)
            <ul style="line-height:18px; list-style-type:none; margin-left:-30px;color:#646464;font-size:11px;font-family:sans-serif;">			

                @if(@$contact_details->last_name || @$contact_details->first_name)
                    <li>{{@$contact_details->last_name}} @if(@$contact_details->first_name), {{@$contact_details->first_name}} @endif </li>			           
                @endif
                
                @if(@$contact_details->address->street_line_1)
                    <li>{{@$contact_details->address->street_line_1}} @if(@$contact_details->address->street_line_2), {{@$contact_details->address->street_line_2}} @endif</li>			           
                @endif
                
                @if(@$contact_details->address->city || @$contact_details->address->zip)
                    <li>{{@$contact_details->address->city}}, {{@$contact_details->address->state}} @if(@$contact_details->address->zip)- {{@$contact_details->address->zip}}@endif </li>			           
                @endif
                
                @if(@$contact_details->entity_code_label)
                    <li><span style="color:#00877f">Entity Code :</span> {{@$contact_details->entity_code_label}} </li>			           
                @endif

                @if(@$contact_details->identification_code_label)
                    <li><span style="color:#00877f">Identification Code :</span> {{@$contact_details->identification_code_label}}  </li>			           
                @endif

                @foreach(@$contact_details->contacts as $contact_key => $contact_value)
					<li><span style="color:#00877f">{{@ucwords($contact_value->contact_type)}}  :</span> {{ @strtolower($contact_value->contact_value) }} </li>	
                @endforeach
            </ul>									
            @endforeach			
        </td>
        <td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">
            <ul style="line-height:18px; list-style-type:circle; margin-left:-25px;color:#646464;font-size:11px;font-family:sans-serif;">						
                @foreach(@$additional_insurance_policies->comments as $comments)
                    <li style="text-transform: capitalize;margin-bottom: 5px;">{{$comments}}</li>
                @endforeach
            </ul>
        </td>        
    </tr>
    @endforeach
    @else
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
        </tr>
    @endif
    
</table>

<h4 style="color:#00877f;font-size:11px;font-family:sans-serif;margin-top:20px; margin-bottom:0px;">Services</h4>

<table class="table-responsive table-striped-view table" style="border:1px solid #e3e6e6; border-collapse:collapse; width:100%;margin-top:-20px; margin-bottom: 20px;">
    <tr style="border:1px solid #fff; border-collapse:collapse;">
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Type</td>        
        <td style="background-color: #00877f;border:1px solid #e3e6e6; border-collapse:collapse; vertical-align: top; padding:5px 10px 2px 10px; font-weight:400; color: #fff; text-align: left; font-size:11px;font-family:sans-serif;">Coverage Status</td>			
    </tr>	

	@if(@$result_array->services)
        @foreach(@$result_array->services as $services)
    		@if(@$services->coverage_status_label == 'Active Coverage')
    			<tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">        
    				<td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{@$services->type_label}}</td>
    				<td style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px; ">{{@$services->coverage_status_label}}</td>
    			</tr>
    		@endif
        @endforeach
	@else
        <tr style="border:1px solid #e3e6e6; border-collapse:collapse;vertical-align: top;color:#646464;font-size:11px;font-family:sans-serif;">
            <td colspan="6" style="border:1px solid #e3e6e6;border-collapse:collapse;vertical-align: top;text-align: center;color:#646464;font-size:11px;font-family:sans-serif;padding:5px 10px 2px 10px;text-transform: capitalize ">No Records Found</td>
        </tr>
    @endif
</table>