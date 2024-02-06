<div class="col-lg-2 col-md-2 min-height-profile-messages-left no-padding" style="border-right:2px solid #cfdbe8;">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-white task-table no-padding"> 
		<table class="table-responsive table no-bottom">
			<thead>                                
			<th>Folders</th>

			</thead>
		</table>
	</div>
   
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding task-ul">                                                                                
		<ul style="">
			<a href="javascript:void(0)">
                            <li class="find_type active" data-type="Inbox"><i class="fa fa fa-inbox"></i> Inbox <small class="label pull-right bg-yellow inbox_unread_count" style="margin-right:5px;margin-top:11px;">@if($inbox_unread_count != 0 ){!! $inbox_unread_count !!} @endif</small></li>
			</a>
			<a href="javascript:void(0)">
				<li class="find_type" data-type="Send"><i class="fa fa-envelope"></i> Sent</li>
			</a>
			<a href="javascript:void(0)">
				<li class="find_type" data-type="Draft"><i class="fa fa-flag"></i>Draft</li>
			</a>
			<a href="javascript:void(0)">
				<li class="find_type" data-type="Trash"><i class="fa fa-trash"></i>Trash</li>
			</a>
		</ul>


	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding task-ul1 margin-t-20"> 
		<ul style="">                          
			<a href="javascript:void(0)"><li class="js-add-new-label" style="background:#f0f0f0;padding-left:10px;"><i class="fa fa-plus"></i> Add Labels</li></a>
			@foreach($label_list as $list)
			<a href="javascript:void(0)"><li class="find_type" data-type="{!! ucfirst($list->label_name) !!}" class="p-l-10">{!! $list->label_name !!} <i class="fa fa-square margin-r-10 pull-right margin-r-5 margin-t-10" style="color:{!! $list->label_color !!}"></i></li></a>
			@endforeach
			<a href=""><li>&emsp;</li></a>
			<a href=""><li>&emsp;</li></a>
		</ul>
	</div>
</div>