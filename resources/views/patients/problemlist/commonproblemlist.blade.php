<div id="claim_assign_all" class="modal fade in">
    <?php 
        if(!isset($get_default_timezone)){
           $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
        }                  
    ?>
    <div class="modal-sm-usps">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-left"> Workbench</h4>
            </div>
            <div class="modal-body no-padding" >
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                    
                    <div class="box-body form-horizontal no-padding">                        
                        {!! Form::open(['method'=>'POST','name'=>'all_claim_assign_form','class'=>'js_all_claim_assign_form popupmedcubicsform']) !!} 
                        <input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.assign") }}' />
                        
                        <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                            <div class="box-body-block"><!--Background color for Inner Content Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!-- General Details Full width Starts -->                           
                                    <div class="box no-border  no-shadow" ><!-- Box Starts -->
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--  1st Content Starts -->
                                           
                                            <div class="box-body form-horizontal no-padding"><!-- Box Body Starts --> 
                                                <div class="form-group-billing margin-t-10">
                                                    {!! Form::label('Assigned To', 'Assigned To', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600 star']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
														<?php
															$user_list = App\Http\Helpers\Helpers::user_list();
														?>                                   
                                                        {!! Form::select('assign_to', [''=>'-- Select --']+(array)$user_list,null,['class'=>'select2 form-control input-sm-modal-billing js_assign_to']) !!}
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    {!! Form::label('Followup Date', 'Followup Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600 star']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">     
                                                        <i class="fa {{Config::get('cssconfigs.common.calendar')}} form-icon-billing font12" onclick = "iconclick('follow_up_date')"></i> 
                                                        {!! Form::text('follow_up_date',null,['placeholder'=>Config::get('siteconfigs.default_date_format'),'class'=>'form-control dm-date js_follow_up_date', 'autocomplete'=>'off']) !!}
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    {!! Form::label('Priority', 'Priority', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600 star']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                       {!! Form::select('priority', [''=>'-- Select --','High' => 'High','Moderate' => 'Moderate','Low'=>'Low'],null,['class'=>'select2 form-control input-sm-modal-billing js_priority']) !!}
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    {!! Form::label('Status', 'Status', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green font600 star']) !!}
                                                    <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                       {!! Form::select('status', [''=>'-- Select --','Assigned' => 'Assigned','Inprocess' => 'Inprocess','Completed'=>'Completed'],null,['class'=>'select2 form-control input-sm-modal-billing js_status']) !!}
                                                    </div>                                   
                                                </div>
                                                <div class="form-group-billing">
                                                    <div class="col-lg-12 col-md-12 col-sm-8 col-xs-10"> 
                                                        {!! Form::textarea('description',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Problem Description']) !!}
                                                    </div>
                                                </div>                       
                                            </div><!-- /.box-body Ends-->
                                           
                                        </div><!--  1st Content Ends -->                            
                                    </div><!--  Box Ends -->

                                </div><!-- General Details Full width Ends -->
                            </div><!-- Inner Content for full width Ends -->
                         
                        </div><!--Background color for Inner Content Ends --> 
                        <div class="text-center">    
                        {!!Form::submit('Save', ['class' => 'margin-b-6 btn btn-medcubics-small js_allclaim_assign_btn'])!!}
                        <button class="btn btn-medcubics-small close_popup margin-b-6" type="button">Cancel</button>
                        </div>
                        {!! Form::close() !!}                                           
                    </div>

                </div><!-- /.box-body -->                                
            </div><!-- /.box Ends Contact Details-->
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog --> 
<script type="text/javascript">
   <?php if($get_default_timezone){ ?> 
        var get_default_timezone = '<?php echo $get_default_timezone; ?>';
        <?php }else{?>
        var get_default_timezone = '';
   <?php }?>
</script>