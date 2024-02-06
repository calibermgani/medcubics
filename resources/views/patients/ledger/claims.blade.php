<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="box box-view-border no-shadow margin-t-m-10 no-border-radius">
        <div class="box-header-view-white ">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Claim List</h3> 
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
		<div class="box-body">
		   <div class="row">
			   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="col-sm-3 pull-right">
						{!! Form::open(['onsubmit'=>"event.preventDefault();", 'url'=>'patients/'.$patients->id.'/ledger/ajax/pagination', 'method'=>'POST']) !!}
							{!! Form::text('ledger_search',null,['class'=>'form-control input-sm-modal js_ledger_claim_search','placeholder'=>'Search...']) !!}
						{!! Form::close() !!}
					</div>
				</div>
			</div>
			<div id="js_ajax_part">
				<div class="js_claim_list_part">
					@include ('patients/ledger/claimlist')
				</div>
			</div>
		</div>
    </div>
</div>

<div id="claim-charge-modal-popup" class="modal fade in">
	<div class="modal-md-800">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"> Edit Charge</h4>
			</div>
			<div class="modal-body no-padding">

			</div>

		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->
@include ('patients/problemlist/commonproblemlist') 