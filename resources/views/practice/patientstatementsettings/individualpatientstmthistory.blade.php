<table id="example1" class="table table-bordered table-striped ">
	<thead>
		<tr>
		<!--	<th>Patient Name</th>	-->
			<th>Send Statement Date</th>
			<th>Pay by Date</th>
			<th>Balance($)</th>           
			<th>Type</th>
			<th>Created By</th>
		</tr>
	</thead>
	<tbody>		
	   @foreach($statementlist as $patient_value)
		<tr style="cursor:default;">
			<td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_value->send_statement_date,'date') }}</td>
			<td>{{ App\Http\Helpers\Helpers::dateFormat(@$patient_value->pay_by_date,'date') }}</td>
			<td>{!! App\Http\Helpers\Helpers::priceFormat($patient_value->balance,'yes') !!}</td>
			<td>{{ @$patient_value->type_for }}</td>
			<td>{{ @$patient_value->user_detail->name }}</td> 
		</tr>
	   @endforeach
	</tbody>
</table>		 