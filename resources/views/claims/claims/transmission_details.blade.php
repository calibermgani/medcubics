@extends('admin')
@section('toolbar')    
    <div class="row toolbar-header">
        <section class="content-header">
           <h1>
                <small class="toolbar-heading">
                <i class="fa font14 {{$heading_icon}}"></i> Claim Transmission
                    <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Details</span>
                </small>
            </h1>
            <ol class="breadcrumb">       
				<li><a href="{{ url('claims/transmission') }}"><i class="fa fa-reply" data-placement="bottom" data-toggle="tooltip" data-original-title="Back"></i></a></li>
                <!--li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom" data-toggle="tooltip" data-original-title="Print"></i></a></li-->
                <li><a href="#js-help-modal" data-url="" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
            </ol>
        </section>
    </div>
@stop
@section('practice')
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-info no-shadow">
            <div class="box-header with-border">
                <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">List</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div><!-- /.box-header -->
			
			<div class="box-body table-responsive">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-list-bg margin-t-10 text-center">
				<span class="font600 med-green">Trans. Type</span>&emsp;:&emsp;{{ $transmission->transmission_type}}&emsp;&emsp;
				<span class="font600 med-green">No. Of Claims</span>&emsp;:&emsp;{{ $transmission->total_claims}}&emsp;&emsp;
				<span class="font600 med-green">Billed Amt</span>&emsp;:&emsp;{{ $transmission->total_billed_amount }}&emsp;&emsp;
				<span class="font600 med-green">Trans. By</span>&emsp;:&emsp;@if(@$transmission->user->name != ""){{ @$transmission->user->name }} @else -Nil- @endif &emsp;&emsp;
				<span class="font600 med-green">Trans. On</span>&emsp;:&emsp;@if(@$transmission->user->name != ""){{ App\Http\Helpers\Helpers::dateFormat($transmission->created_at,'date') }} @else -Nil- @endif
			</div>
			
			<span class="claimdetail font600 form-cursor js-submitted-claim p-r-10 p-l-10 right-border orange-b-c" data-transmission-id="{{ $transmission->id }}"><i class="fa fa-tv font14"></i> Electronic Submit </span>
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box-body table-responsive">
                <div style="border: 1px solid #008E97;border-radius: 4px;">
                <div class="box-header med-bg-green no-padding" style="border-radius: 4px 4px 0px 0px;">
                    <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Claim #</h3>
                    </div>
                    <div class="col-lg-2 col-md-1 col-sm-1 hidden-xs" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Patient</h3>
                    </div>  

                    <div class="col-lg-1 col-md-3 col-sm-3 col-xs-4" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-4 med-white">Rendering</h3>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Billing</h3>
                    </div> 
                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Facility</h3>
                    </div>                    
                    <div class="col-lg-2 col-md-1 col-sm-1 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Billed To</h3>
                    </div> 
                    <div class="col-lg-1 col-md-1 col-sm-2 col-xs-3" style="border-right: 1px solid #fff;">
                        <h3 class="box-title padding-6-0 med-white">Billed Amt</h3>
                    </div> 
                    <div class="col-lg-2 col-md-1 col-sm-2 col-xs-3">
                        <h3 class="box-title padding-6-0 med-white">ICD</h3>
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body no-padding">

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-b-5"><!--  Left side Content Starts -->  
						<?php $count =1; ?>					
						@foreach(@$transmission->claim_transmission as $key => $transmission_details)
						<?php 
							$facility = $transmission_details->claims->facility_detail;
							$provider = $transmission_details->claims->rendering_provider;
							$patient = @$transmission_details->claims->patient; 
						?>
						@if($count % 2 == 0)
							<?php $class = "odd";$bg= "FFF";?>
						@else
							<?php $class = "even";$bg= "F3FFFE"; ?>
						@endif
						<?php $patient_name = App\Http\Helpers\Helpers::getNameformat(@$transmission_details->claims->patient->last_name,@$transmission_details->claims->patient->first_name,@$transmission_details->claims->patient->middle_name); ?> 
                        <div class="box collapsed-box" style="box-shadow:none;margin:0px;"><!--  Box Starts -->
                            <div class="box-header-view-white no-padding" style="background-color: #{{ $bg}}; color: #fff;border-bottom: 1px solid #CDF7FC;">
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2" style="border-right: 1px solid #fff;">
                                    <h3 class="box-title font12 font-normal">
										 <button class="btn btn-box-tool" data-widget="collapse"><i class="fa {{Config::get('cssconfigs.common.plus')}}"></i></button></h3>
									<span style="color: #868686;">{{$transmission_details->claims->claim_number}}</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2 margin-t-5" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">@include ('layouts/patient_hover')</span>
                                </div>
                                 <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 margin-t-5" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;"><a id="someelem{{hash('sha256','p_'.@$transmission_details->claims->rendering_provider->id.$key)}}" class="someelem" data-id="{{hash('sha256','p_'.@$transmission_details->claims->rendering_provider->id.$key)}}" href="javascript:void(0);"> {{str_limit(@$transmission_details->claims->rendering_provider->short_name,15,' ...')}}</a> 
                                        <?php @$provider->id = 'p_'.@$transmission_details->claims->rendering_provider->id.$key; ?> 
                                        @include ('layouts/provider_hover')</span>
                                </div>
								 <?php $provider = $transmission_details->claims->billing_provider; ?>
                                 <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 margin-t-5" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;"><a id="someelem{{hash('sha256','p_'.@$transmission_details->claims->billing_provider->id.$key)}}" class="someelem" data-id="{{hash('sha256','p_'.@$transmission_details->claims->billing_provider->id.$key)}}" href="javascript:void(0);"> {{str_limit(@$transmission_details->claims->billing_provider->short_name,15,' ...')}}</a> 
                                        <?php @$provider->id = 'p_'.@$transmission_details->claims->billing_provider->id.$key; ?> 
                                        @include ('layouts/provider_hover')</span>
                                </div>
                                 <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 margin-t-5" style="border-right: 1px solid #fff;">
								<span style="color: #868686;"><a id="someelem{{hash('sha256','f_'.@$transmission_details->claims->facility_detail->id.$key)}}" class="someelem" data-id="{{hash('sha256','f_'.@$transmission_details->claims->facility_detail->id.$key)}}" href="javascript:void(0);"> {{str_limit(@$transmission_details->claims->facility_detail->short_name,15,' ...')}}</a> 
                                        <?php @$facility->id = 'f_'.@$transmission_details->claims->facility_detail->id.$key; ?> 
                                        @include ('layouts/facility_hover')</span>
                                </div>
                                <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2 margin-t-5" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{{$transmission_details->insurance->insurance_name}}</span>
                                </div>

                                 <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 margin-t-5 text-right" style="border-right: 1px solid #fff;">
                                    <span style="color: #868686;">{!!App\Http\Helpers\Helpers::priceFormat($transmission_details->total_billed_amount)!!}</span>
                                </div>

                                 <div class="col-lg-2 col-md-1 col-sm-1 col-xs-2 margin-t-5"><span style="color: #868686;">{{str_replace(',',', ',$transmission_details->icd)}}</span></div>
                            </div><!-- /.box-header -->
                            <div class="box-body form-horizontal">
                                <div class="col-md-12 col-sm-12 col-xs-12 no-padding border-radius-4 yes-border border-b4f7f7">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                                        <span class="med-orange margin-l-10 font13 font600 padding-0-4 bg-white">CPT Details</span>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding m-b-m-10" >   
										<table class="popup-table-wo-border table margin-t-5 margin-b-10 no-sm-bottom">
											<thead>
												<tr>    
													<th>CPT</th> 
													<th>ICD Pointers</th>
													<th>Billed Amt</th>
												</tr>
											</thead>
											<tbody>

												@foreach(@$transmission_details->cpt_transmission as $trans_detail)
												<tr>
													<td>{{	@$trans_detail->cpt }}</td>
													<td>{{	@$trans_detail->icd_pointers }}</td>
													<td class="text-right">{{	@$trans_detail->billed_amount }}</td>
												</tr>
												@endforeach
											</tbody>
										</table>                         
                                    </div>
                                </div>
                            </div><!-- /.box Ends-->
                        </div>
						<?php $count++; ?>
                        @endforeach
                    </div>    
                </div>
            </div>
        </div><!-- /.box -->
        </div>
        </div>
    </div>
    </div>
@stop
@push('view.scripts')
	<script>
		$(document).on('click','.js-submitted-claim',function(){
			var id = $(this).attr('data-transmission-id');
			var url = api_site_url + '/claims/errorSubmission/'+id;
			$.ajax({
				url: url,
				type: 'get',
				success: function (data) {
				}
			});
		})
	</script>
@endpush