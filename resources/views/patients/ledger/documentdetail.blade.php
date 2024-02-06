<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- Documents  & Appointments, VOB, Auth Starts -->
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 p-r-0"><!-- Document Col Starts -->
        <div class="box box-view-border no-shadow margin-t-m-20 no-border-radius no-b-t p-b-4"><!-- Document Box Details Starts -->
            <div class="box-header-view-white no-border-radius">
                <i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i><strong class="med-darkgray font13"> Documents </strong>                                 
            </div><!-- /.box-header -->
            <div class="box-body chat ledger-scroll pr-l-5 p-r-5"><!-- Documents Box Body Starts -->
                @if(count($patients->documents)>0)
                @foreach($patients->documents as $document)
                <?php 
					$count = 1;
					$document->type_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$document->type_id,'encode');
					$doc_category =  @$document->document_categories->category_value; 
				?>
                @if($count == 1)
                <hr class="margin-t-5 margin-b-4">
                @endif
                <p class="no-bottom"><strong class="med-green">{{ @$doc_category }}&nbsp;</strong> <span class="@if(strlen(@$doc_category) <= 30) pull-right font12 pr-r-5 @else <br/> pull-right font12 pr-r-5 margin-l-10 margin-t-2 @endif ">{{ App\Http\Helpers\Helpers::dateFormat(@$document->created_at,'date') }}</span></p>
                                
                <p class="text-muted pr-r-5">
                    {{ @$document->title }}<span class="pull-right"><a href="{{url('patients/'.@$document->type_id.'/document/get/'.@$document->document_type.'/'.@$document->filename)}}" target="_blank">View</a></span>
                </p>
                
                @endforeach
                @else
                
                <div class="center-area">
                    <div class="centered med-gray-dark">
                        {{ trans("common.validation.not_found_msg") }}
                    </div>
                </div>
                @endif

            </div><!--  Documents box-body Ends-->
        </div><!-- Documents box Ends --><!-- Document Details Starts -->
    </div>

    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 p-l-0"><!-- Apt VOB Auth Starts -->
        <div class="box box-view-border no-shadow margin-t-m-20 no-border-radius no-b-l no-b-t"><!-- Apt Box Starts -->
            <div class="box-header-view-white no-border-radius">
                <i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i> <strong class="med-darkgray font13"> Appointments</strong>
                <!--div class="box-tools pull-right">
                    <button class="btn btn-box-tool"><i class="fa fa-minus"></i></button>
                </div-->
            </div><!-- /.box-header -->
            <div class="box-body chat ledger-apts p-r-10"><!-- Apt Box Body Starts -->
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive">
                    <table class="popup-table table margin-b-5 no-border">	
                        <thead>
                            <tr style="border-bottom: 2px solid #96dcd8">
                                <th class="med-green font600" style="background: #fff; border-bottom: 2px solid #96dcd8">Date</th>
                                <th class="med-green font600" style="background: #fff;">Time</th>
                                <th class="med-green font600" style="background: #fff;">Provider</th>
                                <th class="med-green font600" style="background: #fff;">Facility</th>
                                <th class="med-green font600" style="background: #fff;">Reason for Visit</th>
                                <th class="med-green font600" style="background: #fff;">Status</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($patients->appointment)>0)
                            @foreach($patients->appointment as $appointment)
                            <?php 
								$get_time =explode("-",@$appointment->appointment_time); 
								$start_time = strtoupper($get_time[0]); 
								$provider = @$appointment->provider->provider_name." ".@$appointment->provider->degrees->degree_name; 
							?>
                            <tr>                                
                                <td>{{ App\Http\Helpers\Helpers::dateFormat(@$appointment->scheduled_on ,'date') }}</td>
                                <td>{{@$start_time }}</td>
                                <td>{{@$provider }}</td>
                                <td>{{@$appointment->facility->facility_name }}</td>
                                <td>{{@$appointment->reasonforvisit->reason }}</td>
                                <td><span class="{{ $appointment->status }}">{{@$appointment->status }}</span></td>
                            </tr>
                            @endforeach
                            @else
                            <tr><td colspan="6" class="text-center"><p class="margin-t-10 font13 med-gray-dark">{{ trans("common.validation.not_found_msg") }}</p></td></tr>
                            @endif
                        </tbody>
                    </table>    
                </div>                                                            
            </div><!-- Apt box-body Ends -->
        </div><!-- Apt box Ends -->


        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding"><!-- VOB Col starts -->
            <div class="box box-view-border no-shadow margin-t-m-20 no-border-radius no-b-l no-b-t p-b-5"><!-- VOB Box starts -->
                <div class="box-header-view-white no-border-radius">
                    <i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i> <strong class="med-darkgray font13">Templates</strong> 
                </div><!-- /.box-header -->
                <div class="box-body table-responsive chat ledger-apts margin-t-m-5 p-r-10"><!-- VOB Box Body starts -->
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive">
                        <table class="popup-table table ">	
                            <thead>
                                <tr style="border-bottom: 2px solid #96dcd8">
									<th class="med-green font600" style="background: #fff;">Date</th>
									<th class="med-green font600" style="background: #fff;">Category</th>
									<th class="med-green font600" style="background: #fff;">Letter Type</th>
                                </tr>
                            </thead>
                            <tbody>
							@if(count($patients->correspondence_details)>0)
								@foreach($patients->correspondence_details as $correspondence)
								@if(@$correspondence->template_detail !=null)
								<tr>                                
									<td>{{ App\Http\Helpers\Helpers::dateFormat(@$correspondence->created_at ,'date') }}</td>
									<td>{{@$correspondence->template_detail->templatetype->templatetypes }}</td>
									<td>{{@$correspondence->template_detail->name }}</td>
								</tr>
								@endif
								@endforeach
                            @else
								<tr><td colspan="6" class="text-center"><p class="margin-t-10 font13 med-gray-dark">{{ trans("common.validation.not_found_msg") }}</p></td></tr>
                            @endif
                            </tbody>
                        </table>    
                    </div>                                                            
                </div><!-- VOB box-body Ends-->
            </div><!-- VOB box Ends -->
        </div><!-- VOB COl Ends -->

        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding"><!-- Auth COL Starts -->
            <div class="box box-view-border no-shadow margin-t-m-20 no-border-radius no-b-l no-b-t p-b-5"><!-- Auth Box Starts -->
                <div class="box-header-view-white no-border-radius">
                    <i class="livicon" data-color="#f07d08" data-name="responsive-menu"></i> <strong class="med-darkgray font13">Authorization</strong>                      
                </div><!-- /.box-header -->
                <div class="box-body chat ledger-apts margin-t-m-5 p-r-10 table-responsive">                                        
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding table-responsive">
                        <table id="" class="popup-table table">	
                            <thead>
                                <tr style="border-bottom: 2px solid #96dcd8">
                                    <th class="med-green font600" style="background: #fff;">Auth No</th>                                               
                                    <th class="td-c-60 med-green font600" style="background: #fff;">Insurance</th>
                                    <th class="med-green font600" style="background: #fff;">Start Date</th>                                               
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($patients->authorization_details)>0)
                                @foreach($patients->authorization_details as $auth_dtl)
                                <?php 
									$get_time =explode("-",@$appointment->appointment_time); 
									$start_time = strtoupper(@$get_time[0]); 
									$provider = @$appointment->provider->provider_name." ".@$appointment->provider->degrees->degree_name; 
								?>
                                <tr>                                                
                                    <td>{{ @$auth_dtl->authorization_no }}</td>                                                
                                    <td>{{ str_limit( @$auth_dtl->insurance_details->insurance_name, 20, '..') }}</td>
                                    <td>@if(@$auth_dtl->start_date !='' && @$auth_dtl->start_date !='0000-00-00'){{ App\Http\Helpers\Helpers::dateFormat(@$auth_dtl->start_date,'date') }} @else - @endif </td>
                                </tr>

                                @endforeach
                                @else
                                <tr><td colspan="3" class="text-center"><p class="margin-t-10 font13 med-gray-dark">{{ trans("common.validation.not_found_msg") }}</p></td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>                                                            
                </div><!-- Auth /.box-body Ends -->
            </div><!--Auth  /.box Ends -->
        </div><!-- Auth Col Ends -->
    </div><!-- Apt VOB Auth Ends  -->
</div><!-- Documents  & Appointments, VOB, Auth Ends -->