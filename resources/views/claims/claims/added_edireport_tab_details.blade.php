@if(count(@$edireport_detail)>0)
@foreach($edireport_detail as $edireport_detail_val)  
<div class="tab-pane jsremovealltab_details" id="edireport-tab-info_{{@$edireport_detail_val->id}}">
    <div class="box-view no-shadow no-border"><!--  Box Starts -->                        
        <div class="box-body form-horizontal no-padding"> 
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white padding-10">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding yes-border margin-t-5 bg-white tabs-border border-radius-4">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-m-10">
                        <span class="med-orange padding-0-4 font13 margin-l-10 bg-white font600"> EDI Report {{@$edireport_detail_val->file_name}}</span>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive" >
                        <div class="col-lg-12 col-md-12 col-sm-6 col-xs-12 table-responsive" >
							<?php 
								$edireport_detail_val->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$edireport_detail_val->id,'encode'); 
								$file_content = App\Http\Controllers\Claims\Api\ClaimApiController::tabviewEdiReportApi($edireport_detail_val->id); 
							?>
							<div style="overflow-wrap: break-word;" class="margin-t-10 margin-b-10">
							<pre>{{ $file_content }}</pre>
							</div>
                        </div>
					</div>
                </div>
            </div>
        </div>        
    </div><!-- /.box-body -->
</div>
@endforeach
@endif