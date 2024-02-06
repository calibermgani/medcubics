

            <div class="box box-view no-shadow no-border"><!--  Box Starts -->
                <div class="box-body form-horizontal no-padding">                        
                    <div class="col-md-12 no-padding"><!-- Inner Content for full width Starts -->
                       <div class="box-body-block" style="background: #f0f0f0;" ><!--Background color for Inner Content Starts -->
                           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding" style="border-color:#deedaa;margin-top:4px;"><!-- General Details Full width Starts -->                           
                              <div class="box no-border  no-shadow" ><!-- Box Starts -->
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="background: #f0f0f0"><!--  1st Content Starts -->
                                    {!! Form::open(['url'=>'charges/batch/create','id' => 'js-batch-charge']) !!}
                                    <div class="box-body form-horizontal no-padding"><!-- Box Body Starts -->                                           
                                        <div class="form-group-billing">
                                            {!! Form::label('claim', 'Created Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">     
                                                <i class="fa fa-calendar-o form-icon-billing font12" onclick= "iconclick('created_at')"></i>
                                                {!! Form::text('created_at',date('m/d/Y'),['maxlength'=>'25','class'=>'form-control input-sm', 'readonly' => 'readonly']) !!}
                                            </div>                                   
                                        </div>
                                        
                                        <div class="form-group-billing">
                                            {!! Form::label('claim', 'Batch Date', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">
                                                <i class="fa fa-calendar-o form-icon-billing font12" onclick= "iconclick('batch_date')"></i>
                                                {!! Form::text('batch_date',date('m/d/Y'),['maxlength'=>'25','class'=>'form-control input-sm js-auth_datepicker']) !!}
                                            </div>                                   
                                        </div>
                                        
                                        <div class="form-group-billing">
                                            {!! Form::label('claim', 'Batch No', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                    
                                                {!! Form::text('batch_no',App\Http\Helpers\Helpers::getRandonCharacter(),['maxlength'=>'25','class'=>'form-control input-sm']) !!}
                                            </div>                                   
                                        </div>
                                                                                                
                                        <div class="form-group-billing">
                                            {!! Form::label('No of Claims', 'No of Claims', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                    
                                                {!! Form::text('no_of_claims',null,['maxlength'=>'25','class'=>'form-control input-sm']) !!}
                                            </div>                                   
                                        </div>
                                                                                               
                                        <div class="form-group-billing">
                                            {!! Form::label('Reference No', 'Reference No', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                {!! Form::text('reference_no',null,['maxlength'=>'20','class'=>'form-control input-sm']) !!}
                                            </div>                                   
                                        </div>
                                        <div class="form-group-billing">
                                            {!! Form::label('Billing Provider', 'Billing Provider', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                {!! Form::select('billing_provider_id',array('--' => '-- Select --')+(array)$billing_providers,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}  
                                            </div>                                   
                                        </div>
                                         <div class="form-group-billing">
                                            {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                {!! Form::select('facility_id',array('--' => '-- Select --')+(array)$facilities,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}  
                                            </div>                                   
                                        </div>
                                         <div class="form-group-billing">
                                            {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-5 col-md-5 col-sm-6 col-xs-12 control-label-billing med-green','style'=>'font-weight:600;']) !!}                                                  
                                            <div class="col-lg-7 col-md-7 col-sm-6 col-xs-10">                                   
                                                {!! Form::select('rendering_provider_id',array('--' => '-- Select --')+(array)$rendering_providers,null,['class'=>'form-control select2 input-sm-modal-billing']) !!}  
                                            </div>                                   
                                        </div>                                            
                                    </div><!-- /.box-body Ends-->
                                    {!! Form::submit('Save', ['class'=>'btn btn-medcubics','style'=>'padding:2px 40px;']) !!}
                                    {!! Form::close() !!}
                                </div><!--  1st Content Ends -->                            
                            </div><!--  Box Ends -->
                     
                      </div><!-- General Details Full width Ends -->
                 </div><!-- Inner Content for full width Ends -->
              </div><!--Background color for Inner Content Ends -->                        
           <a href="javascript:void(0)"><span class="pull-right js-create-batch" style="font-size: 14px; margin-top: 5px;  margin-right: 20px; margin-bottom: 7px; border-radius: 4px; border:1px solid #00877f; padding: 0px 10px 0px 10px; cursor: pointer;">Create Batch</span></a>

<script type="text/javascript">
 $(document).ready(function () {
    var BillingformData = $('form#js-batch-charge').serialize();  
       $('.js-create-batch').click(function(){
          $.ajax({
           type: "POST",
           url: $('#js-batch-charge').attr('action'),
           data: $('form#js-batch-charge').serialize(), // serializes the form's elements.
           success: function(data)
           {
             console.log(data);
           }
       });
       });
    $(function() {
              $(".js-auth_datepicker").datepicker({
                  minDate: -20,
                  maxDate: "+1M +10D",
                  changeMonth: true,
                  changeYear: true,             
              });
          });
    });
</script>
