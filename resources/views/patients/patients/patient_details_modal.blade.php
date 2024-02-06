<div class="box-body padding-0-4 table-responsive">
	<table id="patientmodal_details_tbl" class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>Patient Name</th>
				<th>Acc No</th>
				<!-- <th>Created On</th> -->
				<th>AR Due</th>
				</tr>
		</thead>
		<tbody>			
			@foreach($patients as $patient)
				@if($patient !='')
					<?php 
						$patient_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$patient->id); 
						$patient_name = App\Http\Helpers\Helpers::getNameformat("$patient->last_name","$patient->first_name","$patient->middle_name");
					?>
					<tr data-url="{{ url('patients/'.$patient_id.'/ledger') }}" class="js-table-click clsCursor">
						<td>
							<div class="col-lg-12 p-b-0 p-l-0">
		                        {{ $patient_name }}					
							</div>
						</td>
						<td>{{ @$patient->account_no }}</td>							
						<?php /* ?> <td data-url="{{ url('patients/'.$patient_id) }}" class="js-table-click clsCursor">{{ App\Http\Helpers\Helpers::dateFormat(@$patient->created_at,'date')}}</td> <?php */ ?>
		                <td class="pull-right">
		                	{!! App\Http\Helpers\Helpers::priceFormat(@$patient->patient_claim_fin[0]->total_ar) !!}
		                </td>
					</tr>
				@endif
			@endforeach
		</tbody>
	</table>
</div><!-- /.box-body -->