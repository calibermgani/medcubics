@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
       <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> Appeal Address <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i>  <span>View</span></small>
        </h1> 
       
        <ol class="breadcrumb">
        <li><a href="{{ url('insurance/'.$insurance->id.'/insuranceappealaddress/') }}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
		
            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="#js-help-modal" data-url="{{url('help/insurance')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice-info')
@include ('practice/insurance/insurance_tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if($checkpermission->check_url_permission('insurance/{insuranceid}/insuranceappealaddress/{appealaddressid}/edit') == 1)	
    <a href="{{ url('insurance/'.$insurance->id.'/insuranceappealaddress/'.$appealaddress->id.'/edit') }}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
		@endif   
</div>
<div class="col-lg-12 col-md-12 col-xs-12"><!--  Left side Content Starts -->
   
            <div class="box no-shadow">
                <div class="box-block-header margin-b-10">
                    <i class="livicon" data-name="info"></i> <h3 class="box-title">Appeal Address</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body form-horizontal margin-l-10">

                   <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 no-padding">                    
                        

                        <div class="form-group">
                            {!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!} 
                            <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">                                                     
                               <p class="show-border no-bottom">{{ $appealaddress->address_1 }}</p>
                            </div>
                            <div class="col-sm-1"></div>
                        </div>  

                        <div class="form-group">
                            {!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!} 
                            <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">                            
                                <p class="show-border no-bottom">{{ $appealaddress->address_2 }}</p>
                            </div>
                            <div class="col-sm-1"></div>
                        </div> 
                       
                        
                        <div class="form-group">
                            {!! Form::label('City', 'City', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                            <div class="col-lg-2 col-md-4 col-sm-3 col-xs-6">  
                                 <p class="show-border no-bottom">{{ $appealaddress->city }}</p>
                            </div>
                            {!! Form::label('ST', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">
                               <p class="show-border no-bottom">{{ strtoupper($appealaddress->state) }}</p>
                            </div>
                        </div>


                        <div class="form-group">
                            {!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">                             
                                <p class="show-border no-bottom">{{ $appealaddress->zipcode5}}</p>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-2 col-xs-4">                             
                                <p class="show-border no-bottom">{{ $appealaddress->zipcode4 }}</p>
                            </div>

                            <div class="col-md-1 col-sm-2">                                            
                                 <span class="js-address-success @if($address_flag['general']['is_address_match'] != 'Yes') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-check icon-green margin-t-5"></i></a></span>    
                            <span class="js-address-error @if($address_flag['general']['is_address_match'] != 'No') hide @endif"><a data-toggle="modal" href="#form-address-modal"><i class="fa fa-close icon-red margin-t-5"></i></a></span> 
                            </div> 
                        </div>


                        <div class="form-group">
                            {!! Form::label('work phone', 'Work Phone', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                            <div class="col-lg-2 col-md-4 col-sm-3 col-xs-6">
                               <p class="show-border no-bottom">{{ $appealaddress->phone }}</p>
                            </div>
                            {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">                             
                                <p class="show-border no-bottom">{{ $appealaddress->phoneext }}</p>                      
                            </div>
                                                        
                        </div>
                                                                        

                        <div class="form-group">
                            {!! Form::label('fax', 'Fax', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-10">
                                <p class="show-border no-bottom">{{ $appealaddress->fax }}</p>
                            </div>
                            <div class="col-sm-1 col-xs-2"></div>
                        </div> 


                        <div class="form-group">
                            {!! Form::label('email', 'Email', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                            <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">
                                <p class="show-border no-bottom">{{ $appealaddress->email }}</p>
                            </div>

                            <div class="col-sm-1 col-xs-2"></div>
                        </div> 
                        
                        <div class="form-group">
                            {!! Form::label('Created By', 'Created By', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                            <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">
                                <p class="show-border no-bottom">{{ @$appealaddress->user->short_name }}</p>
                            </div>

                            <div class="col-sm-1 col-xs-2"></div>
                        </div> 
                        
                        <div class="form-group">
                            {!! Form::label('Updated By', 'Updated By', ['class'=>'col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label']) !!}                                                                                 
                            <div class="col-lg-4 col-md-7 col-sm-6 col-xs-10">
                                <p class="show-border no-bottom">{{ @$appealaddress->userupdate->short_name }}</p>
                            </div>

                            <div class="col-sm-1 col-xs-2"></div>
                        </div> 
                                                               
                </div><!-- /.box-body -->                               
                
            </div><!-- /.box Ends--> 
      
            </div>
         
        </div><!--  Left side Content Ends -->  

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
   @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends --> 

@include('practice/layouts/favourite_modal')   
@stop 