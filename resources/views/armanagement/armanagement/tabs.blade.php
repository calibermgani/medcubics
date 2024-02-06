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
				<a href="{{url('/charges?search=yes&status=Hold')}}" target="_blank">
					<div class="practice-icons">
						{!! HTML::image( 'img/stat-hold.png',null) !!}
					</div>
					<h4 class="med-orange">@if(!empty($claim_status_count{0}->hold_count)){{ $claim_status_count{0}->hold_count }}@else 0 @endif</h4>
					<div class="btn-group">
						<h4 class="margin-t-m-5">Claims on Hold</h4>
					</div>
				</a>                
			</div>
			<!-- EDI Rejections -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" >
				<a href="{{url('/charges?search=yes&status=Rejection')}}" target="_blank">
					<div class="practice-icons">
						{!! HTML::image( 'img/stat-rejection.png',null) !!}
					</div>
					<h4 class="med-orange">@if(!empty($claim_status_count{0}->rejection_count)){{ $claim_status_count{0}->rejection_count }}@else 0 @endif</h4>	
					<div class="btn-group">
						<h4 class="margin-t-m-5">EDI Rejections</h4>
					</div>
				</a>
			</div>
			<!-- Denied Claims -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" >
				<a href="{{url('/charges?search=yes&status=Denied')}}" target="_blank">
					<div class="practice-icons">
						{!! HTML::image( 'img/stat-denied.png',null) !!}
					</div>
					<h4 class="med-orange">@if(!empty($claim_status_count{0}->denied_count)){{ $claim_status_count{0}->denied_count }}@else 0 @endif</h4>	
					<div class="btn-group">
						<h4 class="margin-t-m-5">Denied Claims</h4>
					</div>
				</a>
			</div>
			<!-- Pending Claims -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" >
				<a href="{{url('/charges?search=yes&status=Pending')}}" target="_blank">
					<div class="practice-icons">
						{!! HTML::image( 'img/stat-un-applied.png',null) !!}
					</div>
					<h4 class="med-orange">@if(!empty($claim_status_count{0}->pending_count)){{ $claim_status_count{0}->pending_count }}@else 0 @endif</h4>
					<div class="btn-group">
						<h4 class="margin-t-m-5">Pending Claims</h4>
					</div>
				</a>
			</div>
			<!-- Submitted Claims -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" >
				<a href="{{url('/charges?search=yes&status=Submitted')}}" target="_blank">
					<div class="practice-icons">
						{!! HTML::image( 'img/stat-submitted.png',null) !!}
					</div>
					<h4 class="med-orange">@if(!empty($claim_status_count{0}->submitted_count)){{ $claim_status_count{0}->submitted_count }}@else 0 @endif</h4>
					<div class="btn-group">
						<h4 class="margin-t-m-5">Submitted</h4>
					</div>
				</a>
			</div>
			<!-- Assigned in workbench -->
			<div class="col-lg-2 col-md-2 col-sm-4 col-xs-4" >
				<a href="{{url('/armanagement/myproblemlist')}}" target="_blank">
					<div class="practice-icons">
						{!! HTML::image( 'img/stat-problem-list.png',null) !!}
					</div>
					<h4 class="med-orange">{{ App\Models\Patients\ProblemList::getProblemListCount() }}</h4>
					<div class="btn-group">
						<h4 class="margin-t-m-5">Assigned</h4>
					</div>
				</a>
			</div>
        
        </div>
    </div>
</div>