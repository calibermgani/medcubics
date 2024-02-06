<div class="tab-content js-claim-dyanamic-tab">
	<div class="active tab-pane" id="claim-tab-info_main0">
		<div class="no-border no-shadow">
			<div class="box-body table-responsive monitor-scroll">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 no-padding">  		
					@include('layouts.search_fields', ['search_fields'=>$search_fields])          
				</div>
				<div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4 margin-t-10">             
					<a class="form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i></a>	
					<a id="claim_assign_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
				</div>
				<div class="ajax_table_list hide"></div>
                <div class="data_table_list " id="js_ajax_part">
					<table id="search_table_claims" class="claims1 table table-bordered table-striped">				
						<thead>
							<tr>
								<th class="chechbox-readytosubmit table-select-dropdown">
									<div class="no-margin" aria-checked="false" aria-disabled="false">
									   <select name="js-select-option">
										   <option value="none">None</option>
										   <option value="page">This List</option>
										   <option value="all">All List</option>
									   </select>
									   <label for="js-select-all" style="min-height: 10px;"></label>
									</div>
								</th>
								<th>Claim No</th>
								<th>DOS</th>
								<th>Acc No</th>
								<th>Patient Name</th>
								<th>Insurance</th>
								<th>Category</th>
								<th>Rendering</th>
								<th>Facility</th>								
								<th>Denied CPT</th>
								<th>Denied Date</th>
								<th>Sub Status</th>
								<?php /*
								<th>Denial Reason Code</th>
								*/ ?>
								<th>Claim Age</th>
								@if(isset($workbench_status) && $workbench_status == 'Include')
									<th class="fnWBHead">Workbench Status</th>
								@endif
								<th class="text-right">Charge Amt($)</th>
								<th class="text-right">Outstanding AR($)</th>							
							</tr>
						</thead>
						<tbody>
							
						</tbody>
					</table>

				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box -->		
	</div>
</div>

<style>
.ar-hide-class {
	display: none;
}
</style>