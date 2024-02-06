@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <?php $employer->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($employer->id,'encode'); ?>
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> {{ucfirst($selected_tab)}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>View</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('employer')}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/employers')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop


@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-b-10">
    @if($checkpermission->check_url_permission('employer/{employer}/edit') == 1)
    <a href="{{ url('employer/'.$employer->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- Contact Person Starts -->
    <div class="box  no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="user"></i> <h3 class="box-title"> Employer Status</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10">   <!-- Box Body Ends -->       <!--                                        
            <div class="form-group" >
                {!! Form::label('Employment Status', 'Employment Status', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $employer ->employer_status }}</p>
                </div>                       
            </div>
            @if($employer ->employer_status == 'Employed' || $employer ->employer_status == 'Self Employed')
            <div class="form-group" >
                {!! Form::label('Organization Name', 'Organization Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $employer ->employer_organization_name }}</p>
                </div>                       
            </div>

            <div class="form-group" >
                {!! Form::label('Occupation', 'Occupation', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $employer ->employer_occupation }}</p>
                </div>                       
            </div>
            @endif

            @if($employer ->employer_status == 'Student')
            <div class="form-group" >
                {!! Form::label('Student Status', 'Student Status', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $employer ->employer_student_status }}</p>
                </div>                       
            </div>
            @endif
            -->
            <div class="form-group" >
                {!! Form::label('Employee Name', 'Employer Name', ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ $employer->employer_name }}</p>
                </div>                       
            </div>

            <div class="form-group">
                {!! Form::label('Phone', 'Phone1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                    <p class="show-border no-bottom">{{ @$employer->work_phone}}</p>
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <p class="show-border no-bottom">{{ @$employer->work_phone_ext }}</p>
                </div>
            </div>
             <div class="form-group">
                {!! Form::label('Phone', 'Phone2 ', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-3 col-md-4 col-sm-3 col-xs-6">
                    <p class="show-border no-bottom">{{ @$employer->work_phone1}}</p>
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <p class="show-border no-bottom">{{ @$employer->work_phone_ext1 }}</p>
                </div>
            </div>
			
			<div class="form-group">
                    {!! Form::label('Fax', 'Fax',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!}
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$employer->fax }}</p>
                </div>
                </div>

                <div class="form-group">
                    {!! Form::label('Email', 'Email',  ['class'=>'col-lg-4 col-md-5 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ @$employer->emailid }}</p>
                    </div>                                   
                </div> 			
			
           
        </div><!-- /.box-body Ends-->
    </div><!-- /.box -->
</div><!--Employer Col Ends -->


<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!-- Employer Col Starts -->
    <div class="box no-shadow">
        <div class="box-block-header margin-b-10">
            <i class="livicon" data-name="address-book"></i> <h3 class="box-title"> Employer Details</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->

        <div class="box-body  form-horizontal margin-l-10"><!-- Box Body Starts -->
            <div class=" js-address-class" id="js-address-general-address"><!-- Address Div Starts -->
                {!! Form::hidden('general_address_type','employer',['class'=>'js-address-type']) !!}
                {!! Form::hidden('general_address_type_id',null,['class'=>'js-address-type-id']) !!}
                {!! Form::hidden('general_address_type_category','general_information',['class'=>'js-address-type-category']) !!}
                {!! Form::hidden('general_address1',$address_flag['general']['address1'],['class'=>'js-address-address1']) !!}
                {!! Form::hidden('general_city',$address_flag['general']['city'],['class'=>'js-address-city']) !!}
                {!! Form::hidden('general_state',$address_flag['general']['state'],['class'=>'js-address-state']) !!}
                {!! Form::hidden('general_zip5',$address_flag['general']['zip5'],['class'=>'js-address-zip5']) !!}
                {!! Form::hidden('general_zip4',$address_flag['general']['zip4'],['class'=>'js-address-zip4']) !!}
                {!! Form::hidden('general_is_address_match',$address_flag['general']['is_address_match'],['class'=>'js-address-is-address-match']) !!}
                {!! Form::hidden('general_error_message',$address_flag['general']['error_message'],['class'=>'js-address-error-message']) !!}

                <div class="form-group">
                    {!! Form::label('AddressLine1', 'Address Line 1', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $employer->address1 }}</p>
                    </div>                   
                </div>

                <div class="form-group">
                    {!! Form::label('AddressLine2', 'Address Line 2', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                        <p class="show-border no-bottom">{{ $employer->address2 }}</p>
                    </div>                    
                </div>

                <div class="form-group">
                    {!! Form::label('City', 'City', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        <p class="show-border no-bottom">{{ $employer->city }}</p>
                    </div>
                    {!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
                        <p class="show-border no-bottom">{{ $employer->state }}</p>
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('zip Code', 'Zip Code', ['class'=>'col-lg-4 col-md-4 col-sm-4 col-xs-12 control-label star']) !!}                                                  
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                        <p class="show-border no-bottom">{{ $employer->zip5 }}</p>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4"> 
                       <p class="show-border no-bottom">{{ $employer->zip4 }}</p>
                    </div>
                    <div class="col-md-1 col-sm-1 col-xs-2">
                        <span class="add-on js-address-loading hide"><i class="fa fa-spinner spin icon-green-form"></i></span>
                        <?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match']); ?>   
                        <?php echo $value; ?> 
                    </div>
                </div>
            </div>   <!-- Address Div Ends -->       

            
            @if($employer ->employer_status == 'Employed' || $employer ->employer_status == 'Self Employed')
            <div class="col-lg-12 col-md-12 hidden-sm hidden-xs margin-b-18">
                &emsp;
            </div>
            @endif                      
            
        </div><!-- /.box-body Ends -->
    </div><!-- /.box Ends -->
</div><!--Employer col (left) Ends -->


<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include('practice/layouts/usps_form_modal') 
</div><!-- /.modal  Ends-->
@stop 