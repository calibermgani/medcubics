<div class="col-md-12 space-m-t-15 print-m-t-30"><!-- Col Starts -->
    <div class="box-block box-info"><!-- Box Starts -->
        <div class="box-body"><!-- Box body Starts -->
            
            <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                <h3>{{ $employer->employer_name }}</h3>
                 <p class="push"><b>Employment Status : </b>{{ $employer ->employer_status }}</p>
				 @if($employer ->employer_status == 'Employed')
                 <p class="push"><b>Organization Name : </b>{{ $employer ->employer_organization_name }}</p>
                 <p class="push"><b>Occupation : </b>{{ $employer ->employer_occupation }}</p>
				 @endif
				 @if($employer ->employer_status == 'Student')
                 <p class="push"><b>Student Status : </b>{{ $employer ->employer_student_status }}</p>
				 @endif
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form-horizontal med-left-border">
                <div class="form-group">
                    {!! Form::label('Phone', 'Phone',  ['class'=>'col-lg-3 col-md-4 col-sm-3 col-xs-12 control-label']) !!} 
                    <div class="col-lg-9 col-md-8 col-sm-8 col-xs-10">
                        <span class="@if($employer->work_phone == '') nill @endif"> @if(@$employer->work_phone != ''){{ @$employer->work_phone}} <span class="@if(@$employer->work_phone_ext != '')bg-ext @endif"> {{ @$employer->work_phone_ext }}</span> @else - Nil - @endif</span>
                    </div>
                </div>                                
            </div>

        </div><!-- /.box-body ends -->    
        <?php 
            $activetab = 'employer'; 
            $routex = explode('.',Route::currentRouteName());
            if(count($routex) > 2){
                if($routex[2] == 'notes'){
                    $activetab = 'notes';
                }
            }
        ?>
    </div><!-- /.box ends -->
    <div class="med-tab nav-tabs-custom space10 no-bottom">
        <ul class="nav nav-tabs">
            @if($checkpermission->check_url_permission('employer/{employer}') == 1 )
            <li class="@if($activetab == 'employer') active @endif"><a href="{{ url('employer/'.$employer->id) }}" ><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} i-font-tabs"></i> Employer Details</a></li> 
            @endif	
            @if($checkpermission->check_url_permission('employer/{id}/notes/{notes}') == 1)
            <!--<li class="@if($activetab == 'notes') active @endif"><a href="{{ url('employer/'.$employer->id.'/notes') }}"><i class="fa {{Config::get('cssconfigs.Practicesmaster.notes')}} i-font-tabs"></i> Notes</a></li>   -->
            @endif	
        </ul>
    </div>

</div><!--/.col (left) -->