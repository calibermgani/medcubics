@extends('admin')
<?php // dd($superbill_array); ?>
@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.superbills')}} font14"></i> Superbills</small>
        </h1>
        <ol class="breadcrumb">
			<li><a href="{{ url('superbills')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>            
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/superbills')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
<div class="col-md-12 js_alert_class hide"></div>
<div class="col-md-12 margin-t-m-18">
	<div class="box-block">
		<div class="box-body">
			
			<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 ">
				<h3 class="med-orange">{{ @$superbill_array->template_name }}</h3>
				<p><span class="med-green font600">Provider :</span> {{ $superbill_array->provider->provider_name.' '.@$superbill_array->provider->degrees->degree_name }} <span class="med-orange">{{ $superbill_array->provider->short_name }} </span></p>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 med-left-border">
				@if(@$superbill_array->creator->name)
					<p><span class="med-green font600">Created by</span> <span class="pull-right">{{ @$superbill_array->creator->name }}</span></p>														
				@endif
				@if(@$superbill_array->modifier->name)
					<p><span class="med-green font600">Updated by</span> <span class="pull-right">{{ @$superbill_array->modifier->name}}</span></p>
				@endif
                <p><span class="med-green font600">Status </span>
				<span class="pull-right patient-status-bg-form @if(@$superbill_array->status == 'Active')label-success @else label-danger @endif">{{ @$superbill_array->status }}</span>
				</p>
			</div>
		</div><!-- /.box-body -->
	</div><!-- /.box -->
</div>
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">              
    <a href="{{ url('superbills/'.@$superbill_array->id.'/edit')}}" class=" pull-right font14 font600 margin-r-5 hidden-print"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>            
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 box-body">
	<div class="box box-view no-shadow"><!--  Box Starts -->
		<div class="box-header-view margin-b-10">
			<i class="livicon" data-name="info"></i> <h3 class="box-title">Template Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 droppable box-body table-responsive no-padding margin-t-10" id="columns-almostFinal" style="border:1px solid #85E2E6;padding:50px 10px !important;">
			@foreach ($superbill_array->get_list_order as $key => $get_list)
			<?php $header_list = explode(",",$superbill_array->order_header); ?>
			<?php $style = explode("/&/",$superbill_array->header_style); ?>
			<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12  no-padding" style="{{ $style[$key] }}">
           <!-- <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 no-padding"> -->  
				<ul class="checkbox-grid no-padding line-height-26 yes-border med-border-color no-bottom" style="list-style-type:none;">
					<li class="superbill">
						<table class="table-striped-view" style="width: 100%;">
							<thead>
								<tr>
									<th style="width: 75%" class="js_header_order">{{ $header_list[$key] }}</th>
									<th style="text-align: center; width: 12%">New</th>
									@if($get_list == "skin_procedures" || $get_list == "medications")<th style="text-align: center; width: 13%">Units</th> @endif
								</tr>
							</thead>
						</table>                                     
					</li>
					@foreach ($superbill_array->$get_list as $ind_key => $ind_val)
					<?php $value = explode("::",$ind_val) ?>
					<li class="superbill text-center">
						<table class="table-striped-views">
							<tbody>
								<tr>
									<td style="width: 75%;">&nbsp;{{ $value[1] }}</td>
									<td style="width: 12%;">&nbsp;{{ $value[0] }}</td>
									@if($get_list == "skin_procedures" || $get_list == "medications")
									<td style="text-align: center; width: 13%">
										@if($get_list == "skin_procedures")
											{{ @$superbill_array->skin_procedures_units[$ind_key] }} 
										@endif
										@if($get_list == "medications")
											{{ @$superbill_array->medications_units[$ind_key] }} 
										@endif
									</td>
									@endif	
								</tr>
							</tbody>
						</table>                                    
					</li>
					@endforeach
				</ul><!-- /.box Ends-->
			</div><!-- /.box Ends-->		
			@endforeach
		</div>
	</div>
</div>
@stop 