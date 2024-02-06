<!-- Modal PAyment details starts here -->
<div class="box box-view no-shadow no-border"><!--  Box Starts -->
    <div class="box-body form-horizontal">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="margin-top:-5px; margin-bottom: 10px; ">
            <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
				<h6>Billed : <span class="med-green">@if($detail->insurance_details != '') 
                {!!App\Http\Helpers\Helpers::getInsuranceName(@$detail->insurance_details->id)!!}@else <?php echo 'Self Pay';?>@endif</span></h6>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-12">
            <h6>Bill : <span class="med-orange">$ {{@$detail->total_charge}}</span>&emsp; Paid : <span class="med-orange">${{ @$detail->total_paid}}</span>&emsp; Bal: <span class="med-orange">${{ @$detail->balance_amt}}</span></h6>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border" style="margin-bottom: 15px;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
            <span class="bg-white med-orange" style="margin-left:10px; font-size: 13px; padding: 0px 4px;"> Claim Details</span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" >
                <table class="popup-table-wo-border table">                    
                    <tbody>
                        <tr>
                            <td class="font600">Rend Prov</td>
                            <td>{{@$detail->rendering_provider->provider_name}}</td>  
                            <td class="med-green font600">Status</td>
                            <td>{{@$detail->status}}</td>
						</tr>
						<tr>
                            <td class="font600">Bill Prov</td>
                            <td>{{@$claim->billing_provider->provider_name}}</td>
                            <td class="med-green font600">Claim Type</td>
                            <td>{{ App\Models\Payments\ClaimInfoV1::getPayerIdbilledToInsurance(@$claim->insurance_id)}}</td>
						</tr>
						<tr>
                            <td class="font600">Facility</td>
                            <td>{{@$detail->facility_detail->facility_name}}</td>
                            <td class="med-green font600">DOI</td>                           
                            @if(!empty($detail->doi) && $detail->doi != '0000-00-00 00:00:00')
								<?php $doi = date('m-d-Y',strtotime($detail->doi))?> 
                            @elseif(!empty($detail->claim_details->illness_box14) && $detail->claim_details->illness_box14 != '0000-00-00 00:00:00')
								<?php $doi = date('m-d-Y',strtotime($detail->claim_details->illness_box14))?> 
                            @else
								<?php $doi = '-'?> 
                            @endif
                            <td><span class="bg-date">{{@$doi}}</span></td>
						</tr>
						<tr>
                            <td class="font600">Ref Prov</td>
                            <td>{{@$claim->refering_provider->provider_name}}</td>  
                            <td class="med-green font600">Claim No</td>
                            <td>{{@$detail->claim_number}}</td>
						</tr>
						<tr>
                            <td class="font600">Auth #</td>
                            <td>43763456</td>
                            <td class="med-green font600">Submitted Dt</td>
                            <td><span class="bg-date">{{date('m-d-Y',strtotime($detail->submited_date))}}</span></td>
						</tr>
						<tr>
                            <td class="font600">Primary ICD</td>
                             <?php 
								if(!empty($detail->dosdetails[0]->cpt_icd_code)){
									$icd = explode(',',$detail->dosdetails[0]->cpt_icd_code);
								}
							?>
                            <td>{{@$icd[0]}}</td>  
                            <td class="med-green font600">Last Submitted Dt</td>
                            <td><span class="bg-date">{{date('m-d-Y',strtotime($detail->last_submited_date))}}</span></td>  
                        </tr>                                                                        
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive">
				<table class="popup-table-wo-border table" style="border-top:1px solid #E4FAFD; margin-bottom: 5px;">
					<thead>
						<tr>
							<th>CPT</th>
							<th>Billed</th>
							<th>Allowed</th>
							<th>Paid</th>
							<th>Co-Ins</th>
							<th>Co-Pay</th>
							<th>Deductible</th>
							<th>Adj</th>
							<th>Status</th>
						</tr>
					</thead>
                    <tbody>
                        @if(!empty($detail->dosdetails))
                            @foreach($detail->dosdetails as $dosdetails)
                            <tr>                                             
                                <td>{{@$dosdetails->cpt_code}}</td>
                                <td>${{@$dosdetails->charge}}</td>
                                <td>${{@$dosdetails->cpt_allowed_amt}}</td>
                                <td>${{@$dosdetails->paid_amt}}</td>
                                <td>${{@$dosdetails->co_ins}}</td>
                                <td>${{@$dosdetails->co_pay}}</td>
                                <td>${{@$dosdetails->deductable}}</td>                                        
                                <td>${{@$dosdetails->adjustment}}</td>
                                <?php if($dosdetails->status == "Pending") {
                                    $class = "c-denied";
                                }else if($dosdetails->status == "P.Paid"){
                                    $class = "m-ppaid";
                                }else{
                                    $class = "m-paid";
                                }?>
                                <td><span class="<?php echo $class;?>">{{@$dosdetails->status}}</span></td>
                            </tr>
                            @endforeach
                        @else
                         <tr>
                            <td>No records found</td>
                        </tr>
                        @endif
                    </tbody>
				</table>
			</div>
        </div>
        <div class="btn-group pull-right margin-t-m-10 bottom-space-10">
            <button type = "button" class = "btn-min btn-success" style="background: #fff; border:0px solid #ccc; color:#00877f;"data-toggle = "collapse" data-target = "#demo">View Transactions</button>
            <button type="button" class="btn-min btn-success" style="background: #fff; border-top:0px solid #ccc; border-bottom:0px solid #ccc; "><a href="#"> Edit Billing</a></button>
            <button type="button" class="btn-min btn-success" style="background: #fff; border-right:0px solid #ccc;  border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="{{ url('patients/1/payments/edit') }}" > Post Payments</a></button>
        </div>       
    </div>
</div><!-- /.box-body -->