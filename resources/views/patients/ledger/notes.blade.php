<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Notes Starts -->
    <div class="box box-view-border no-shadow margin-t-m-20 no-border-radius no-border">
        <div class="box-header-view-white no-border-radius">
            <i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i><strong class="med-darkgray font13"> Notes </strong>                   
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="chat @if($patients->notes)ledger-scroll @endif">
                <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                    @if(count($patients->notes)>0)

                    @if($patients->alert_notes != 0 )
                   <!--  <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                        <p class="med-orange js_note_head font14 font600 margin-b-5">Alert Notes:</p>
                        @foreach($patients->notes as $note)
                        <?php $alert_notes = $note->patient_notes_type == 'alert_notes'; ?>
                        @if($note->patient_notes_type == 'alert_notes')
                        <div class="box-comment margin-t-5">
                            <h5 class="no-padding no-margin">
                                <span class=" med-green js_notes_title">{{ $note->title }}</span> 
                                <span class="pull-right font12"><span class="med-orange">{{ App\Http\Helpers\Helpers::dateFormat($note->updated_at,'date')}}</span> | <span class="med-green">{{ App\Http\Helpers\Helpers::shortname($note->created_by) }}</span></span>
                            </h5> 
                            <p class="med-gray-dark margin-t-5 p-b-8  border-bottom-f0f0f0">{{ $note->content }}</p>
                        </div>
                        @endif
                        @endforeach	
                    </div> -->
                    @endif


                    @if($patients->patient_notes != 0 )
                    <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                        <p class="med-orange font14 font600 margin-b-5">Patient Notes :</p>
						<?php $createdArr = []; ?>
                        @foreach($patients->notes as $note)
                        @if($note->patient_notes_type == 'patient_notes')
							<?php 
								if(!isset($createdArr[$note->created_by])) {
									$createdArr[$note->created_by] = App\Http\Helpers\Helpers::shortname($note->created_by);
								}
								$created_user = $createdArr[$note->created_by];
							?>
                        <div class="box-comment margin-t-5">
                            <h5 class="no-padding no-margin">
                                <span class=" med-green">{{ $note->title }}</span> 
                                <span class="pull-right font12"><span class="med-orange">
									@if(!empty($note->updated_at) && $note->updated_at != '0000-00-00 00:00:00')
										{{ App\Http\Helpers\Helpers::dateFormat($note->updated_at,'datetime')}}
									@else
										{{ App\Http\Helpers\Helpers::dateFormat($note->created_at,'datetime')}}
									@endif
								</span> | <span class="med-green">{{ $created_user }}
                                    </span></span>
                            </h5> 
                            <p class="med-gray-dark margin-t-5 p-b-8  border-bottom-f0f0f0"> {{ $note->content }}</p>		
                        </div>	
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($patients->claim_notes != 0 )
                    <div class="col-lg-12 col-md-12 col-sm-12 no-padding">
                        <p class="med-orange font14 font600 margin-b-5">Claim Notes :</p>
                        <?php
							$claim_number = ''; 
							$createdUser = [];
						 ?>
                        @foreach($patients->notes as $note)
							<?php
								if(!isset($createdUser[$note->created_by])) {
									$createdUser[$note->created_by] = App\Http\Helpers\Helpers::shortname($note->created_by);
								}
								$createdBy = $createdUser[$note->created_by];
							?>
                            @if($note->patient_notes_type == 'claim_notes')										
                                @if(!empty($note->claims) &&($claim_number != $note->claims->claim_number ) )	
                                     <?php
										$claim_number = $note->claims->claim_number;
									 ?>
                                @endif                  
                                <div class="box-comment margin-t-5">
                                    <h5 class="no-padding no-margin">
                                        <span class=" med-green"> Claim No :  {{ $claim_number }}</span>		
                                        <span class="pull-right font12"><span class="med-orange">
										@if(!empty($note->updated_at) && $note->updated_at != '0000-00-00 00:00:00')
											{{ App\Http\Helpers\Helpers::dateFormat($note->updated_at,'datetime')}}
										@else
											{{ App\Http\Helpers\Helpers::dateFormat($note->created_at,'datetime')}}
										@endif
										</span> | <span class="med-green">{{ $createdBy }}</span></span>
                                    </h5>  
                                    <p class="med-gray-dark margin-t-5 p-b-8  border-bottom-f0f0f0">
                                        @if(!empty($note->claims))
                                        <span class="font600 med-darkgray">DOS : {{ App\Http\Helpers\Helpers::dateFormat(@$note->claims->date_of_service) }} - </span>
                                        @endif
                                        {!! html_entity_decode($note->content) !!}</p>
                                </div>
							@elseif($note->patient_notes_type == 'claim_denial_notes')
								@if(!empty($note->claims) &&($claim_number != $note->claims->claim_number ) )	
                                     <?php
										$claim_number = $note->claims->claim_number;
									 ?>
                                @endif
								 <div class="box-comment margin-t-5">
									<h5 class="no-padding no-margin">
										<span class=" med-green"> Claim No :  {{ $claim_number }}</span>		
										<span class="pull-right font12"><span class="med-orange">		
										@if(!empty($note->updated_at) && $note->updated_at != '0000-00-00 00:00:00')
											{{ App\Http\Helpers\Helpers::dateFormat($note->updated_at,'datetime')}}
										@else
											{{ App\Http\Helpers\Helpers::dateFormat($note->created_at,'datetime')}}
										@endif
										</span> | <span class="med-green">{{ $createdBy }}</span></span>
									</h5>  
								</DIV>
								<?php $denial_notes_arr = App\Models\Patients\Patient::getARDenialNotes(@$note->content); ?>
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
										<p class="no-bottom margin-t-5"><span class="med-green font600">Denial Date :</span> <span class="">&nbsp; {{App\Http\Helpers\Helpers::dateFormat(@$denial_notes_arr['denial_date'],'date')}}</span> </p>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
										<p class="no-bottom margin-t-5"><span class="med-green font600">Billed To :</span> <span class="">&nbsp;{{@$denial_notes_arr['denial_insurance']}}</span> </p>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
										<p class="no-bottom margin-t-5"><span class="med-green font600">Check No :</span> <span class="">&nbsp;{{@$denial_notes_arr['check_no']}}</span> </p>
									</div>
									@if($denial_notes_arr['reference'] != '')
									<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3 p-l-0">
										<p class="no-bottom margin-t-5"><span class="med-green font600">Reference :</span> <span class="">&nbsp;{{@$denial_notes_arr['reference']}}</span> </p>
									</div>
									@endif
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">					
										@foreach($denial_notes_arr['denial_code_result'] as $denial_code_result_key=>$denial_code_result_val)
										<p class="margin-b-5">{{@$denial_code_result_val}}</p>
										@endforeach
									</div>
								</div>							
                            @endif
                        @endforeach	
                    </div>
                    @endif
                    @else
                    <p class="text-center med-gray-dark">{{ trans("common.validation.not_found_msg") }}</p> 
                    @endif
                </div>
            </div>
        </div>
    </div>
</div><!-- Notes Ends -->