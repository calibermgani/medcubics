<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 js_common_header no-padding" style="padding-right:10px !important;" id="js_{{ $request['value'] }}"data-index="{{ $request['value'] }}">                            
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
		<div class="box box-view no-shadow" style="border:1px solid #00877f;"><!--  Box Starts -->
			<div class="box-header-view med-bg-green med-white no-border-radius">
				<i class="livicon" data-name="info"></i> <h3 class="box-title med-white">{{ $request['key'] }}</h3>
				<div class="box-tools pull-right">
					<span class="btn btn-box-tool js_close_header js_session_confirm" data-original-title="Remove" data-toggle="tooltip" data-placement="bottom" style="cursor: pointer;" ><i class="fa fa-close"></i></span>
				</div>
			</div><!-- /.box-header -->
			<div class="box-body table-responsive">
				<div class="col-lg-12 no-padding">
					<div class="col-lg-12 no-padding" id="{{ $request['value'] }}">						
					</div>
				</div>
			</div><!-- /.box-body -->
		</div><!-- /.box Ends-->
		<input type="hidden" class="js_div_empty_alert" value="0" />
	</div><!-- /.box Ends-->
</div><!-- /.box Ends-->		