<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">
    <div id='calendar'></div>
</div>     
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space-m-t-7 no-padding">

    <div class="box box-info no-shadow space20">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Team Members</h3>
            <div class="box-tools pull-right" style="margin-top: 3px;">
                  
            </div>
        </div><!-- /.box-header -->
        <div class="box-body">
            <ul class="no-padding" style="list-style-type: none; line-height: 45px;">			
			@foreach($users as $user)
				@if($user->id != 1)
				<?php
					$filename = @$user->avatar_name.'.'.@$user->avatar_ext;
					$img_details = [];
					$img_details['module_name']=($user->practice_user_type == "customer") ? 'customers' : 'user';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="admin";
					$img_details['class']='online';
					$img_details['style']='width:30px; border-radius:50%; height:30px;';
					$img_details['alt']='user-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>
                <li>{!! $image_tag !!} {{$user->short_name}} <span class="pull-right" style="font-size: 11px; color:#ccc"> {{$user->designation}}</span></li>
				@endif
			@endforeach
              
            </ul>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
@push('view.scripts') 
<script>
$(document).ready(function() {

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    var events_array = [
        {
        title: 'Test1',
        start: new Date(2012, 8, 20),
        tip: 'Personal tip 1'},
		{
        title: 'Test2',
        start: new Date(2012, 8, 21),
        tip: 'Personal tip 2'}
    ];

    $('#calendar').datepicker({
        header: {
            left: 'prev ',
            center: 'title',
            right: 'next'
        },
        selectable: true,
        events: events_array,
        eventRender: function(event, element) {
            element.attr('title', event.tip);
        }
    });
});
</script>
@endpush