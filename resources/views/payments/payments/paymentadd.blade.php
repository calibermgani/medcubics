@extends('admin')
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
           <small class="toolbar-heading"><i class="fa fa-money font14"></i> Payments <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Create Payment</span></small>
        </h1> 
         <ol class="breadcrumb">
            <li><a href="{{ url('payments') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>           
        </ol>      
    </section>
</div>
@stop
@section('practice-info')
<?php
    $check_date = (!empty($post_val->check_date) && $post_val->check_date != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat(@$post_val->check_date, 'date'):'';
	$check_no = ($post_val->pmt_mode != 'Credit' && isset($post_val->check_no) ) ? @$post_val->check_no : @$post_val->card_no;
    $payment_type = @$post_val->pmt_type; 
    $insurance_id = @$post_val->insurance_id;  
    $payment_detail_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$post_val->id, 'encode');
?>
    {!! Form::open(['url'=>'payments/search', 'id' => 'js-next-searchform']) !!}
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="margin-top:-30px;"> 
		@include ('payments/payments/append_payment', ['payment_detail_id' => 'appendpayment'])   
		{!!Form::hidden('insurance_id', @$insurance_id, ['id' => 'js-insurance-list'])!!} 
		{!!Form::hidden('payment_mode', @$post_val->pmt_mode)!!}
		{!!Form::hidden('check_no', @$check_no)!!}
		{!!Form::hidden('check_date', $check_date)!!} 
		{!!Form::hidden('payment_type', $payment_type)!!}
		{!!Form::hidden('insurance_unapplied_amt', @$post_val->balance)!!} 
		{!!Form::hidden('unapplied_amt', @$post_val->balance)!!}   
		{!!Form::hidden('payment_detail_id', $payment_detail_id)!!}
		{!!Form::hidden('payment_amt', $post_val->pmt_amt)!!}   
    </div>
    {!!Form::close()!!}

@stop
@section('practice')
	<?php  $label = App\Http\Helpers\Helpers::GetLabelFields($post_val->pmt_mode);  ?> 
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 js-append-payment form-horizontal no-padding margin-t-8">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-m-20">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-green bg-white">
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 padding-10 ">        
					<span class = "js-check-remaining"></span>
					 <div class="form-group-billing">
						{!! Form::label('type', 'Billed To', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">                                     
							{!! Form::select('insurance_id',$insurance_lists,@$insurance_id,['class'=>'select2 form-control', 'disabled' => 'disabled']) !!}                
						</div>                                 
					</div>    
		  
				   <div class="form-group-billing ">
						{!! Form::label('amt', 'Mode', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10 select2-white-popup">                                     
							{!! Form::select('payment_mode', ['' => '--', 'Check' => 'Check','EFT' => 'EFT', 'Credit' => 'CC'],@$post_val->pmt_mode,['class'=>'select2 form-control', 'disabled' => 'disabled']) !!}
						</div>
					</div>
					 <div class="form-group-billing">                               
						{!! Form::label('amt', 'Amount', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green font600']) !!}
						<div class="col-lg-8 col-md-8 col-sm-8 col-xs-10">                                     
							{!! Form::text('payment_amt',@$post_val->pmt_amt,['class'=>'form-control allownumericwithdecimal input-sm-header-billing', 'readonly' => 'readonly']) !!}
						</div> 
					</div> 
				</div>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 padding-10 form-horizontal tab-l-b-1 border-green">                                                                                                             
					<div class="form-group-billing">                               
						{!! Form::label('Chk No', $label['label_no'], ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
						<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                     
							{!! Form::text('check_no',$check_no,['maxlength'=>'25','class'=>'form-control input-sm-header-billing','readonly' => 'readonly']) !!}
						</div>                               
					</div>
					<div class="form-group-billing">                               
						{!! Form::label('Check Date', $label['label_date'], ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
						<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">  
							<i class="fa fa-calendar-o form-icon-billing" onclick = "iconclick('check_date')"></i>
							{!! Form::text('check_date',@$check_date,['class'=>'form-control input-sm-header-billing','readonly' => 'readonly','maxlength' => 10]) !!}
						</div>                                
					</div>                          

					<div class="form-group-billing">
						{!! Form::label('Unapplied', 'Unapplied', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600']) !!}
						<div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                    
							{!! Form::text('unapplied_amt',@$post_val->balance,['readonly' => 'readonly','class'=>'form-control input-sm-header-billing']) !!}
						</div>                                   
					</div>
				</div>
			 </div>
		</div>
	</div>     
	
	<!-- Transaction details block starts -->	
    <div class="box-body form-horizontal margin-t-8">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border tabs-border margin-b-10 margin-t-8">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                <span class="bg-white med-orange padding-0-4 margin-l-10 font600"> Transaction Details</span>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive mobile-scroll margin-t-10">
                <table class="popup-table-wo-border table margin-b-5 mobile-width"> 
                    <thead>
                        <tr>  
							<th></th>
                            <th>DOS</th>
                            <th>Claim No</th>
                            <th>Patient Name</th>
                            <th class="text-right">Billed</th>
                            <th class="text-right">Allowed</th>
                            <th class="text-right">Ded</th>
                            <th class="text-right">Co-Pay</th>
                            <th class="text-right">Co-Ins</th>
                            <th class="text-right">With Held</th>
                            <th class="text-right">Adj</th>
                            <th class="text-right">Paid</th>                            
                        </tr>
                    </thead>
                    <tbody> 
						<?php $i = 0; $j = 1; $payment = 1; ?>
                        @if(!empty($post_val->payment_claim_txns))    
                        @foreach($post_val->payment_claim_txns as $payment_claim_detail) 
                        @if(!empty($payment_claim_detail->claim))        
							<?php	
								$finDet = @$payment_claim_detail->fin;
								$claim_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($payment_claim_detail->claim->id, 'encode'); 
								$url = url('patients/payment/popuppayment/'.$claim_id); 
								$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$payment_claim_detail->claim->patient->id, 'encode'); 
								$i +=1;
								$patient_name = App\Http\Helpers\Helpers::getNameformat(@$payment_claim_detail->claim->patient->last_name, @$payment_claim_detail->claim->patient->first_name, @$payment_claim_detail->claim->patient->middle_name);
							
								$style = $dmStyle = "";
								$cpttx = @$payment_claim_detail->cpts;
								if(count($cpttx) < 1){
									$toggler = "toggle-minus";
									$style = "style = display:none";									
								} elseif( count($cpttx) > 0) {
									$toggler = "toggle-plus";
									$style =  "style = display:none";
								} else{
									$toggler = "";
								}								
							?>
							<tr class="med-l-green-bg"  >																		
								<td> 
									<a href="#" class="toggler font600 {{ $toggler }}" data-prod-cat="{{$j}}"><span style="position: absolute"> &emsp;</span>  </a>
								</td>
								<td>	
									<a href="#" claim_number = "{{$payment_claim_detail->claim->claim_number}}"data-toggle="modal" data-target="#js-model-popup-payment" data-url="{{$url}}" class="claimbilling">{{@date('m/d/Y',strtotime($payment_claim_detail->claim->date_of_service))}}</a>
								</td>
								<td>{{@$payment_claim_detail->claim->claim_number}}</td>
								<td>
									<span>                               
										<a href="{{ url('patients/'.@$patient_id.'/ledger') }}" target="_blank"> <span class="someelem" data-id="{{@$payment_claim_detail->claim->patient->id}}" id="someelem{{@$payment_claim_detail->claim->patient->id}}">@if(@$payment_claim_detail->claim->patient->title){{ @$payment_claim_detail->claim->patient->title }}. @endif{{ str_limit(@$patient_name,25,'...') }}</span></a> 
									</span>
									<div class="on-hover-content js-tooltip_{{$payment_claim_detail->claim->patient->id}}" style="display:none;">
										<span class="med-orange font600">@if(@$payment_claim_detail->claim->patient->title){{ @$payment_claim_detail->claim->patient->title }}. @endif{{ @$patient_name }}</span> 
										<p class="no-bottom hover-color"><span class="font600">Acc No :</span> {{ @$payment_claim_detail->claim->patient->account_no }}
											<br>
											@if(@$payment_claim_detail->claim->patient->dob !='' && @$payment_claim_detail->claim->patient->dob != "0000-00-00" && @$payment_claim_detail->claim->patient->dob != "1901-01-01" )
											<span class="font600">DOB :</span>{{ App\Http\Helpers\Helpers::dateFormat(@$payment_claim_detail->claim->patient->dob,'claimdate') }}
											<span class="font600">Age :</span> {{ App\Http\Helpers\Helpers::dob_age(@$payment_claim_detail->claim->patient->dob) }}
											@endif
											<span class="font600">Gender :</span> {{ $payment_claim_detail->claim->patient->gender }}<br>
											<span class="font600">Ins :</span> {{ App\Models\Patients\PatientInsurance::CheckAndReturnInsuranceName(@$payment_claim_detail->claim->patient->id)}}<br>
											<span class="font600">Address :</span> {{ $payment_claim_detail->claim->patient->address1 }}<br>
											{{ $payment_claim_detail->claim->patient->city}}, {{ $payment_claim_detail->claim->patient->state}}, {{ $payment_claim_detail->claim->patient->zip5}}-{{ $payment_claim_detail->claim->patient->zip4}}<br>
											@if(@$payment_claim_detail->claim->patient->phone)<span class="font600">Home Phone :</span>{{$payment_claim_detail->claim->patient->phone}} <br>@endif
											@if(@$payment_claim_detail->claim->patient->work_phone)<span class="font600">Work Phone :</span> {{$payment_claim_detail->claim->patient->work_phone}}@endif
										</p>
									</div>
								</td>																
								<td class="text-right">{{@$payment_claim_detail->claim->total_charge}}</td>							
								<td class="text-right">{{@$payment_claim_detail->total_allowed}}</td>							
								<td class="text-right">{{@$payment_claim_detail->total_deduction}}</td>						
								<td class="text-right">{{@$payment_claim_detail->total_copay}}</td>
								<td class="text-right">{{@$payment_claim_detail->total_coins}}</td>							
								<td class="text-right">{{@$payment_claim_detail->total_withheld}}</td>
								<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_claim_detail->total_writeoff)!!}</td>
								<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$payment_claim_detail->total_paid)!!}</td>
							</tr>
							
							<!-- CPT Block Start -->
							@foreach($payment_claim_detail->cpts as $cpts)
							<tr class="cat{{$j}} med-l-red-bg" {{$style}}>
								<td></td>
								<td>&nbsp; {{@date('m/d/Y',strtotime($cpts->claim->date_of_service))}}</td>
								<td>CPT: {{@$cpts->claimcpt->cpt_code}}</td>
								<td></td>
								<td class="text-right">{{@$cpts->claimcpt->charge}}</td>
								<td class="text-right">{{@$cpts->allowed}}</td>
								<td class="text-right">{{@$cpts->deduction}}</td>
								<td class="text-right">{{@$cpts->copay}}</td>
								<td class="text-right">{{@$cpts->coins}}</td>
								<td class="text-right">{{@$cpts->withheld}}</td>
								<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$cpts->writeoff)!!}</td>
								<td class="text-right">{!!App\Http\Helpers\Helpers::priceFormat(@$cpts->paid)!!}</td>
								
							</tr>
							@endforeach
							<!-- CPT Block End -->
							<?php $j++;?>
                        @endif
                        @endforeach
                        @endif
						
						@if($i < 1) 
							<tr><td colspan="12" class="text-center"><span class="med-gray-dark">No payments has been done</span> </td></tr>
						@endif
                    </tbody>
                </table>                    
            </div>
        </div>
	</div>	
	<!-- Transaction details block ends -->
	
	
	<div id="choose_claims" class="modal fade in">
		<div class="modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"> Posting</h4>
				</div>
				<div class="modal-body no-padding" >
				</div><!-- /.box Ends Contact Details-->
			</div>
		</div><!-- /.modal-content -->
	</div>   
	
	<!-- Claim transaction details popup data starts here -->
	<div id="js-model-popup-payment" class="modal fade in">
		<div class="modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close hidden-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"> Claim No : <span class = "js-replace"></h4>
				</div>
				<div class="modal-body no-padding" >

				</div>
			</div>
		</div>
	</div>	
	
	<script type="text/javascript">
		// To handle browser back button. click on back redirect to Payments page.
		window.addEventListener('popstate', function(event) {
			//window.location = "{{url('payments')}}";
		}, false);
	</script>
@stop