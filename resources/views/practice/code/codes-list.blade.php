<table id="example1" class="table table-bordered table-striped js_no_change">
	<thead>
		<tr>
			<th>Code Category</th>
			<th>Transaction Code</th>
			<th>Description</th>
			<th>Status</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php $allow_url = $checkpermission->check_url_permission('code/{code}'); ?>
		@foreach($codes as $code)  
			<?php $code_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($code->id,'encode'); ?>
			<tr @if($allow_url) data-url="{{ url('code/'.$code_id) }}" @endif class="js-table-click clsCursor">
				<td>{{ @$code->codecategories->codecategory }}</td>
				<td>{{ $code->transactioncode_id }}</td>
				<td>{{ str_limit($code->description, 215, ' ..') }}</td>
				<td>{{ $code->status }} </td>  
				<td class="js-prevent-redirect">
					<a href="#" class="js_code_id" data-reason-code="{{ @$code->transactioncode_id }}" data-code-id="{{ $code->transactioncode_id }}" data-reason-type="{{ @$code->rule_engine[0]->reason_type }}" data-co-claim-status="{{ @$code->rule_engine[0]->claim_status }}" data-co-next-resp="{{ @$code->rule_engine[0]->next_resp }}" data-co-priority="{{ @$code->rule_engine[0]->priority }}"data-pr-claim-status="{{ @$code->rule_engine[1]->claim_status }}" data-pr-next-resp="{{ @$code->rule_engine[1]->next_resp }}" data-pr-priority="{{ @$code->rule_engine[1]->priority }}"data-oa-claim-status="{{ @$code->rule_engine[2]->claim_status }}" data-oa-next-resp="{{ @$code->rule_engine[2]->next_resp }}" data-oa-priority="{{ @$code->rule_engine[2]->priority }}"data-pi-claim-status="{{ @$code->rule_engine[3]->claim_status }}" data-pi-next-resp="{{ @$code->rule_engine[3]->next_resp }}" data-pi-priority="{{ @$code->rule_engine[3]->priority }}" data-toggle="modal" data-target="#ruleEngine">
						<i class="fa fa-server" aria-hidden="true"></i>
					</a>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>