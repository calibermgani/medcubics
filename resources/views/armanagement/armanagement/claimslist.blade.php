<div class="tab-content js-claim-dyanamic-tab">
    <div class="active tab-pane" id="claim-tab-info_main0">
        <div class="no-border no-shadow">
            <div class="box-body table-responsive monitor-scroll">
            	@include('layouts.search_fields', ['search_fields'=>$search_fields])
		@if($claims_count > 0)
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-b-4 margin-t-10">
			<div class="col-lg-9">
            <a class="js-create-claim claimdetail form-cursor med-orange font600 p-r-10"> Action <i class="fa fa-angle-double-right"></i></a>                
			<a href="javascript:void(0);" class="js-claim-view-tab form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.charges.review')}}"></i> Review</a>
			<a id="claim_notes_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> Notes</a>
			<a id="claim_assign_all_link" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
			 
			<a id="js-ar-ready" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.ar')}}"></i> Ready</a>
			
			<a id="js-ar-pending" class="form-cursor claimotherdetail font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.pending')}}"></i> Pending</a>
			<a id="js-ar-hold" class="form-cursor claimotherdetail font600 p-l-10 p-r-10  right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.holdoption')}}"></i> Hold</a>
			<a id="js-ar-substatus" class="form-cursor claimotherdetail font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.sub-status')}}"></i> Sub Status</a>
			</div>
			<!-- Added hold reason for bulk hold option in armanagement -->
			<!-- Revision 1 : MR-2786 : 4 Sep 2019 -->
			
			<div class="col-lg-3 pull-right">
				<div class="js-add-new-select hold-option no-margin hide col-lg-12 margin-t-m-5 no-padding" id= "js-holdoptions-type">
					<div class="form-group js_common_ins no-margin">                                                                                                   
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-8 p-r-0 m-t-sm-5 m-t-xs-5 @if($errors->first('insurancetype_id')) error @endif ">
							{!! Form::select('hold_reason_id', array('' => '-- Select Hold Reason --') + (array)$hold_option,  null,['class'=>'form-control select2 input-sm-modal-billing js-add-new-select-opt js-ar-reason','id' =>'js-hold-reason']) !!} 
						</div>
						<div class="col-sm-12 col-xs-12">
							{!!Form::hidden('hold_reason_exist',null)!!}
						</div>
					</div>
					<div class="form-group hide no-margin" id="add_new_span">                   
						<div class="col-lg-11 col-md-11 col-sm-9 col-xs-8 p-r-0  no-margin">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  m-t-sm-5 m-t-xs-5 no-padding hold-option-reason pull-right" >      
								{!! Form::text('other_reason',null,['id'=>'newadded','class'=>'form-control','placeholder'=>'Add New','data-label-name'=>'hold reason','data-field-name'=>'option', 'data-table-name' => 'holdoptions']) !!}
							</div>                                               
							<a href="javascript:void(0)" class="font600" id="add_new_save"><i class="fa {{Config::get('cssconfigs.common.save')}}"></i> Save</a> | 
							<a href="javascript:void(0)" class="font600" id="add_new_cancel"><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-lg-3 pull-right">
				<div class="js-add-new-select substatus-option no-margin hide col-lg-12 margin-t-m-5 no-padding" id= "js-substatus-type">
					<div class="form-group js_common_ins no-margin">
						<?php $sub_status = App\Models\ClaimSubStatus::getClaimSubStatusList(); ?>
					
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-8 p-r-0 m-t-sm-5 m-t-xs-5 @if($errors->first('insurancetype_id')) error @endif ">
							{!! Form::select('sub_status_id', array('' => '-- Select Sub Status --') + (array)$sub_status+ array('-NA-' => '--NIL--'),  null,['class'=>'form-control select2 input-sm-modal-billing js-add-new-select-opt js-ar-substatus','id' =>'js-claim-substatus']) !!} 
						</div>
						<div class="col-sm-12 col-xs-12">
							{!!Form::hidden('sub_status_exist',null)!!}
						</div>
					</div>
					<div class="form-group hide no-margin" id="add_new_span">                   
						<div class="col-lg-11 col-md-11 col-sm-9 col-xs-8 p-r-0  no-margin">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  m-t-sm-5 m-t-xs-5 no-padding hold-option-reason pull-right" >      
								{!! Form::text('other_substatus',null,['id'=>'newadded','class'=>'form-control','maxlength'=>'25', 'placeholder'=>'Add New','data-label-name'=>'Sub Status','data-field-name'=>'sub_status_desc', 'data-table-name' => 'claim_sub_status']) !!}
							</div>                                               
							<a href="javascript:void(0)" class="font600" id="add_new_save"><i class="fa {{Config::get('cssconfigs.common.save')}}"></i> Save</a> | 
							<a href="javascript:void(0)" class="font600" id="add_new_cancel"><i class="fa {{Config::get('cssconfigs.common.cancel')}}"></i> Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		@elseif($claims_count == 0)
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 p-r-0 margin-b-4 margin-t-10">
			<a href="javascript:void(0);" class="form-cursor font600 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.charges.review')}}"></i> Review</a>
			<a id="" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}}"></i> Notes</a>
			<a id="" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Workbench</a>
			<a id="" class="form-cursor font600 p-l-10 p-r-10 right-border orange-b-c"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Pending</a>
			<a id="" class="form-cursor font600 p-l-10"><i class="fa {{Config::get('cssconfigs.Practicesmaster.problemlist')}}"></i> Hold</a>
		</div>
		@endif
                <div class="ajax_table_list hide"></div>
                <div class="data_table_list" id="js_ajax_part">
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
	                            <th class="datatable-dos">DOS</th>
	                            <th class="datatable-claim">Claim No</th> 
								<th>Acc No</th>                                                       
	                            <th>Patient</th>                                                        
	                            <th>Rendering</th>
	                            <th>Facility</th>
	                            <th>Billed To</th>
	                            <th class="datatable-amount">Charges($)</th>
	                            <th class="datatable-amount">Paid($)</th>                        
	                            <th class="datatable-amount">Pat AR($)</th>                        
	                            <th class="datatable-amount">Ins AR($)</th>                        
	                            <th class="datatable-amount">AR Due($)</th>
	                            <th>Status</th>
								<th>Sub Status</th>
	                            <th></th>
	                        </tr>
	                    </thead>               
	                    <tbody>
	                        <!-- AJAX content will be loaded here -->           
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