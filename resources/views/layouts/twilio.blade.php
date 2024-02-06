<?php $phone_number = preg_replace('/[^0-9]/', '', @$phone_number); ?>
<div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->

    <div class="box-body form-horizontal no-padding">
        <i class="fa fa-times-circle font14 cur-pointer pull-right med-green bg-white close_Box" style="margin-top:-6px; margin-right: -6px;" data-dismiss="modal"></i>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                <h3 class="text-center">{{@$patientName}}</h3>
                <h4 class="text-center med-orange userPhone"></h4>
                <input type="hidden" class="toNumber"></input>

            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="">&emsp;</div>
                </div> 
                <center>{!! HTML::image($image_tag,null,['class'=>'img-circle','style'=>'width:100px;']) !!}</center>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="">&emsp;</div>
                </div> 
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10">
                <center>  <a href="#" id="callbtn" class="btn-tab">Voice Call</a>
						<a href="#" id="endcall" style="display:none;" class="btn-tab endcall">End Call</a>	
                    <a href="#" onclick="hideMsgTextPan()" class="btn-tab" style="margin-left: 10px;">Message</a>    </center>        
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="margin-t-20">&emsp;</div>
            </div>  
            <div id ="msgDiv" class="hide">
             {!! Form::open(['url'=>'sendSms','id'=>'sendSmsForm','name'=>'medcubicsform','class'=>'medcubicsform']) !!}
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="@if($errors->first('content')) error @endif">
                        {!! Form::textarea('content',null,['class'=>'form-control','name'=>'content','placeholder'=>'Type the content']) !!}
                        {!!Form::hidden('phone_number', $phone_number,['id'=>'phoneNumVal'])!!}
                        {!!Form::hidden('lastCallId', '',['id'=>'lastCallId'])!!}
                        {!! $errors->first('content', '<p> :message</p>')  !!}
                    </div>                    
                </div>
             <button class="btn btn-medcubics pull-right margin-r-10 margin-b-20" onclick="sendMessage()" type="button"><i class="fa fa-paper-plane"></i></button>
               
                {!!Form::close()!!}
             </div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-15">
            <!--    <a href = "javascript:void(0)" data-phone="{{$phone_number}}" class="js-mesgclass text-underline font600" data-id= "js-call">Call History</a><a href = "javascript:void(0)" class="js-mesgclass pull-right font600 text-underline" data-id= "js-msg">Message History</a>-->
            </div>    
            <?php $list = array_merge($allCalls,$messageList);
            function date_compare($a, $b)
            {   
                $t1 = strtotime($a['dateCreated']);
                $t2 = strtotime($b['dateCreated']);                      
                 return $t2 - $t1;
            }    
            usort($list, 'date_compare');?>
            <div class="js_show_details" id="js-call">
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding modal-scroll-calling ">
                    @foreach($list as $val)
                        @if(!isset($val['msg'])) 
                        
                         <div class="js_show_details" id="js-call">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding calling-bg-color">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 margin-t-8">
                                    <p class="no-bottom med-green font600">{{$val['dateCreated']}}</p>
                                    <p>{{$val['startTime']}} - {{$val['endTime']}}</p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 margin-t-8">
                                    <p class="no-bottom">{{$val['status']}}</p>
                                    <p class="med-orange">{{$val['duration']}} Sec</p>
                                </div>
                            </div>
                        </div>
                        @else
                         <div class="js_show_details"  id="js-msg">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding calling-bg-color">
                                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 margin-t-8">
                                    <p class="no-bottom med-green font600">{{$val['dateCreated']}}</p>
                                    <p class="font600">{{$val['dcdateTime']}}</p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 margin-t-8">
                                    <p class="no-bottom">&emsp;</p>
                                    <p class="med-green-o text-right p-r-0">{{$val['status']}}</p>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <p class="text-justify">{{$val['msg']}} </p>
                                </div>
                            </div>
                        </div>
                       @endif 
                    @endforeach
                </div>
                <input type="hidden" value="{{ substr($phone_number,2)}}" name='cellphone' >
                <button class="btn btn-medcubics text-center hide" id ="callbtn" type="button">Call</button>
            </div>
            <div class="js_show_details1" style="display:none;" id="js-msg">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding modal-scroll-calling ">
                    @foreach($messageList as $messge)
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding calling-bg-color">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 margin-t-8">
                            <p class="no-bottom med-green font600">{{$messge['dateCreated']}}</p>
                            <p class="font600">{{$messge['dcdateTime']}}</p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 margin-t-8">
                            <p class="no-bottom">&emsp;</p>
                            <p class="med-green-o text-right p-r-0">{{$messge['status']}}</p>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p class="text-justify">{{$messge['msg']}} </p>
                        </div>
                    </div>
                    @endforeach     
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- Modal Light Box Ends --> 
</div>
<script>

    $('#js-bootstrap-validator').bootstrapValidator({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            content: {
                validators: {
                    notEmpty: {
                        message: auth_no
                    },
                }
            },
        }
    });
    
   function hideMsgTextPan(){
       $("#msgDiv").removeClass("hide selected");
       $("#js-msg").addClass("hide selected");
       $("#js-call").addClass("hide");
   }     
   
   $('.btn-tab').click(function(){
	   $('.selected-btn').removeClass('selected-btn');
	   $(this).addClass('selected-btn');
	});
</script>