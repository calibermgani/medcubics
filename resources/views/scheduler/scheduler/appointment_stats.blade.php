<div class="col-md-4">
    <div class="box no-shadow no-border" style="margin-left: 9px; border-radius: 6px;">
        <div class="box-header transparent">
            <i class="livicon" data-name="retweet"></i>
            <h3 class="box-title">Appointments</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool default-cursor"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body chat chat-scheduler">
            <p class="margin-t-10"><span class="font600 Scheduled"><i class="fa {{Config::get('cssconfigs.patient.calendar')}} Scheduled"></i> Scheduled </span> <span class="pull-right font12">{{@$index_stats_count->scheduled}}</span></p>            
            <p class="margin-t-m-2"><span class="font600 conformed"><i class="fa {{Config::get('cssconfigs.scheduler.check-in')}} conformed"></i> Completed </span> <span class="pull-right font12">{{@$index_stats_count->completed}}</span></p>
            <p class="margin-t-m-2"><span class="font600 canceled"><i class="fa fa-calendar-times-o canceled"></i> Canceled </span> <span class="pull-right font12">{{@$index_stats_count->canceled}}</span></p>
            <p class="margin-t-m-2"><span class="font600 encounter"><i class="fa {{Config::get('cssconfigs.scheduler.encounter')}} encounter"></i> Encounter  </span> <span class="pull-right font12">{{@$index_stats_count->encounter}}</span></p>
            <p class="margin-t-m-2 m-b-m-15"><span class="font600 noshow"><i class="fa {{Config::get('cssconfigs.common.calendar')}} noshow"></i> No Show </span> <span class="pull-right font12">{{@$index_stats_count->no_show}}</span></p>       

        </div><!-- /.chat -->
    </div><!-- /.box (chat box) -->
</div>