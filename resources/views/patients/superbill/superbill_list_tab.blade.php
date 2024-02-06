<div class="active tab-pane" id="superbills_list">

    <div class="box-info no-shadow">
        <div class="box-body table-responsive" style="margin-top:30px;">
            
            <table id="example2" class="table table-bordered table-striped">	

                <thead>
                    <tr>
                        <th>DOS</th>
                        <th>Insurance</th>                                
                        <th>Provider</th>                               
                        <th>Billed Amt</th>
                        <th>Ins Bal</th>
                        <th>Pat Bal</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    @if(count($claims_list) > 0)
                    @foreach ($claims_list as $claims_list_arr)                   
                    <tr>
                        <td><a>{{ App\Http\Helpers\Helpers::dateFormat($claims_list_arr->date_of_service,'claimdate') }}</a></td>
                        <td>{!! @$insurance_arr->insurance_details->insurance_name !!}</td>
						<td>
							<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
								<a id="someelem{{hash('sha256',@$claims_list_arr->rendering_provider->id)}}" class="someelem" data-id="{{hash('sha256',@$claims_list_arr->rendering_provider->id)}}" href="javascript:void(0);"> {{ str_limit(@$claims_list_arr->rendering_provider->provider_name,25,'...') }} {{ @$claims_list_arr->rendering_provider->degrees->degree_name }}</a>
								<?php $provider = @$claims_list_arr->rendering_provider; ?>  
								@include ('layouts/provider_hover')
							</div>
						</td>						
                        <!--td>{!! @$claims_list_arr->provider_details->provider_name !!}</td-->                                
                        <td>{!! @$claims_list_arr->total_charge !!}</td>
                        <td>{!! @$claims_list_arr->insurance_due !!}</td>
                        <td>{!! @$claims_list_arr->patient_due !!}</td>
                        <td>{!! @$claims_list_arr->balance_amt !!}</td>
                        <td><span class="@if(@$claims_list_arr->status == 'Ready') ready-to-submit @elseif(@$claims_list_arr->status == 'Partial Paid') c-ppaid @else {{ @$claims_list_arr->status }} @endif">{!! @$claims_list_arr->status !!}</span></td>
                    </tr>
                    @endforeach
                    @endif

                </tbody>
            </table>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!-- /.tab-pane -->