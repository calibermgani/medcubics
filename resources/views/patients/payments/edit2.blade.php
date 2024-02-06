@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Edit Billing</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('contactdetail') }}" ><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
            <li><a href="javascript:void(0);" class="js-print"><i class="fa fa-print" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li>
            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
@include ('patients/billing/tabs') 
@stop

@section('practice')
<div class="col-md-12" style="margin-top: -13px; padding-top: 0px;"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border no-padding"><!-- General Details Full width Starts -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding"><!-- Only general details content starts -->
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="background: #fbf1fe"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding" style="margin-top: -10px;">
                            <span class="bg-white">General Details</span>
                        </div>
                        <div class="box-body form-horizontal"><!-- Box Body Starts -->
                            <div class="form-group-billing">
                                {!! Form::label('Rendering Provider', 'Rendering Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-red']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10   @if($errors->first('rendering_provider_id')) error @endif">
                                    {!! Form::text('rendering_provider_id','Carey, Mariah E',['class'=>'form-control input-sm-header-billing no-border','readonly'=>'readonly','style'=>'background:#fbf1fe !important;']) !!}  
                                    {!! $errors->first('rendering_provider_id', '<p> :message</p>')  !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                <i class="fa fa-comments med-green form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul>
                                                <li><span>Name</span> : Laura, Madhava</li>
                                                <li><span>EIN</span> : 45343455</li>
                                                <li><span>NPI</span> : 1245319599</li>
                                                <li><span>Speciality</span> : Air Transport</li>
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Referring Provider', 'Referring Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-green']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 billing-select2-disabled-no-border @if($errors->first('referring_provider_id')) error @endif">
                                    {!! Form::select('referring_provider_id', array('' => 'James, Allen') + (array)$referring_providers,  $referring_provider_id,['class'=>'select2 no-border form-control','style'=>'border:0px solid #ccc;']) !!}  
                                    {!! $errors->first('referring_provider_id', '<p> :message</p>')  !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                    <!-- Popup Starts -->
                                    
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                <i class="fa fa-plus-circle med-green form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul>
                                                <li><span>Name</span> : Willams</li>
                                                <li><span>EIN</span> : 56534584</li>
                                                <li><span>NPI</span> : 1245319599</li>
                                                <li><span>Speciality</span> : Acute Care</li>
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Billing Provider', 'Billing Provider', ['class'=>'col-lg-5 col-md-5 col-sm-4 control-label-billing med-red']) !!} 
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 @if($errors->first('billing_provider_id')) error @endif">
                                    {!! Form::text('billing_provider_id','Martin, Lu K',['class'=>'form-control input-sm-header-billing no-border ','readonly'=>'readonly','style'=>'background:#fbf1fe']) !!}  
                                    {!! $errors->first('billing_provider_id', '<p> :message</p>')  !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">

                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                <i class="fa fa-comments med-green form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul>
                                                <li><span>Name</span> : David George</li>
                                                <li><span>EIN</span> : 45343455</li>
                                                <li><span>NPI</span> : 1245319599</li>
                                                <li>1001 W Fayette St, Syracuse - NY, 77386 - 2859</li>
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->                             

                                </div>
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Facility', 'Facility', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-red']) !!}                                                  
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 billing-select2-disabled @if($errors->first('facility_id')) error @endif">  
                                    {!! Form::text('facility_id','Mercy Medical Center',['class'=>'form-control input-sm-header-billing no-border','readonly'=>'readonly','style'=>'background:#fbf1fe']) !!}   
                                    {!! $errors->first('facility_id', '<p> :message</p>')  !!}
                                </div>      
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                <i class="fa fa-comments med-green form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul>
                                                <li><span>Facility</span> : Mercy Medical Center</li>                                            
                                                <li>1001 W Fayette St, Syracuse - NY, 77386 - 2859</li>
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>                            
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Billed To', 'Billed To', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-green']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10 billing-select2-disabled-no-border">  
                                    {!! Form::select('insurance_id', (array)$insurances,  $insurance_id,['class'=>'select2 form-control','id'=>'insurance_id']) !!}
                                </div>  
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                    <!-- Popup Starts -->
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                <i class="fa fa-comments med-green form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul >
                                                <li><span>Insurance</span> : Kaiser Permanente</li>
                                                <li><span>Type</span> : Asterix</li>
                                                <li><span>Policy ID</span> : 1245319599</li>
                                                <li><span>Claim Type</span> : Electronic</li>
                                                <li>2101 East Jefferson Stree, Maryland - MY, 20852 - 1232</li>
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Popup Ends -->
                                </div>
                            </div>

                            <div class="form-group-billing" style="margin-bottom:7px;">
                                {!! Form::label('authorization', 'Authorization', ['class'=>'col-lg-5 col-md-5 col-sm-4 col-xs-12 control-label-billing med-red']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">  
                                    {!! Form::text('authorization','42432',['maxlength'=>'25','class'=>'form-control input-sm-header-billing no-border','style'=>'background:#fbf1fe;','readonly'=>'readonly']) !!}
                                </div>   
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2"><a href="#auth" data-toggle="modal" data-target="#auth"><i class="fa fa-comments med-green form-icon-billing"></i></a></div>
                            </div>

                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->

                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 no-padding" style="background: #fffbeb; border-left: 1px solid #f0f0f0;"><!--  2nd Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green no-padding" style="margin-top: -10px;">&emsp; </div>

                        <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">


                            <div class="form-group-billing">
                                {!! Form::label('pos', 'Patient Type',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label-billing med-red']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-yellow-no-border">
                                    {!! Form::select('insurance_id', array(''=>'Patient ')+(array)$insurances,  $insurance_id,['class'=>'select2 form-control','id'=>'insurance_id']) !!}
                                </div>                                                     
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('pos', 'Bill Cycle',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label-billing med-red']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::text('employer','H - M',['class'=>' form-control input-sm-header-billing no-border','style'=>'background:#fffbeb;','readonly'=>'readonly']) !!}
                                </div>                                                       
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('pos', 'Employer',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label-billing med-green']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                    {!! Form::text('employer','Victoria Bellot',['class'=>' form-control input-sm-header-billing no-border','style'=>'background:#fffbeb;']) !!}
                                </div>    
                                <div class="col-lg-1 col-md-1 col-sm-2 col-xs-2">
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                <i class="fa fa-plus-circle med-green form-icon-billing"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul>
                                                <li><span>Name</span> : Willams</li>
                                                <li><span>EIN</span> : 56534584</li>
                                                <li><span>NPI</span> : 1245319599</li>
                                                <li><span>Speciality</span> : Acute Care</li>
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('pos', 'POS',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label-billing med-red']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                                   {!! Form::text('employer','Ambulance',['class'=>' form-control input-sm-header-billing no-border','style'=>'background:#fffbeb;','readonly'=>'readonly']) !!}
                                </div>                                                       
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('Copay', 'Co-Pay',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label-billing med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10 billing-select2-disabled-yellow-no-border">
                                    {!! Form::select('employer_id',array('Cash'=>'Cash','Check'=>'Check','Credit Card'=>'Credit','Others'=>'Others'),null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
                                </div>
                                {!! Form::label('pos', 'Amt',  ['class'=>'col-lg-1 col-md-1 col-sm-4 col-xs-12 control-label-billing med-green','style'=>'padding-left:0px;']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">                                                                        
                                    {!! Form::text('doi',350000,['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#fffbeb;']) !!}                    
                                </div>    
                            </div>

                            <div class="form-group-billing" style="margin-bottom:6px;">
                                {!! Form::label('mode', 'Details',  ['class'=>'col-lg-4 col-md-4 col-sm-5 col-xs-12 control-label-billing med-green']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2">
                                    {!! Form::text('copay','General Details',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#fffbeb;']) !!}
                                </div>                          
                            </div>

                        </div><!-- /.box-body -->
                    </div><!--  2nd Content Ends -->                  
                    
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 left-border" style="background: #eeffff; "><!-- ICD Details Starts here -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange  no-padding" style="margin-top: -10px;">
                            <span class="bg-white">Diagnosis - ICD 10</span>
                        </div>
                        <div class="box-body form-horizontal">

                            <div class="form-group-billing">                            
                                {!! Form::label('DOI', '1',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('doi','G44.81',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>


                                {!! Form::label('Billed Date', '7',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('billed_date','O89.4',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>
                            </div>

                            <div class="form-group-billing">                            
                                {!! Form::label('DOI', '2',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('doi','G44.009',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                                {!! Form::label('Billed Date', '8',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('billed_date','G44.52',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>     
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>
                            </div>

                            <div class="form-group-billing">                            
                                {!! Form::label('DOI', '3',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('doi','O29.43',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                                {!! Form::label('Billed Date', '9',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('billed_date','G44.029',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>          
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                            </div>

                            <div class="form-group-billing">                            
                                {!! Form::label('DOI', '4',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('doi','Z85.020',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                                {!! Form::label('Billed Date', '10',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('billed_date','D3A.092',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>      
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                            </div>

                            <div class="form-group-billing">                            
                                {!! Form::label('DOI', '5',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('doi','K31.819',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                                {!! Form::label('Billed Date', '11',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('billed_date','A96.1',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>                   
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                            </div>

                            <div class="form-group-billing margin-b-5">                            
                                {!! Form::label('DOI', '6',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('doi','R50.82',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                                {!! Form::label('Billed Date', '12',  ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-12 control-label med-green']) !!} 
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                                    {!! Form::text('billed_date','A01.09',['class'=>'form-control input-sm-header-billing no-border','style'=>'background:#eeffff;']) !!}
                                </div>     
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                                    <i class="fa fa-info med-green form-icon-billing"></i>
                                </div>

                            </div>
                        </div>
                    </div>
                </div><!--  Box Ends -->
            </div><!-- Only general details Content Ends -->

            

        </div><!-- General Details Full width Ends -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">

                <div class="form-group-billing margin-t-8">       
                    <div class="col-lg-1 col-md-2 col-sm-2 med-orange no-padding">Alert</div>

                    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                        {!! Form::text('doi',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Alert Message']) !!}
                    </div>
                    {!! Form::label('mode', 'DOI',  ['class'=>'col-lg-1 col-md-1 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                    <div class="col-lg-2 col-md-2 col-sm-5 col-xs-10">
                        {!! Form::text('doi',null,['class'=>'form-control input-sm-modal-billing','id'=>'date_of_birth','readonly'=>'readonly']) !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pull-right no-padding">
                <div class="form-group margin-t-8">                            
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <div class="col-lg-4 col-md-1 col-sm-2 col-xs-12 med-orange no-padding">Anesthesia</div>


                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-10">
                            {!! Form::text('doi',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Start']) !!}
                        </div>


                        <div class="col-lg-3 col-md-3 col-sm-2 col-xs-10">
                            {!! Form::text('billed_date',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Stop']) !!}
                        </div>


                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-10 no-padding">
                            {!! Form::text('billed_date',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Units']) !!}                    
                        </div>   
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-8">                            
            <ul class="billing" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                <li class="billing-grid">
                    <table class="table-billing-view" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 3%;">&emsp;</th>
                                <th style="text-align: center; width: 6%;">From</th>
                                <th style="text-align: center;width: 6%">To</th>                                
                                <th style="text-align: center; width: 8%">CPT</th>
                                <th style="text-align: center; width: 4%">M 1</th>
                                <th style="text-align: center; width: 4%">M 2</th>
                                <th style="text-align: center; width: 4%">M 3</th>    
                                <th style="text-align: center; width: 4%">M 4</th>  
                                <th style="text-align: center; width: 18%">ICD Pointers</th>
                                <th style="text-align: center; width: 5%">Units</th>
                                <th style="text-align: center; width: 6%">Charges</th>
                            </tr>
                        </thead>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%">{!! Form::text('pointers',null,['data-inputmask'=>'"mask": "99|99|99|99|99|99|99|99|99|99|99|99"' ,'data-mask','style'=>'border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;']) !!}</td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%">{!! Form::text('pointers',null,['data-inputmask'=>'"mask": "99,99,99,99,99,99,99,99,99,99,99,99"' ,'data-mask','style'=>'border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;']) !!}</td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>



                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 8%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 4%">{!! Form::select('preferred_communication', ['sd'=>'--','12122' => '0A','67876' => '2E','J5645' => '0H','67758'=>'2L'],null,['class'=>'select2 input-sm form-control']) !!}</td>
                                <td style="text-align: center; width: 18%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 5%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                              
                            </tr>
                        </tbody>
                    </table>                                     
                </li>                
            </ul>
            <div class="pull-right" style="margin-top: -8px;"> 
            <span class=" med-green" >Total Charges : </span>
            <span class=" med-green">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; </span>
            </div>
        </div>            
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->
  <div id = "demo" class="collapse out col-md-12 space10"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border">            
                <div class="box no-border  no-shadow"><!-- Box Starts -->
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <button type = "button" class = "btn-min btn-success pull-right no-border" style="background: #fff; margin-top: -10px; color:#00877f; line-height: 10px;"data-toggle = "collapse" data-target = "#demo">Minimize <i class="fa fa-caret-down med-orange"></i></button>
            
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 " style="background: #fff"><!--  1st Content Starts -->
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding" style="margin-top: -20px;">
                            <span class="bg-white">Payment Details</span>
                            
                        </div>
                        <div class="box-body form-horizontal no-padding"><!-- Box Body Starts -->
                            
                            <ul class="billing" style="list-style-type:none; padding:0px; line-height:26px; border:none;  border-radius:4px;" id="">
                                <li class="billing-grid" style="border:none;">
                                    <table class="table-billing-view" style="width: 100%;">
                                        <thead>
                                            <tr>

                                                <th style="text-align: center; background: #e6fcfb; color:#00877f; font-weight: 600; width: 13%;">Who Paid</th>                                                
                                                <th style="text-align: center; background: #e6fcfb; color:#00877f; font-weight: 600; width: 7%">Posting Date</th>                                
                                                <th style="text-align: center; background: #e6fcfb; color:#00877f; font-weight: 600; width: 7%">Check Date</th>
                                                <th style="text-align: center; background: #e6fcfb; color:#00877f; font-weight: 600; width: 10%">Check No</th>
                                                <th style="text-align: center; background: #e6fcfb; color:#00877f; font-weight: 600; width: 7%">Deposit Date</th>                               
                                                <th style="text-align: center; background: #e6fcfb; color:#00877f; font-weight: 600; width: 7%">Ref No</th>                                                                                           
                                            </tr>
                                        </thead>
                                    </table>                                     
                                </li>

                                <li class="billing-grid no-border">
                                    <table class="table-billing-view superbill-claim">
                                        <tbody>
                                            <tr>

                                                <td style="text-align: center; width: 13%;"><input type="text" value="Peter, Thomas" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                                <td style="text-align: center; width: 7%;"><input type="text" value="12-21-2015" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                   
                                                <td style="text-align: center; width: 7%;"> <input type="text" value="01-15-2016" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>  
                                                <td style="text-align: center; width: 10%"><input type="text" value="34545 454 8676" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>
                                                <td style="text-align: center; width: 7%"><input type="text" value="12-29-2015" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                
                                                <td style="text-align: center; width: 7%"><input type="text" value="3434535" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                                                                      
                                            </tr>
                                        </tbody>
                                    </table>                                     
                                </li>

                                <li class="billing-grid">
                                    <table class="table-billing-view superbill-claim">
                                        <tbody>
                                            <tr>                                 
                                                <td style="text-align: center; width: 13%;"><input type="text" value="Baker, Russell S" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                                <td style="text-align: center; width: 7%;"><input type="text" value="1-09-2016" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                                <td style="text-align: center; width: 7%;"> <input type="text" value="1-20-2016" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                                <td style="text-align: center; width: 10%"><input type="text" value="86744 647 2327" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>
                                                <td style="text-align: center; width: 7%"><input type="text" value="1-22-2016" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>                                
                                                <td style="text-align: center; width: 7%; border-bottom: 1px solid #646464;"><input type="text" value="68567545" style="border: 0px solid #e8e8e8; background: #fff; width: 100%; border-radius: 4px; text-align: center;"></td>

                                            </tr>
                                        </tbody>
                                    </table>                                     
                                </li>                              

                                <li class="billing-grid">
                                    <table class="table-billing-view superbill-claim">
                                        <tbody>
                                            <tr>                               
                                                <td style="text-align: center; width: 13%;"><input type="text" value="Brian, Jose" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                             
                                                <td style="text-align: center; width: 7%;"><input type="text" value="11-18-2015" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                   
                                                <td style="text-align: center; width: 7%;"> <input type="text" value="12-21-2015" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>  
                                                <td style="text-align: center; width: 10%"><input type="text" value="74565 232 6453" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>
                                                <td style="text-align: center; width: 7%"><input type="text" value="1-20-2016" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                
                                                <td style="text-align: center; width: 7%"><input type="text" value="6546565" style="border: 0px solid #e8e8e8; width: 100%; background: #fff; border-radius: 4px; text-align: center;"></td>                                                                                    
                                            </tr>
                                        </tbody>
                                    </table>                                     
                                </li>

                                <li class="billing-grid" style="border-bottom: 1px dotted #85e2e6;">
                                    <table class="table-billing-view superbill-claim">
                                        <tbody>
                                            <tr>                              
                                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%;background: #fff; border-radius: 4px; text-align: center;"></td>                                             
                                                <td style="text-align: center; width: 7%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%;background: #fff; border-radius: 4px; text-align: center;"></td>                                   
                                                <td style="text-align: center; width: 7%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%;background: #fff; border-radius: 4px; text-align: center;"></td>  
                                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%;background: #fff; border-radius: 4px; text-align: center;"></td>
                                                <td style="text-align: center; width: 7%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%;background: #fff; border-radius: 4px; text-align: center;"></td>                                
                                                <td style="text-align: center; width: 7%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%;background: #fff; border-radius: 4px; text-align: center;"></td>                                
                                            </tr>
                                        </tbody>
                                    </table>                                     
                                </li>
                            </ul>

                            
                        </div><!-- /.box-body Ends-->
                    </div><!--  1st Content Ends -->

                     <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="background: #f7fce6;  border-left: 1px solid #f0f0f0;"><!--  2nd Content Starts -->
                      
                        <div class="box-body form-horizontal js-address-class" id="js-address-primary-address">


                            <div class="form-group-billing">
                                {!! Form::label('pos', 'Patient Type',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green ']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-green-no-border">
                                    {!! Form::select('insurance_id',(array)$insurances,  $insurance_id,['class'=>'select2 form-control','id'=>'insurance_id']) !!}
                                </div>                                                     
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('pos', 'Bill Cycle',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-green-no-border">
                                    {!! Form::select('insurance_id',array(''=>'H - S')+ (array)$insurances,  $insurance_id,['class'=>'select2 form-control']) !!}
                                </div>                                                       
                            </div>

                            <div class="form-group-billing">
                                {!! Form::label('pos', 'Employer',  ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label-billing med-green']) !!} 
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 billing-select2-disabled-green-no-border">
                                    {!! Form::select('insurance_id', array(''=>'Victoria')+(array)$insurances,  $insurance_id,['class'=>'select2 form-control']) !!}
                                </div> 
                                <div class="col-lg-1">
                                    
                                    <div class="dropdown user user-menu">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            <span class="hidden-xs">
                                                
                                            </span>
                                        </a>
                                        <div class="dropdown-menu1">
                                            <ul>
                                                <li><span>Name</span> : Willams</li>
                                                <li>1001 W Fayette St, Syracuse - NY, 77386 - 2859</li>                                                
                                                <li> <a href="" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group-billing">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">                       
									<div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
										<input type="checkbox" class="flat-red"> Hold
									</div>

									<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12 billing-select2-disabled-green-no-border">
										{!! Form::select('employer_id',array(''=>'Reason','Cash'=>'Cash','Check'=>'Check','Credit Card'=>'Credit','Others'=>'Others'),null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
									</div>
                                   <div class="col-lg-1 col-md-1 col-sm-1">
                                       <i class="fa fa-plus-circle med-green"></i>
                                   </div>
								</div>                                                       
                            </div>
                        </div><!-- /.box-body -->
                    </div><!--  2nd Content Ends -->
                </div><!--  Box Ends -->
            </div>           
        </div>     

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space10 no-padding">                            
            <ul class="billing" style="list-style-type:none; padding:0px; line-height:26px; border: 1px solid #85e2e6; border-radius:4px;" id="">
                <li class="billing-grid">
                    <table class="table-billing-view" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 3%;">&emsp;</th>
                                <th style="text-align: center; width: 13%;">Insurance</th>                                                
                                <th style="text-align: center;width: 6%">DOS</th>                                
                                <th style="text-align: center; width: 6%">CPT</th>
                                <th style="text-align: center; width: 6%">Billed</th>
                                <th style="text-align: center; width: 6%">Allowed</th>                               
                                <th style="text-align: center; width: 6%">Paid</th>
                                <th style="text-align: center; width: 6%">Co-Ins</th>
                                <th style="text-align: center; width: 6%">Co-Pay</th>
                                <th style="text-align: center; width: 6%">Deductible</th>
                                <th style="text-align: center; width: 6%">With hold</th>
                                <th style="text-align: center; width: 10%">Adjs</th>
                                <th style="text-align: center; width: 6%">Status</th>                                                               
                            </tr>
                        </thead>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-danger">Denied</span></td>                                                                                         
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                
                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-danger">Denied</span></td>                                                                                         
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                
                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-danger">Denied</span></td>                                                                                         
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                
                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-progress tees">P. Paid</span></td>                         
                                
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                
                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-danger tees">Denied</span></td>                         
                               
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
                
                

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-success" style="padding: 1px 14px;"> Billed</span></td>                         
                                
                            </tr>
                        </tbody>
                    </table>                                     
                </li>

                <li class="billing-grid">
                    <table class="table-billing-view superbill-claim">
                        <tbody>
                            <tr>
                                <td style="text-align: center; width: 3%;"><input type="checkbox" class=""></td>  
                                <td style="text-align: center; width: 13%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                             
                                <td style="text-align: center; width: 6%;"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>                                   
                                <td style="text-align: center; width: 6%;"> <input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>  
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td>
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 10%"><input type="text" class="" style="border: 0px solid #e8e8e8; width: 100%; border-radius: 4px; text-align: center;"></td> 
                                <td style="text-align: center; width: 6%"><span class="patient-billing-bg label-success" style="padding: 1px 14px;">Billed</span></td>                         
                                
                            </tr>
                        </tbody>
                    </table>                                     
                </li>
            </ul>
            
            <div class="pull-right" style="margin-top: -8px; margin-bottom: 5px;"> 
            <span class=" med-green" >Total Charges : </span>
            <span class=" med-green">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; </span>
            </div>
            
        </div>
        
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                {!! Form::text('doi',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Notes']) !!}
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">&emsp;</div>
           
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 billing-select2 no-padding pull-right">
                {!! Form::select('employer_id',array(''=>'--','Cash'=>'Cash','Check'=>'Check','Credit Card'=>'Credit','Others'=>'Others'),null,['class'=>'form-control select2 input-sm-modal-billing']) !!}
            </div>
        </div>           
       
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->

<div class="col-md-12 space10"><!-- Inner Content for full width Starts -->
    <div class="box-body-block"><!--Background color for Inner Content Starts -->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="btn-group">
                <button type="button" class="btn-min btn-success" style="background: #fff; border:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#additional"> Claim Details</a></button>
                 <button type="button" class="btn-min btn-success" style="background: #fff; border-top:0px solid #ccc; border-bottom:0px solid #ccc; "><a href="#others" data-toggle="modal" data-target="#ambulance"> Ambulance Billing</a></button>
                <button type="button" class="btn-min btn-success" style="background: #fff; border-right:0px solid #ccc;  border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#others" > Other Details</a></button>
        </div>
            <div class="btn-group pull-right">
                <button type = "button" class = "btn-min btn-success" style="background: #fff; border:0px solid #ccc; color:#00877f;"data-toggle = "collapse" data-target = "#demo"> Payments</button>
                <button type="button" class="btn-min btn-success" style="background: #fff; border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#transaction_details"> Transactions</a></button>
                <button type="button" class="btn-min btn-success" style="background: #fff; border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#others" > Workbench</a></button>
                <button type="button" class="btn-min btn-success" style="background: #fff; border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#others" > CMS 1500</a></button>
                <button type="button" class="btn-min btn-success" style="background: #fff; border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#others" > Re-Submit</a></button>
                <button type="button" class="btn-min btn-success" style="background: #fff; border-right:0px solid #ccc;  border-top:0px solid #ccc; border-bottom:0px solid #ccc;" ><a href="#others" data-toggle="modal" data-target="#others" > Submit</a></button>      
            </div>
            
        </div>
        <div class="box-footer space20">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 space20">
                {!! Form::submit('Save', ['class'=>'btn btn-medcubics']) !!}
                <a href="{{ url('contactdetail')}}">{!! Form::button('Cancel', ['class'=>'btn btn-medcubics']) !!}</a>

            </div>
        </div><!-- /.box-footer -->
    </div><!-- Inner Content for full width Ends -->
</div><!--Background color for Inner Content Ends -->

<div id="additional" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Claim Details</h4>
            </div>
            <div class="modal-body">
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-top:1px solid #f0f0f0; padding: 10px;">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding space-m-t-22">
                                <span class="bg-white">Associations</span>
                            </div>
                            <div class="form-group-billing">                             
                                {!! Form::label('employer_id', 'Employer', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::select('employer_id',array(''=>'-- Select --','Employer'=>'Employer 1','Employer 1'=>'Employer 2','Employer 1'=>'Employer 3'),null,['class'=>'form-control input-sm-modal-billing']) !!}                                          
                                </div>                        
                                <div class="col-sm-1">
                                    <i class="fa fa-comments med-green form-icon-billing" style=""></i>
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('attorney_id', 'Attorney', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::select('attorney_id',array(''=>'-- Select --','Attorney'=>'Attorney 1','Attorney 1'=>'Attorney 2','Attorney 1'=>'Attorney 3'),null,['class'=>'form-control input-sm-modal-billing']) !!}                                           
                                </div>                        
                                <div class="col-sm-1">
                                    <i class="fa fa-comments med-green form-icon-billing" style=""></i>
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('facilitymrn_id', 'Facility MRN', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::select('facilitymrn_id',array(''=>'-- Select --','Facility'=>'Facility 1','Facility 1'=>'Facility 2','Facility 1'=>'Facility 3'),null,['class'=>'form-control input-sm-modal-billing']) !!}                                           
                                </div>                        
                                <div class="col-sm-1">
                                    <i class="fa fa-comments med-green form-icon-billing" style=""></i>
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('primary_care_physician_id', 'Primary Care Physician', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::select('primary_care_physician_id',array(''=>'-- Select --','Provider'=>'Provider 1','Provider 1'=>'Provider 2','Provider 1'=>'Provider 3'),null,['class'=>'form-control input-sm-modal-billing']) !!}                                           
                                </div>                        
                                <div class="col-sm-1">
                                    <i class="fa fa-comments med-green form-icon-billing" style=""></i>
                                </div>
                            </div>
                                                                                                             
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-top:1px solid #f0f0f0; padding: 10px;">

                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding space-m-t-22">
                                <span class="bg-white">Illness and Accident Info</span>
                            </div>
                            <div class="form-group-billing">                             
                                {!! Form::label('', 'Provider employed in hospice?', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                     
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Box 8', 'Reserved for NUCC use (Box 8)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box_8',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Box 9B', 'Reserved for NUCC use (Box 9B)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box_9b',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Box 9c', 'Reserved for NUCC use (Box 9C)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box_9c',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group-billing">                             
                                {!! Form::label('Employment (Box 10A)', 'Employment (Box 10A)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                     
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Auto Accident', 'Auto Accident (Box 10B) / State', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                    
                                </div>        
                                
                                <div class="col-lg-2 col-md-2 col-sm-2">{!! Form::text('employer_id',null,['class'=>'form-control input-sm-modal-billing','maxlength'=>'2']) !!}   </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Other Accident', 'Other Accident (Box 10C)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                    
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>

                            <div class="form-group-billing">                             
                                {!! Form::label('Claim Codes', 'Claim Codes (Box 10D)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                    
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Other Claim ID (Box 11b)', 'Other Claim ID (Box 11b)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box11b',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('box12', 'Print Signature on File (Box 12)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                     
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('box13', 'Print Signature on File (Box 13)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                     
                                </div>                                                        
                            </div>
                            
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Current Illness/Injury/Pregnancy LMP (Box 14)', 'Illness/Injury/Pregnancy LMP (Box 14)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box14',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Other Date (Box 15)', 'Other Date (Box 15)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box15',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Unable to Work', 'Unable to Work: From-To (Box 16)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5">
                                   {!! Form::text('employer_id',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>        
                                                
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 ">
                                    {!! Form::text('employer_id',null,['class'=>'form-control input-sm-modal-billing']) !!}   
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Hospitalization', 'Hospitalization: From-To (Box 18)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 ">
                                   {!! Form::text('hospitalization',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>        
                                       
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-5 ">
                                    {!! Form::text('employer_id',null,['class'=>'form-control input-sm-modal-billing']) !!}   
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Additional Claim Info (Box 19)', 'Additional Claim Info (Box 19)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box19',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>    
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Outside Lab (Box 20)', 'Outside Lab (Box 20)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::radio('status', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                     
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Resubmission Code (Box 22)', 'Resubmission Code (Box 22)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box22',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Emergency (Box 24 C)', 'Emergency (Box 24C)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box24c',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Accept Assignments (Box 27)', 'Accept Assignments (Box 27)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::radio('box27', 'Yes',null,['class'=>'flat-red']) !!} Yes &emsp; {!! Form::radio('status', 'No',null,['class'=>'flat-red']) !!} No                                     
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Reserved for NUCC use (Box 30)', 'Reserved for NUCC use (Box 30)', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label-billing']) !!}
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-10">
                                    {!! Form::text('box30',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                                                        
                            </div>                           
                            
                        </div>
                     
                    </div><!-- /.box-body -->                                
                </div><!-- /.box Ends Contact Details-->

            </div>
			<div class="modal-footer">
				<input class="btn btn-medcubics js-submit-btn" type="submit" value="Save">
				<button class="btn btn-medcubics" data-dismiss="modal" type="button">Cancel</button>
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->    


<div id="auth" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Authorization</h4>
            </div>
            <div class="modal-body">
               <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
							<div class="box box-view no-shadow " style="border: 1px solid #85E2E6">
								<div class="box-header-view">
									<i class="livicon" data-name="responsive-menu"></i> <h3 class="box-title">Authorization Details</h3>
									<div class="box-tools pull-right">
										<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
									</div>
								</div><!-- /.box-header -->
								<div class="box-body table-responsive">
									<table id="" class="table table-borderless table-striped" style="border-collapse: separate">	
										<thead>
											<tr>
												<th>&emsp;</th>
												<th>Auth# No</th>
												<th>Referring Provider</th>
												<th>Insurance</th>                                
												<th>Visit Re#</th>
												<th>Amt Re#</th>
												<th>Start Date</th>
												<th>End Date</th>
											   
											</tr>
										</thead>
										<tbody>

											<tr>
												<td>{!! Form::radio('status', 'Cash',null,['class'=>'flat-red']) !!}</td>
												<td>45345</td>
												<td>Virginia, Deal B</td>
												<td>American Family Insurance</td>
												<td>8</td>
												<td>45300</td>
												<td>04-09-15</td>
												<td>12-16-16</td>                                
											</tr>
											<tr>
												<td>{!! Form::radio('status', 'Cash',null,['class'=>'flat-red']) !!}</td>
												<td>45345</td>
												<td>Thomas M Willamson</td>
												<td>Knights of Columbus</td>
												<td>15</td>
												<td>6000</td>
												<td>09-05-14</td>
												<td>04-16-15</td>                                
											</tr>
											<tr>
												<td>{!! Form::radio('status', 'Cash',null,['class'=>'flat-red']) !!}</td>
												<td>45345</td>
												<td>Suzanne Holroyd</td>
												<td>Northwestern Mutual</td>
												<td>2</td>
												<td>6300</td>
												<td>04-09-15</td>
												<td>12-16-16</td>                                
											</tr>
										</tbody>
									</table>
								</div><!-- /.box-body -->
							</div><!-- /.box -->
						</div>   
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding"><!--  Left side Content Starts -->
                                    <div class="box box-view no-shadow collapsed-box" style="border: 1px solid #85E2E6"><!--  Box Starts -->
                                        <div class="box-header-view">
                                            <i class="livicon" data-name="users-add"></i> <h3 class="box-title">Create Authorization</h3>
                                            <div class="box-tools pull-right">
                                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div><!-- /.box-header -->
                                        <div class="box-body" style="padding-bottom:0px;">
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('authorization_no', 'Auth# No', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('authorization_no',null,['class'=>'form-control input-sm-modal-billing']) !!}                                    
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('Referring Provider', 'Referring Provider', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('referring_provider',null,['class'=>'form-control input-sm-modal-billing']) !!}                                    
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('req_date', 'Requested Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('req_date',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('contact_person', 'Contact Person', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('contact_person',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('alert_on_appointment', 'Alert On Appointment', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('alert_on_appointment',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('allowed_visit', 'Allowed Visits', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('allowed_visit',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('visits_remaining', 'Visits Remaining', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('visits_remaining',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('alerts_visit_remains', 'Alert on Visit Remains', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('alerts_visit_remains',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>

                                            </div>
                                            
                                            
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('insurance_name', 'Insurance Name', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('insurance_name',null,['class'=>'form-control input-sm-modal-billing']) !!}                                    
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('pos', 'Place of Service', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('pos',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>

                                                <div class="form-group-billing">                             
                                                    {!! Form::label('start_date', 'Start Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('start_date',null,['class'=>'form-control input-sm-modal-billing','readonly'=>'readonly']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('end_date', 'End Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('end_date',null,['class'=>'form-control input-sm-modal-billing','readonly'=>'readonly']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('phone', 'Phone / Ext', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-4 col-md-4 col-sm-10 col-xs-10">
                                                        {!! Form::text('phone',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-lg-2 col-md-2 col-sm-4">
                                                        {!! Form::text('ext',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('allowed_amount', 'Total Allowed Amount', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('allowed_amount',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('amount_used', 'Amount Used', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('amount_used',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>
                                                
                                                <div class="form-group-billing">                             
                                                    {!! Form::label('amount_remaining', 'Amount Remaining', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}
                                                    <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                                        {!! Form::text('amount_remaining',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                                    </div>                        
                                                    <div class="col-sm-1"></div>
                                                </div>

                                            </div>
											<div class="modal-footer">
												<input class="btn btn-medcubics js-submit-btn" type="submit" value="Save">
												<button class="btn btn-medcubics" data-dismiss="modal" type="button">Cancel</button>
											</div>
                                        </div><!-- /.box-body --> 
                                        
                                        
                                        
                                    </div><!-- /.box Ends-->
                                </div>
                    </div></div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->    

<div id="ambulance" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Ambulance Billing</h4>
            </div>
            <div class="modal-body">
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">
                       <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                           <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 no-padding">
                        <div class="form-group-billing">                             
                                {!! Form::label('patient_weight', 'Emergency', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    <input type="checkbox" name="emergency">
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                       </div>
                       </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="background: #f4fefe;">

                            
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('patient_weight', 'Patient Weight', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('patient_weight',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                   
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('transport_distance', 'Transport Distance', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('transport_distance',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('transport_code', 'Transport Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('transport_code',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('transport_reason_code', 'Transport Reason Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('transport_reason_code',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" style="background: #f4fefe;">
                            <div class="form-group-billing">                             
                                {!! Form::label('pickup_address_1', 'Pickup Address 1', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('pickup_address_1',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('pickup_address_2', ' Address 2', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('pickup_address_2',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('pickup_address_2', 'City / State', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                                    {!! Form::text('pickup_address_2',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-10">
                                    {!! Form::text('pickup_address_2',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                    {!! Form::text('zipcode',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                    {!! Form::text('zipcode',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 padding-t-5  space10" style="background: #fffefa;">
                            <div class="form-group-billing">                             
                                {!! Form::label('drop_off_location', ' Drop off Location', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('drop_off_location',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('drop_address_1', 'Drop Off Address 1', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('drop_address_1',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('pickup_address_2', ' Address 2', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('pickup_address_2',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('city/state', 'City / State', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-10">
                                    {!! Form::text('city',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-10">
                                    {!! Form::text('state',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                    {!! Form::text('zipcode5',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                    {!! Form::text('zipcode4',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>
                            </div>
                            
                            
                                                        
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">    
                            
                            
                            
                            <div class="form-group-billing">                                                                        
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    {!! Form::textarea('stretcher_purpose',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Medical Office Notes','style'=>'height:143px;']) !!} 
                                </div>                                 
                                <div class="col-sm-1"></div>
                            </div>
                        </div>
                        
                         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
                            <div class="form-group-billing">                                                                        
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                   {!! Form::textarea('stretcher_purpose',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Round Trip Description']) !!} 
                                </div>                                 
                                <div class="col-sm-1"></div>
                            </div>
                        </div>
                        
                         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
                            <div class="form-group-billing">                                                                        
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    {!! Form::textarea('stretcher_purpose',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Stretcher Purpose']) !!} 
                                </div>                                 
                                <div class="col-sm-1"></div>
                            </div>
                        </div>
                                                
                         <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
                            <div class="form-group-billing">                                                                        
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    {!! Form::textarea('stretcher_purpose',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Business Office Notes']) !!} 
                                </div>                                 
                                <div class="col-sm-1"></div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12  space10">                                                                                        
                            <div class="form-group-billing">                                                                        
                                <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                                    {!! Form::textarea('ambulance _certification',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Ambulance Certification']) !!} 
                                </div>                                 
                                <div class="col-sm-1"></div>
                            </div>
                        </div>
                                                                   
                        </div>
                        
                     <div class="modal-footer">
        <input class="btn btn-medcubics js-submit-btn" type="submit" value="Save">
        <button class="btn btn-medcubics" data-dismiss="modal" type="button">Cancel</button>
    </div>
                     
                    </div><!-- /.box-body -->                                
                </div><!-- /.box Ends Contact Details-->

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog --> 


<div id="others" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Other Details</h4>
            </div>
            <div class="modal-body">
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                            <div class="form-group-billing">                             
                                {!! Form::label('family_planning', 'Family Planning', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('family_planning',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('original_reference', 'Original Reference', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('original_reference',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('ref_id_qualifier', 'Reference ID Qualifier', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('ref_id_qualifier',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                                                                                                                
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">                            
                            <div class="form-group-billing">                             
                                {!! Form::label('resubmission_no', 'Resubmission No', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('resubmission_no',null,['class'=>'form-control input-sm-modal-billing']) !!}                                    
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('medicaid_referral_no', 'Medicaid Referral no', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('medicaid_referral_no',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('service_auth_exception', 'Service Auth Exception', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('service_auth_exception',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                                                        
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5" style="background: #f4fefe;">
                            <div class="form-group-billing">                             
                                {!! Form::label('non_availability', 'Non Availability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('non_availability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('sponsor_status', ' Sponsor Status', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('sponsor_status',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('sponsor_grade', 'Sponsor Grade', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('sponsor_grade',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>
                                                        
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5 " style="background: #f4fefe;">
                            <div class="form-group-billing">                             
                                {!! Form::label('branch_service', 'Branch of Service', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('branch_service',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('special_program', 'Special Program', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('special_program',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                                                       
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('start_end_date', 'Effective Start / End Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                    {!! Form::text('start_date',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-10">
                                    {!! Form::text('end_date',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>
                            </div>                                                        
                                                        
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding  margin-t-10">                      
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
                            <div class="form-group-billing">                             
                                {!! Form::label('percent_permanent_disability', 'Percent Permanent Disability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('percent_permanent_disability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('Service_status', 'Service Status', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('service_status',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('service_card_effective', 'Service Card Effective', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('service_card_effective',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('handicapped_program', 'Handicapped Program', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('handicapped_program',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>
                        </div>
                        
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
                            <div class="form-group-billing">                             
                                {!! Form::label('branch_of_service', 'Branch of Service', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('branch_of_service',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('service_grade', 'Service Grade', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('service_grade',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('nonavailable_statement', 'Non Available Statement', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('nonavailable_statement',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                                                        
                            </div>                                                        
                        </div>                                                   
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10"   style="background: #fefef9;">
                     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
                            <div class="form-group-billing">                             
                                {!! Form::label('therapy_type', 'Therapy Type', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('therapy_type',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('class_finding', 'Class Finding', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-10">
                                    {!! Form::text('class_finding',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                                                        
                                                        
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
                            <div class="form-group-billing">                             
                                {!! Form::label('systemic Condition', 'Systemic Condition', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('systemic_condition',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                                                          
                                                        
                        </div>
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
                            <div class="form-group-billing">                             
                                {!! Form::label('nature_of_condition', 'Nature Of Condition', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('nature_of_condition',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>                                                                                                                                                                                                  
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
                            <div class="form-group-billing">                             
                                {!! Form::label('complication_indicator', 'Complication Indicator', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('complication_indicator',null,['class'=>'form-control input-sm-modal-billing']) !!}
                                </div>                                                        
                            </div>                                                                                                                                                                                                  
                        </div>
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="background: #fafaff;">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
                            <div class="form-group-billing">                             
                                {!! Form::label('date_of_last_xray', 'Date of Last X-Ray', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('date_of_last_xray',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('total_disability', 'Total Disability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('total_disability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('hospitalization', 'Hospitalization', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('hospitalization',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div> 
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('prescription_date', 'Prescription Date', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('prescription_date',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div> 
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('months_treated', 'Months Treated', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('months_treated',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div> 
                                                        
                        </div>
                        
                        
                         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
                            <div class="form-group-billing">                             
                                {!! Form::label('consultation_date', 'Consultation Dates', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('consultation_date',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('partial_disability', 'Partial Disability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('partial_disability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('relinquished_care', 'Assumed Relinquished Care', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('relinquished_care',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div> 
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('date_last_visit', 'Date of Last Visit', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('date_last_visit',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div> 
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('date_manifestation', 'Date of Manifestation', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('date_manifestation',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div> 
                                                        
                        </div>
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  no-padding margin-t-10">        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
                            <div class="form-group-billing">                             
                                {!! Form::label('epsdt', 'EPSDT', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('epsdt',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('ambulatory_service_required', 'Ambulatory Service Req', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('ambulatory_service_required',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('levels_of_submission', 'Levels of Submission', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('levels_of_submission',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('weight_units', 'Weight Units', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('weight_units',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  space10">                               
                            <div class="form-group-billing">                             
                                {!! Form::label('family_planning', 'Family Planning', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('family_planning',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('third_party_liability', 'Third Party Liability', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('third_party_liability',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('birth_weight', 'Birth Weight', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('birth_weight',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                                                        
                            </div>     
                            
                        </div>  
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-10" style="background: #f8f9f9;">
                         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
                            <div class="form-group-billing">                             
                                {!! Form::label('pregnant', 'Pregnant', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('pregnant',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('last_menstrual_period', 'Last Menstrual Period', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('last_menstrual_period',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('referal_items', 'Referal Items', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('referal_items',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                                                         
                                                        
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-t-5">
                            <div class="form-group-billing">                             
                                {!! Form::label('estimated_dob', 'Estimated DOB', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('estimated_dob',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('findings', 'Findings', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('findings',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                            
                            <div class="form-group-billing">                             
                                {!! Form::label('referal_codes', 'Referal Codes', ['class'=>'col-lg-5 col-md-5 col-sm-12 col-xs-12 control-label-billing']) !!}                           
                                <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                                    {!! Form::text('referal_codes',null,['class'=>'form-control input-sm-modal-billing']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>                                                         
                                                        
                        </div>
                        </div>
                        
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-t-5">
                            <div class="form-group-billing">                             
                                                 
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    {!! Form::textarea('Notes',null,['class'=>'form-control input-sm-modal-billing','placeholder'=>'Notes']) !!} 
                                </div>                        
                                <div class="col-sm-1"></div>
                            </div>  
                                                                                                                                                                                                                       
                        </div>
                     
                    </div><!-- /.box-body -->                                
                </div><!-- /.box Ends Contact Details-->

            </div>
			<div class="modal-footer">
				<input class="btn btn-medcubics js-submit-btn" type="submit" value="Save">
				<button class="btn btn-medcubics" data-dismiss="modal" type="button">Cancel</button>
			</div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->


<div id="transaction_details" class="modal fade in">
    <div class="modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"> Transaction Details</h4>
            </div>
            <div class="modal-body">
                <div class="box box-view no-shadow no-border"><!--  Box Starts -->

                    <div class="box-body form-horizontal no-padding">
                       
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                            <h5>DOS : <span class="med-orange">12-12-2015</span></h5>
                        </div>
                        
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <h5>Insurance : <span class="med-green">Emmanuel Loucas</span></h5>
                        </div>
                        
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                            <h5>Provider : <span class="med-green">Victoria Bellot</span></h5>
                        </div>
                        
                        <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                            <h5>Balance : <span class="med-orange">$142.40</span></h5>
                        </div>
                        
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-t-5 table-responsive ">
								<table class="table-striped-view table" style="border-bottom:1px solid #E4FAFD; border-top:1px solid #E4FAFD;">                    

									<thead>
										<tr>                                               
											<th>Procedure</th>                                
											<th>M1</th>                               
											<th>M2</th>
											<th>M3</th>
											<th>M4</th>
											<th>Diag#</th>
											<th>Charges</th>
											<th>Units</th>
											<th>Adj</th>
											<th>Paid</th>
											<th>Applied Amt</th>
											<th>Balance</th>
											<th>Resp.Party</th>
											<th>Pat Amt</th>
										</tr>
									</thead>
									<tbody>
										<tr>                                                
											<td>54235</td> 
											<td></td>
											<td></td>
											<td>2K</td>
											<td></td>
											<td>2</td>
											<td>$75.00</td>
											<td>1</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$75.00</td>
											<td>Cigna</td>
											<td>$0.00</td>

										</tr>
										<tr>                                                
											<td>54235</td> 
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td>1</td>
											<td>$200.00</td>
											<td>1</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$200.00</td>
											<td>Health Care</td>
											<td>$0.00</td>

										</tr>

										<tr>                                                
											<td>54235</td> 
											<td></td>
											<td></td>
											<td></td>
											<td>9H</td>
											<td>2</td>
											<td>$189.00</td>
											<td>1</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$189.00</td>
											<td>Cigna</td>
											<td>$0.00</td>

										</tr>
										<tr>                                                
											<td>54235</td> 
											<td></td>
											<td>5J</td>
											<td></td>
											<td></td>
											<td>1</td>
											<td>$375.00</td>
											<td>2</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$0.00</td>
											<td>$375.00</td>
											<td>Cigna</td>
											<td>$0.00</td>

										</tr>



									</tbody>
								</table>
							</div>
                                                                   
                        </div>
                        
                     
                     
                    </div><!-- /.box-body -->                                
                </div><!-- /.box Ends Contact Details-->

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
{!! Form::close() !!}
<!--End-->
@stop 