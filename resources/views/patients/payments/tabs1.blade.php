<div class="col-md-12" style="margin-top:-20px;">
    <div class="box box-info no-shadow" style="border: 1px solid #8ce5bb">
        <div class="box-body" style="background: #e5faf0; border-radius: 4px;">

            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 tab-border-bottom" style="margin-bottom: -8px;">
                {!! HTML::image('img/profile-pic.jpg',null,['class'=>'  margin-r-20','style'=>'width:70px; border-radius:50%; border:3px solid #fff;  -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
                -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);margin-top:10px;
                box-shadow: 0 0 5px rgba(0,0,0,0.5);   height:70px; float:left;']) !!}

                <h5 class="med-green no-margin">Andrew, Russell  </h5>
                <h5 class=""><span class="med-orange sm-size"><i class="med-orange med-gender fa fa-male margin-r-5 "></i> Feb 12,1975, 41 years</span> </h5>
                
                <h5 class="space-m-t-0"><span class="med-green">Acc No: </span>45346</h5>
                <h5 class="space-m-t-0"><span class="med-green">SSN No: </span>634325673</h5>

            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 no-bottom  med-ts-separator"  style="margin-bottom: -8px;  border-color:#8ce5bb">

                <h5 class="no-margin"><span style=" padding: 3px 11px;color:#D98400">Pri: </span> &nbsp;Medicare - <span class="med-orange">$145</span></h5>
                <h5><span style=" padding: 3px 11px; color:#798C01">Sec:</span> Magna Health - <span class="med-orange">$218.5</span></h5>
                <h5><span style="padding: 3px 11px;  color:#0572A1">Tri:&nbsp; </span> Medicare - <span class="med-orange">$145</span></h5>
                <h5 ><span style="padding: 3px 11px;  color:#720294">Gua:</span>&nbsp;Patient Name - <span class="med-orange">$218.5</span></h5>
            </div>
              <div class="col-lg-2 col-md-6 col-sm-6 col-xs-12 form-horizontal tab-l-b-2 md-display" style="margin-bottom: -8px;">                                                                
                <div class="form-group-billing">                            
                    {!! Form::label('Claim No', 'Claim No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 ">
                       {!! Form::text('claim_no',@$claims->claim_number,['maxlength'=>'25','class'=>'form-control input-sm-header-billing','style'=>'border:none; background:#e5faf0;','readonly'=>'readonly']) !!}
                    </div>
                </div>
                
                <div class="form-group-billing" style="margin-top: -3px;">                            
                    {!! Form::label('Created at', 'Created', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-8">
                       {!! Form::text('created',date('m/d/Y'),['maxlength'=>'25','class'=>'form-control input-sm-header-billing','style'=>'border:none; background:#e5faf0; padding:0px;','readonly'=>'readonly']) !!}
                    </div>
                </div>
                
                <div class="form-group-billing"  style="margin-top: -3px;">                            
                    {!! Form::label('Batch No', 'Batch No', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 col-xs-8">
                       {!! Form::text('batch_no',(@$claims->batch_no)?@$claims->batch_no:App\Http\Helpers\Helpers::getRandonCharacter(),['maxlength'=>'25','class'=>'form-control input-sm-header-billing  batch_no no-padding','style'=>'border:none; background:#e5faf0;']) !!}
                    </div>
                </div>
                
                <div class="form-group-billing"  style="margin-top: -3px;">                            
                    {!! Form::label('Batch No', 'Batch Dt', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-4 control-label-billing med-green font600']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 col-xs-8">
                       {!! Form::text('batch_date',(@$claims->batch_date && $claims->batch_date !='0000-00-00 00:00:00')?date('m/d/Y', strtotime($claims->batch_date)):date('m/d/Y'),['maxlength'=>'25','class'=>'form-control input-sm-header-billing no-padding js_changebatch_date','style'=>'border:none; background:#e5faf0;']) !!}
                    </div>
                </div>                
                                
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 no-bottom med-left-border"  style="margin-bottom: -8px;  border-color:#8ce5bb">
                <h5 class="no-margin"><span class="med-green">Last Appt </span> <span class=" pull-right font12">Jan 29, 2015</span></h5>
                <h5><span class="med-green">Last Pat Payment </span> <span class=" pull-right font12">Jan 29, 2015</span></h5>
                <h5><span class="med-green">Last Statement </span> <span class="pull-right font12">Feb 16, 2015</span></h5>
                <h5><span class="med-green">Status </span><span class="pull-right font12">Active</span></h5>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>