@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
<?php $facility->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($facility->id,'encode'); ?>

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Managed Care</span></small>
        </h1>
        <ol class="breadcrumb">
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            @if(count($managecare)>0)
                @include('layouts.practice_module_export', ['url' => 'facility/api/facilitymanagecarereports/'.$facility->id.'/'])
            @endif
            <li><a href="{{url('facility/'.$facility->id)}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/facility')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('practice/facility/tabs')  
@stop

@section('practice')
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			@if(Session::get('message')!== null) 
			<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
			@endif
		</div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20"><!-- col-12 starts -->
            <div class="box box-info no-shadow ">
                <div class="box-header margin-b-10">
                    <i class="fa fa-bars"></i><h3 class="box-title">Managed Care List</h3>
                    <div class="box-tools pull-right margin-t-2">
                        @if($checkpermission->check_url_permission('facility/{id}/facilitymanagecare/create') == 1)
                        <a href="{{url('facility/'.$facility->id.'/facilitymanagecare/create')}}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Managed Care</a>
                        @endif	
                    </div>
                </div><!-- /.box-header -->
                <div class="box-body"><!-- Box Body Starts -->
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Insurance</th>
                                    <th>Provider</th>
                                    <th>Credential</th>
                                    <th>Entity Type</th>
                                    <th>Effective Date</th>
                                    <th>Termination Date</th>
                                    <th>Fee Schedule</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($managecare as $managecare)
								<?php $managecare->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($managecare->id,'encode'); ?>
                                <tr data-url="{{ url('facility/'.$facility->id.'/facilitymanagecare/'.$managecare->id) }}" class="js-table-click clsCursor">
                                    <td>@if($managecare->insurance){{ $managecare->insurance->insurance_name }}@endif</td>
                                    <td>
										<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
											<a id="someelem{{hash('sha256',@$managecare->provider->id)}}" class="someelem" data-id="{{hash('sha256',@$managecare->provider->id)}}" href="javascript:void(0);">{{ str_limit(@$managecare->provider->provider_name,25,'...') }} {{ str_limit(@$managecare->provider->degrees->degree_name,5,'...') }}</a>
											<?php $provider = $managecare->provider; ?>  
											@include ('layouts/provider_hover')
										</div>	
									</td>
                                    <td>{{ $managecare->enrollment }}</td>
                                    <td>{{ $managecare->entitytype }}</td>
                                    <td>{{ ($managecare->effectivedate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managecare->effectivedate,'date'): '' }}</td>
                                    <td>{{ ($managecare->terminationdate != '0000-00-00')? App\Http\Helpers\Helpers::dateFormat($managecare->terminationdate,'date') : '' }}</td>
                                    <td>{{ $managecare->feeschedule }}</td>                   
                                </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div><!-- /.box-body ends -->
            </div><!-- /.box -->
        </div><!-- Col-12 starts -->   
<!--End-->
@stop 