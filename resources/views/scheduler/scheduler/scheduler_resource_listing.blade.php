<div class="col-md-4" style="border-left: 1px dashed #ccc; border-right: 1px dashed #ccc; min-height: 200px">
    <div class="box no-shadow no-border" style="margin-left: 3px; border-radius: 6px;">
        <div class="box-header" style="background: transparent;">
            <i class="fa @if($default_view == Config::get('siteconfigs.scheduler.default_view_facility')) {{Config::get('cssconfigs.Practicesmaster.user')}} @else {{Config::get('cssconfigs.Practicesmaster.facility')}} @endif"></i>
            <h3 class="box-title">
                @if($default_view == Config::get('siteconfigs.scheduler.default_view_facility'))
                    {{Config::get('siteconfigs.scheduler.default_view_provider')}}
                @else
                    {{Config::get('siteconfigs.scheduler.default_view_facility')}}
                @endif
            </h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool default-cursor"><i class="fa fa-minus"></i></button>
            </div>
        </div>

        <div class="box-body chat chat-scheduler" id="js-resource-listing">
            @include('scheduler/scheduler/scheduler_resource_listing_form')
        </div><!-- /.chat -->
    </div><!-- /.box (chat box) -->
</div>