<div class="box no-border no-shadow no-background no-bottom margin-t-m-20 js_update_stats">
    <div class="box-header no-border no-background">
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div><!-- /.box-header -->
	<div class="box-body">
        <div class="col-lg-12" style="text-align: center;">
			<input type="hidden" id="js_page_name" value="{{ @$module }}" >
			<input type="hidden" id="js_message" value="{{ @$message }}" >
			<input type="hidden" name="_token" id="csrf_token" value="{{ Session::token() }}" />
			<!-- Claims on hold -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
				<div class="practice-icons">
					{!! HTML::image( 'img/stat-submitted.png',null) !!}
				</div>
				
				@if(!empty($dataArr->counts->Ready))
					<h4 class="med-orange">{{ $dataArr->counts->Ready }}</h4>
				@else
					<h4 class="med-orange">0</h4>
				@endif
				<div class="btn-group">
					<a><h4 class="margin-t-m-5">Ready to Submit</h4></a>
				</div>                
			</div>
			<!-- EDI Rejections -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
				<div class="practice-icons">
					{!! HTML::image( 'img/stat-charges.png',null) !!}
				</div>
				@if(!empty($dataArr->counts->electronicClaims))
					<h4 class="med-orange">{{ $dataArr->counts->electronicClaims }}</h4>	
				@else
					<h4 class="med-orange">0</h4>	
				@endif
				<div class="btn-group">
					<a><h4 class="margin-t-m-5">Electronic Claims</h4></a>
				</div>
			</div>
			
			<!-- Paper Claims Count -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
				<div class="practice-icons">
					{!! HTML::image( 'img/stat-problem-list.png',null) !!}
				</div>
				<h4 class="med-orange">{{ $dataArr->paper_count }}</h4>
				<div class="btn-group">
					<a><h4 class="margin-t-m-5">Paper Claims</h4></a>
				</div>                
			</div>
			
			 <!-- Pending Claims -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
				<div class="practice-icons">
					{!! HTML::image( 'img/stat-un-applied.png',null) !!}
				</div>
				@if(!empty($dataArr->counts->claimEdits))
					<h4 class="med-orange">{{ $dataArr->counts->claimEdits }}</h4>
				@else
					<h4 class="med-orange">0</h4>
				@endif
				<div class="btn-group">
					<a><h4 class="margin-t-m-5">Claim Edits</h4></a>
				</div>                
			</div>
			
			<!-- Denied Claims -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
				<div class="practice-icons">
					{!! HTML::image( 'img/stat-rejection.png',null) !!}
				</div>
				@if(!empty($dataArr->counts->Rejection))
					<h4 class="med-orange">{{ $dataArr->counts->Rejection }}</h4>	
				@else
					<h4 class="med-orange">0</h4>	
				@endif
				<div class="btn-group">
					<a><h4 class="margin-t-m-5">EDI Rejections</h4></a>
				</div>                
			</div>				
		   
			<!-- Submitted Claims -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" > 
				<div class="practice-icons">
					{!! HTML::image( 'img/stat-new-visit.png',null) !!}
				</div>
				<h4 class="med-orange">{{ $dataArr->era_count }}</h4>
				<div class="btn-group">
					<a><h4 class="margin-t-m-5">New ERA</h4></a>
				</div>                
			</div>
        </div>
    </div>
</div>