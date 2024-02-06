<div class="profile-picture">

    <div class="profile-user-name-right">
        <ul style="list-style:none;margin-left: -30px;">
		
			<?php /*	
            <li class="dropdown" style="padding-top:20px;">
                <a href="{{ url('/reports/generated_reports') }}" class="js_next_process setcolor"> 
                    <button class="btn btn-app btn-medcubics-small" type="button">
                        <i class="livicon" data-name="barchart" data-size="16" data-color="#ccc"></i> Reports
                    </button>
                </a>
            </li> 
			*/ ?>
            @if($checkpermission->check_url_permission('profile/message') == 1)	
            <li class="dropdown">
                <a href="{{ url('profile/message') }}" class="js_next_process setcolor"> 
                    <button class="btn btn-app btn-medcubics-small" type="button">
                        <i class="livicon" data-name="message-add" data-size="16" data-color="#ccc"></i>Messages                                                    
                        <span class="badge bg-green-gradient">{{ $get_module_count['message'] }}</span>
                    </button>
                </a>
            </li>
            @else
            <li class="dropdown">&emsp;</li>
            @endif

            <li class="dropdown">
                <a href="{{ url('profile/personal-notes') }}" class="js_next_process setcolor"> 
                    <button class="btn btn-app btn-medcubics-small" type="button">
                        <i class="livicon" data-name="notebook" data-size="16" data-color="#ccc"></i>Notes                                                    
                        <span class="badge bg-green-gradient">{{ $get_module_count['today_note_count'] }}</span>
                    </button>
                </a>
            </li> 
        </ul>
    </div>
  
</div>

<!---End Profile Details -->
                        <!--- Switch User -->

                        <!--- End Switch User -->
                    </div>