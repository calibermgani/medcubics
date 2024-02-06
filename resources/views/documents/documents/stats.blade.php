<div class="box no-border no-shadow no-background no-bottom margin-t-m-20 js_update_stats">
    <div class="box-header no-border no-background">
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
   	<?php $document_count =  App\Models\Document::DocumentCountList(); ?>
	<div class="box-body">
        <div class="col-lg-12" style="text-align: center;">
		<input type="hidden" id="js_page_name" value="{{ @$module }}" >
		<input type="hidden" id="js_message" value="{{ @$message }}" >
		<input type="hidden" name="_token" id="csrf_token" value="{{ Session::token() }}" />
        
            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
                <div class="practice-icons">
                    {!! HTML::image( 'img/stat-documents.png',null) !!}
                </div>

				<h4 class="med-orange">{{ $total_document_count }}</h4>
				<div class="btn-group"><a>
                   <h4 class="margin-t-m-5">No. Documents</h4> </a>
				</div>                
            </div>
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
                <div class="practice-icons">
					{!! HTML::image( 'img/stat-problem-list.png',null) !!}
                </div>
				<h4 class="med-orange">{{ $assigned_document_count }} </h4>	
				<div class="btn-group"><a>
                   <h4 class="margin-t-m-5">Assigned</h4></a>
				</div>                
            </div>
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
                <div class="practice-icons">
					{!! HTML::image( 'img/stat-appointment.png',null) !!}
                </div>
				<h4 class="med-orange">{{ $inprocess_document_count }}</h4>	
				<div class="btn-group"><a>
                   <h4 class="margin-t-m-5">In Process</h4></a>
				</div>                
            </div>
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
                <div class="practice-icons">
					{!! HTML::image( 'img/stat-new-visit.png',null) !!}
                </div>
				<h4 class="med-orange">{{ $review_document_count }}</h4>
				<div class="btn-group"><a>
                   <h4 class="margin-t-m-5">In Review</h4></a>
				</div>                
            </div>
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
                <div class="practice-icons">
					{!! HTML::image( 'img/stat-un-applied.png',null) !!}
                </div>
				<h4 class="med-orange">{{ $pending_document_count }}</h4>
				<div class="btn-group"><a>
                   <h4 class="margin-t-m-5">In Pending</h4></a>
				</div>                
            </div>
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" >  
                <div class="practice-icons">
					{!! HTML::image( 'img/stat-submitted.png',null) !!}
                </div>
				<h4 class="med-orange">{{ $completed_document_count }}</h4>
				<div class="btn-group"><a>
                   <h4 class="margin-t-m-5">Completed</h4></a>
				</div>                
            </div>        
        </div>
    </div>
</div>