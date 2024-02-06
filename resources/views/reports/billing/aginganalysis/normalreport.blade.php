<div class="box box-view no-shadow"><!--  Box Starts -->

    <div class="box-header-view">
        <i class="livicon" data-name="info"></i> <h3 class="box-title">{{ @$title }}</h3>
        <div class="pull-right">
            <h3 class="box-title med-orange">Date : {{ date("m/d/y") }}</h3>
        </div>
    </div>

	<div class="box-body no-padding"><!-- Box Body Starts -->

		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
			<div class="box box-info no-shadow no-border no-bottom">
				<div class="box-body margin-t-10">
					<div class="table-responsive">
						<table class="table table-bordered table-striped dataTable">
							<thead>
								<tr>
									@php $count_r = 1; @endphp 
									@foreach($header as $header_name => $header_val)

									@if($count_r ==1)
									<th style="cursor:default;" class="text-right">{{ @ $header_val }}</th>
									@elseif($count_r % 2 == 0)
									<th style="cursor:default;" colspan="2" class="text-center">{{ @ $header_val }}</th>
									@endif
									@php $count_r++; @endphp
									@endforeach
								</tr>
							</thead>                
							<tbody>
								@php $count = 1; @endphp  
								@foreach($aging_report_list as $report_value)  
								@if($count % 2 == 0)
								<tr style="cursor:default;" role="row">   
									@foreach($report_value as $column_value)                                
									<td class="@if(@$column_value != 'Patient' || @$column_value != 'Total AR' )med-green font600  @endif text-right">  {{ @$column_value }}</td>    
									@endforeach
								</tr>
								@else
								<tr style="cursor:default; " role="row">   
									@foreach($report_value as $column_value)
									<td class="@if(@$column_value != 'Claims' || @$column_value != 'Value' || @$column_value != 'Insurance' ||  @$column_value != 'Total AR %')med-green font600 @endif text-right">{{ @$column_value }}</td>    
									@endforeach
								</tr>
								@endif
								@php $count++; @endphp
								@endforeach                   
							</tbody>
						</table>
					</div><!-- /.box-body -->
				</div><!-- /.box -->
			</div><!-- /.box -->
		</div>
	</div><!-- Box Body Ends --> 
</div><!-- /.box Ends-->