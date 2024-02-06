<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-t-m-13 hide"><!-- Hided -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 " style="background: #00877f;min-height: 300px;">
        <div id='calendar' class="profile-calendar" style="margin-left:-10px"></div>
    </div>
</div>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-r-0 margin-t-m-13">

    <div class="box box-info no-shadow">
        <div class="box-header with-border">
            <i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Users</h3>

        </div><!-- /.box-header -->
        <div class="box-body no-padding">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 form-horizontal margin-t-5" style="padding-left: 3px; padding-right:3px;" >
                {!! Form::text('provider_id',null,['class'=>'form-control no-border-radius ','placeholder'=>'Search User','style'=>'line-height:30px;']) !!}
                
            </div>
            <i class="fa fa-search pull-right med-gray" style="position: absolute; text-align: right; margin-top:11px;margin-right:0px; right:10px;"></i>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 min-height-profile-user">

            <ul class="no-padding" style="list-style-type: none; line-height: 25px;" id="showing_filter_users">
				<i class="fa fa-spinner fa-spin coverloadingimg" id="user_listing" style="margin-left:120px;display:none"></i>
				
                @foreach($users as $user)
                @if($user->id != 1)
                <?php
					$filename = @$user->avatar_name.'.'.@$user->avatar_ext;
					$img_details = [];
					$img_details['module_name']=($user->practice_user_type == "customer") ? 'customers' : 'user';
					$img_details['file_name']=$filename;
					$img_details['practice_name']="admin";
					$img_details['class']='margin-r-10 no-padding';
					$img_details['style']='width:25px; border-radius:50%; margin-top:-8px; margin-bottom:-15px; height:25px;';
					$img_details['alt']='user-image';
					$image_tag = App\Http\Helpers\Helpers::checkAndGetAvatar($img_details);
				?>

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-bottom:1px dashed #e2e9f1;">			
                    <div class="col-lg-02 col-md-2 col-sm-4 col-xs-12 p-l-0 p-r-0" style="">{!! $image_tag !!} </div>
                    <div class="col-lg-10 col-md-8 col-sm-8 col-xs-12" style="margin-bottom:-3px;">
                        <p class="" style="padding-top: 7px"><span class="med-darkgray  font14">{{$user->name}}</span> <i id="user-online_{{$user->id }}" class="fa fa-circle med-gray @if($user->is_logged_in =='1')med-green-o @endif pull-right margin-t-5" style="font-size:9px;"></i></p>
                    </div>
                </div>
                @endif
                @endforeach                
            </ul>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
@push('view.scripts') 
<script>
	var typingTimer;                //timer identifier
	var doneTypingInterval = 500;  //time in ms, 5 second for example

	//on keyup, start the countdown
	$("input[name=provider_id]").on('keyup', function () {
	  clearTimeout(typingTimer);
	  typingTimer = setTimeout(doneTyping, doneTypingInterval);
	});

	//on keydown, clear the countdown 
	$("input[name=provider_id]").on('keydown', function () {
	  clearTimeout(typingTimer);
	});

	//user is "finished typing," do something
	function doneTyping () { 
		$('#user_listing').show();
		if($("input[name=provider_id]").val() != '')
			var keyword = '/'+$("input[name=provider_id]").val();      
		else
				var keyword = '';
			var url = api_site_url+'/profile/filteruser'+keyword;
		$.ajax({
			type : 'GET',
			url  : url,
			success :  function(result) {
				$('#showing_filter_users').html(result);
				$('#user_listing').hide();
				
			}
		});
	}


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
	
	// -------------Online live Updating --- Pugazh ------------

	$(document).ready(function(){
		setInterval(getActiveUserList, 1000 * 60 * 5);   /*  ------5 minutes------       */
		var ajax_data = 1;
		function getActiveUserList(){
			$.ajax({
				type:"post",
				headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            		},
				data:ajax_data,
				url: api_site_url + '/getActiveUserList',
				success: function(data){
					 var is_logged = data;
					 $.each(is_logged, function(index, value){
						if(value.is_logged_in == 1){
							$("#user-online_"+value.id).removeClass('med-gray').addClass('med-green-o');
						} else { 
							$("#user-online_"+value.id).removeClass('med-green-o').addClass('med-gray');	
						}
					 })
				}
			})
		}
	});

</script>
@endpush