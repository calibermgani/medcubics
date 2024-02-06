<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10" >
    <div class="box box-info no-shadow">
        <div class="box-block-header with-border">
            <i class="livicon" data-name="mail"></i> <h3 class="box-title">General Details</h3>

        </div><!-- /.box-header -->              
    </div>
</div>
<div class="box-body form-horizontal">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <?php $current_page = Route::getFacadeRoot()->current()->uri(); ?>	



        <div class="box-info no-shadow js-template">

            @if(isset($set_input_col->date))
            <?php $current_date = App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/Y'); ?>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">  
                    {!! Form::label('current_date', 'Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <i class="fa fa-calendar-o form-icon"></i> 	
                        {!! Form::text('currentdate',@$current_date,['id'=>'js_currentdate','class'=>'form-control dm-date form-cursor js_field_list',"data-key"=>$total_pair->date]) !!}
                    </div>                    
                </div>
            </div>
            @endif

            @if(isset($set_input_col->dosfrom))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">  
                    {!! Form::label('dosfrom', 'DOS From', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <i class="fa fa-calendar-o form-icon"></i> 	
                        {!! Form::text('dosfrom',NULL,['class'=>'form-control dm-date form-cursor js_field_list',"data-key"=>$total_pair->dosfrom,'id'=>'js_dosfrom','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                    </div>
                </div>
            </div>
            @endif       
            {!!Form::hidden('dosfrom')!!}  

            @if(isset($set_input_col->dosto))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">  
                    {!! Form::label('dosto', 'DOS To', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <i class="fa fa-calendar-o form-icon"></i> 	
                        {!! Form::text('dosto',NULL,['class'=>'form-control dm-date form-cursor js_field_list',"data-key"=>$total_pair->dosto,'id'=>'js_dosto','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                    </div>
                </div>
            </div>
            @endif
            {!!Form::hidden('dosto')!!}




            @if(isset($set_input_col->policyid))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('policy_id', 'Policy ID', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('policy_id', array(''=>'-- Select --')+(array)$input_arr->policyid,NULL,['class'=>'hide']) !!} 
                        {!! Form::text('policyid', NULL,['class'=>'js_show_inspolicy form-control js_field_list',"data-key"=>$total_pair->policyid,"readonly"=>"true"]) !!}   
                    </div>
                </div>
            </div>
            @endif



            @if(isset($set_input_col->emailaddress))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('emailaddress', 'Email', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::text('email',@$temp_pair->email,['id'=>'js_paybydate','class'=>'form-control js_field_list',"data-key"=>$total_pair->emailaddress]) !!}
                    </div>
                </div>
            </div>
            @endif
            @if(isset($set_input_col->subject))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('subject', 'Subject', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::text('subject',@$templates->name,['class'=>'form-control js_field_list',"data-key"=>$total_pair->subject,'maxlength'=>'100']) !!}
                    </div>
                </div>
            </div>
            @endif




            @if(isset($set_input_col->paybydate))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">  
                    {!! Form::label('duedate', 'Pay By Date', ['class'=>'col-lg-4 col-md-4 col-sm-4 control-label']) !!} 
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <i class="fa fa-calendar-o form-icon"></i> 	
                        {!! Form::text('pay_date',NULL,['class'=>'form-control dm-date form-cursor js_field_list',"data-key"=>$total_pair->paybydate,'id'=>'js_pay_date','placeholder'=>Config::get('siteconfigs.default_date_format')]) !!}
                    </div>
                </div>
            </div>
            @endif

            @if((isset($set_input_col->insurancename)) || (isset($set_input_col->insuranceaddress)) || (isset($set_input_col->policyid)))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('insurance name', 'Insurance Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('insurance_id', array(''=>'-- Select --')+(array)$input_arr->insurancename,NULL,['class'=>'select2 form-control js_change_type js_field_list ',"data-key"=>$total_pair->insurancename,"data-access"=>"insurance"]) !!} 
                    </div>
                </div>
            </div>
          
            @endif
              {!!Form::hidden('insurance_id')!!}
            <?php //dd($input_arr->insuranceaddress);?>
            @if(isset($set_input_col->insuranceaddress))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('insuranceadd', 'Insurance Address', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('insurance_add', (array)$input_arr->insuranceaddress,NULL,['class'=>'hide']) !!} 
                        {!! Form::textarea('insurance_addr', NULL,['class'=>'js_show_insaddr form-control ',"readonly"=>"true"]) !!}
                        {!! Form::text('insurance_addr', NULL,['class'=>'js_show_insaddr js_field_list hide',"data-key"=>$total_pair->insuranceaddress]) !!}   
                    </div>
                </div>
            </div>
            @endif

            @if(isset($set_input_col->message))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('message', 'Message', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::textarea('message',NULL,['id'=>'js_paybydate','class'=>'form-control js_field_list',"data-key"=>$total_pair->message]) !!}
                    </div>
                </div>
            </div>
            @endif
            @if((isset($set_input_col->renderingprovider)) || (isset($set_input_col->ein)) || (isset($set_input_col->npi)))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('Provider name', 'Provider Name', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('provider_id', array(''=>'-- Select --')+(array)$input_arr->renderingprovider,NULL,['class'=>'select2 form-control js_field_list js_change_type',"data-key"=>$total_pair->renderingprovider,"data-access"=>"provider"]) !!} 
                    </div>
                </div>
            </div>
            @endif
            @if(isset($set_input_col->ein))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('ein', 'ETIN Number', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('ein_number', array(''=>'-- Select --')+(array)$input_arr->ein,NULL,['class'=>'hide']) !!} 
                        {!! Form::text('ein', NULL,['class'=>'js_show_provider_ein form-control js_field_list',"data-key"=>$total_pair->ein,"readonly"=>"true"]) !!}   
                    </div>
                </div>
            </div>
            @endif
            @if(isset($set_input_col->npi))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('npi_number', 'NPI', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('npi_number', array(''=>'-- Select --')+(array)$input_arr->npi,NULL,['class'=>'hide']) !!} 
                        {!! Form::text('npi', NULL,['class'=>'js_show_provider_npi form-control js_field_list',"data-key"=>$total_pair->npi,"readonly"=>"true"]) !!}   
                    </div>
                </div>
            </div>
            @endif
            @if(isset($set_input_col->practicephonenumber))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('practicephonenumber', 'Practice Phone', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::text('practicephone',@$temp_pair->practicephonenumber,['class'=>' form-control dm-phone js_field_list',"data-key"=>$total_pair->practicephonenumber]) !!} 
                    </div>
                </div>
            </div>
            @endif

            @if(isset($set_input_col->practicefaxnumber))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('practicefaxnumber', 'Practice Fax', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::text('practicefax', @$temp_pair->practicefaxnumber,['class'=>'form-control dm-phone js_field_list',"data-key"=>$total_pair->practicefaxnumber]) !!} 
                    </div>
                </div>
            </div>
            @endif
            @if(isset($set_input_col->claimnumber))
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">   
                <div class="form-group">
                    {!! Form::label('claims', 'Select Claim', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}                                 
                    <div class="col-lg-6 col-md-8 col-sm-5 col-xs-10">  
                        {!! Form::select('claims', (array)$input_arr->claims,null,['class'=>'select2 form-control  js_field_list js_multi_claim_detail',"data-key"=>$total_pair->claimnumber,'multiple'=>'multiple','autocomplete'=>'off']) !!} 
                    </div>
                </div>
            </div>

            @endif
            {!!Form::hidden('claim_number')!!}
        </div>

        <?php //echo htmlspecialchars_decode($templates->content);?>

        <div class="col-lg-11 col-md-11 l-green-b bg-white margin-t-20" style="padding: 20px;">
            <?php echo htmlspecialchars_decode($templates->content);?>

        </div>

        <div class="col-lg-12 col-md-12  col-sm-12 col-xs-12 text-center">
            {!! Form::submit("Generate", ['name'=>'send','class'=>'btn btn-medcubics form-group js_generate', 'id' => 'js_template_send']) !!}&emsp;&nbsp;	
            <a href="javascript:void(0)" data-url="{{url('patients/'.$patient_id.'/correspondence')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics js_cancel_site']) !!}</a>
        </div>

        <input type="hidden" class="js_content" value="{{ $templates->content}}" />
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding js_set_content hide"><?php echo $templates->content ?>
        </div>
    </div>
</div>