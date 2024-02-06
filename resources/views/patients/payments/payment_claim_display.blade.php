<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive claim-transaction-scroll">
    <table class="popup-table-wo-border table table-responsive">                    
        <thead>
            <tr>   
            <th style="width:2%"></th>
            <th>DOS</th>
            <th>Claim No</th>                                
            <th>Billed To</th>                               
            <th class="text-right">Billed Amt</th>
            <th class="text-right">Paid</th>
            <th class="text-right">Adj</th>
            <th class="text-right">Balance</th>
            <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
				$max = Config::get("siteconfigs.payment.max_claim_choose_onsearch"); 
                $sel_claim =  @$claim_id;
            ?>
        @if(!empty($claims_lists))                                    
            @foreach($claims_lists as $claim)                                
            <tr>                                     
                <?php 
					$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($claim->id,'encode');
					$disabled = '';
					if(empty($claim->insurance_details)) {
					$insurance_data = "Patient";
					//$disabled  = "disabled = disabled" ; 
					$disabled  = "" ; 
					} else  {
					$insurance_data = App\Http\Helpers\Helpers::getInsuranceName(@$claim->insurance_details->id);
					}                         
					$default_checked = ($sel_claim != '' && $sel_claim == $claim_id)? 'checked':'';
                ?>                                        
                <td><a href="javascript:void(0)">
                <input id = "{{$claim_id}}" data-insurance = "{{@$insurance_data}}" type="checkbox" class="js-sel-claim" name = "insurance_checkbox" data-max= "{{ $max}}" data-claim = "js-bal-{{$claim->claim_number}}" {{$disabled}} {{$default_checked}}></a></td>
                <?php $url = url('patients/popuppayment/'.$claim->id) ?>                              
                <td> <a>{{App\Http\Helpers\Helpers::dateFormat(@$claim->date_of_service, 'dob')}}<label for="{{$claim_id}}" class="no-bottom">&nbsp;</label></a></td>     
                <td>{{@$claim->claim_number}}</td>               
                <td>{!!$insurance_data!!}</td>                              
                <td class="text-right">{{@$claim->total_charge}}</td>
                {!!Form::hidden('patient_paid_amt', $claim->patient_paid,['class' => 'js-bal-'.$claim->claim_number])!!}
                <td class="text-right">{{@$claim->total_paid}}</td> 
                <td class="text-right"> {{App\Http\Helpers\Helpers::getCalculatedAdjustment(@$claim->total_adjusted, @$claim->total_withheld)}}</td>                   
                <td class="text-right" id = "js-bal-{{$claim->claim_number}}">{{@($claim->balance_amt)}}</td>
                <td><span class="@if(@$claim->status == 'Ready') ready-to-submit @elseif(@$claim->status == 'Partial Paid') c-ppaid @else {{ @$claim->status }} @endif">{{@$claim->status}}</span></td>
            </tr>
            @endforeach
        @else
           <tr><td colspan="9" class="text-center"><span class="med-gray-dark">No Claims Available</span> </td></tr>
        @endif                       
        </tbody>
    </table>                  
</div>   