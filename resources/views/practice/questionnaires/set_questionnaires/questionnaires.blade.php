@extends('admin')
@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> APP Settings <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Set Questionnaires</span></small>
        </h1>
        <ol class="breadcrumb">
           
           <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
			@if(count(@$questionnaries) > 0)
            <li class="dropdown messages-menu">
                @include('layouts.practice_module_export', ['url' => 'api/questionnaireexport/'])
            </li>
			@endif
            <li><a href="{{ url('questionnaire/template')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/questionnaire')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop
@section('practice-info')
	@include ('practice/questionnaires/tabs') 
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20" ><!-- Col-12 starts -->
    
    <div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view">
			<i class="fa fa-bars"></i> <h3 class="box-title">List</h3>
            <div class="box-tools pull-right">
                @if($checkpermission->check_url_permission('questionnaires/create') == 1)
                <a href="{{ url('questionnaires/create') }}" class="font600 font14"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Questionnaires</a>
                @endif 
            </div>
		</div><!-- /.box-header -->
    
	    <div class="box no-border no-shadow">
			<div class="box-body">	<!-- Box Body Starts -->
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					@if(Session::get('message')!== null) 
					<p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
					@endif
				</div>
				<div class="table-responsive margin-t-20">
					<table id="example1" class="table table-bordered table-striped ">         
						<thead>
							<tr>
								<th>Provider</th>
								<th>Facility</th>
								<th>Questionnaires Template</th>
								<th>Created By</th>
								<th>Updated By</th>
								<th>Updated On</th>
							</tr>
						</thead>
						<tbody>
							<?php $count = 1;   ?>  
							@foreach($questionnaries as $questionnaries)
							<?php $questionnaries->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$questionnaries->id,'encode'); ?>
							<tr data-url="{{ url('questionnaires/'.@$questionnaries->id) }}" class="js-table-click clsCursor">
								<td>
									<?php $provider = @$questionnaries->provider; ?>  
									<?php $provider_name = @$provider->provider_name ; ?>  
									<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
										<a id="someelem{{hash('sha256','p_'.@$provider->id.$count)}}" class="someelem" data-id="{{hash('sha256','p_'.@$provider->id.$count)}}" href="javascript:void(0);"> 
										{{ str_limit(@$provider_name,25,'...') }} {{ @$questionnaries->provider->degrees->degree_name }}</a> 
										<?php @$provider->id = 'p_'.@$questionnaries->provider->id.$count; ?>
										@include ('layouts/provider_hover')
									</div> 
		                        </td>
								<td>
									<div class="col-lg-12" style="padding-bottom: 0px; padding-left: 0px;">
										<?php $facility = @$questionnaries->facility; ?> 
										<a id="someelem{{hash('sha256','f_'.@$questionnaries->facility->id.$count)}}" class="someelem" data-id="{{hash('sha256','f_'.@$questionnaries->facility->id.$count)}}" href="javascript:void(0);">							
										{{ str_limit(@$facility->facility_name,25,'...') }}</a>
									<?php	@$facility->id = 'f_'.@$questionnaries->facility->id.$count; ?> 
										@include ('layouts/facility_hover')
									</div>
								</td>
								<td>{{ @str_limit(@$questionnaries->questionnaries_option->title,25) }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname(@$questionnaries->created_by) }}</td>
								<td>{{ App\Http\Helpers\Helpers::shortname(@$questionnaries->updated_by) }}</td>
								<td>
								@if($questionnaries->updated_at !='' && $questionnaries->updated_at !='-0001-11-30 00:00:00' && $questionnaries->updated_at !='0000-00-00 00:00:00')
                               {{ App\Http\Helpers\Helpers::timezone(@$questionnaries->updated_at, 'm/d/y') }}
  								@endif
								</td>
							</tr>
							<?php $count++;   ?> 
							@endforeach      
						</tbody>
					</table>
				</div>                                
			</div><!-- /.box-body ends -->
	    </div>
    </div>
</div><!-- Col-12 Ends -->
<!--End-->
@stop   