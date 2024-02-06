@extends('admin')

@section('toolbar')
<div class="row toolbar-header">
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Contact Details</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('practice/'.$practice->id)}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div>
@stop

@section('practice-info')
@include ('practice/practice/practice-tabs')  
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10 margin-b-10">
    @if(@$checkpermission->check_url_permission('contactdetail/{contactdetail}/edit') == 1 ) 
    <a href="{{ url('contactdetail/'.@$contact_detail->id.'/edit')}}" class="font600 font14 pull-right margin-r-5"><i class="fa {{Config::get('cssconfigs.common.edit')}}"></i> Edit</a>
    @endif	
</div>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    @if(Session::get('message')!== null) 
    <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
    @endif
</div>

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Left side Content Starts -->
    <div class="box  no-shadow margin-b-10"><!-- Box General Contacts Starts -->
        <div class="box-block-header with-border">
            <i class="livicon" data-name="users"></i> <h3 class="box-title">General Contacts</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10"><!-- Box body Starts -->
            <div class="form-group">
                {!! Form::label('PracticeCEO', 'Practice CEO',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->practiceceo }}</p>
                </div>
                <div class="col-sm-1 col-md-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->mobileceo }}</p>
                </div>
                <div class="col-sm-1 col-md-1 col-xs-2"></div>
            </div>                                


            <div class="form-group">
                {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                    <p class="show-border no-bottom">{{ @$contact_detail->phoneceo }}</p>
                </div>
                {!! Form::label('St', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
                    <p class="show-border no-bottom">{{ @$contact_detail->phoneceo_ext }}</p>
                </div>
            </div>                                                                               

            <div class="form-group">
                {!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->faxceo }}</p>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->emailceo }}</p>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>


        </div><!-- /.box-body ends -->
    </div><!-- Box General Contacts Ends -->

    <div class="box no-shadow margin-b-10"><!-- Box Practice Manager Starts -->
        <div class="box-block-header with-border">
            <i class="livicon" data-name="user-flag"></i> <h3 class="box-title">Practice Manager</h3>
            <div class="box-tools pull-right">
                <button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div><!-- /.box-header -->
        <div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->
            <div class="form-group">
                {!! Form::label('PracticeManager', 'Practice Manager',   ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->practicemanager }}</p>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>

            <div class="form-group">
                {!! Form::label('Cell Phone', 'Cell Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->mobilemanager }}</p>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>                                          

            <div class="form-group">
                {!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
                    <p class="show-border no-bottom">{{ @$contact_detail->phonemanager }}</p>
                </div>
                {!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3"> 
                    <p class="show-border no-bottom">{{ @$contact_detail->phonemanager_ext }}</p>
                </div>
            </div>                                                                               

            <div class="form-group">
                {!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-4 col-md-4 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->faxmanager }}</p>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div> 

            <div class="form-group">
                {!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
                    <p class="show-border no-bottom">{{ @$contact_detail->emailmanager }}</p>
                </div>
                <div class="col-md-1 col-sm-1 col-xs-2"></div>
            </div>

        </div><!-- /.box-body -->
    </div><!-- Box Practice Manager Ends -->
</div><!--  Left side Content Ends -->   

<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12"><!--  Right side Content Starts -->
	<div class="box no-shadow margin-b-10"><!-- Box Company Information Starts -->
		<div class="box-block-header with-border">
			<i class="livicon" data-name="tag"></i> <h3 class="box-title">Company Details</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->
			
			<div class="form-group">
				{!! Form::label('CompanyName', 'Company Name',   ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
					<p class="show-border no-bottom">{{ @$contact_detail->companyname }}</p>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"></div>
			</div>          

			<div class="form-group">
				{!! Form::label('Address Line 1', 'Address Line 1', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
				   <p class="show-border no-bottom">{{ @$contact_detail->address1 }}</p>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"></div>
			</div> 

			<div class="form-group">
				{!! Form::label('Address Line 2', 'Address Line 2', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
				   <p class="show-border no-bottom">{{ @$contact_detail->address2 }}</p>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"></div>
			</div> 

			<div class="form-group">
				{!! Form::label('City', 'City', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
					<p class="show-border no-bottom">{{ @$contact_detail->city }}</p>
				</div>
				{!! Form::label('St', 'ST', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">  
					<p class="show-border no-bottom">{{ @$contact_detail->state }}</p>
				</div>
			</div>   

			<div class="form-group">
				{!! Form::label('zipcode', 'Zip Code', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
					<p class="show-border no-bottom">{{ @$contact_detail->zipcode5 }}</p>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-4"> 
					<p class="show-border no-bottom">{{ @$contact_detail->zipcode4 }}</p>
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-2"> 
					<?php $value = App\Http\Helpers\Helpers::commonUSPScheck_view($address_flag['general']['is_address_match'],'show'); ?>   
					<p class="margin-t-13"><?php echo $value; ?></p>  

				</div>
			</div>                                                                                                  

		</div><!-- /.box-body ends -->
	</div><!-- Box Company Information Ends -->

	<div class="box no-shadow margin-b-10"><!-- Box Contact Person Starts -->
		<div class="box-block-header with-border">
			<i class="livicon" data-name="user"></i> <h3 class="box-title">Contact Person</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" tabindex ="-1" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div><!-- /.box-header -->
		<div class="box-body form-horizontal margin-l-10"><!-- Box Body Starts -->
			<div class="form-group">
				{!! Form::label('ContactPerson', 'Contact Person',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
					<p class="show-border no-bottom">{{ @$contact_detail->contactperson }}</p>
				</div>
				<div class="col-sm-1 col-md-1 col-xs-2"></div>
			</div>


			<div class="form-group">
				{!! Form::label('Work Phone', 'Work Phone', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">  
					<p class="show-border no-bottom">{{ @$contact_detail->phone }}</p>
				</div>
				{!! Form::label('Ext', 'Ext', ['class'=>'col-lg-1 col-md-1 col-sm-1 col-xs-1 control-label']) !!}
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">  
					<p class="show-border no-bottom">{{ @$contact_detail->phone_ext }}</p>
				</div>
			</div>                                                                               

			<div class="form-group">
				{!! Form::label('Fax', 'Fax', ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!}
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
					<p class="show-border no-bottom">{{ @$contact_detail->fax }}</p>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"></div>
			</div> 

			<div class="form-group">
				{!! Form::label('Email', 'Email',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
					<p class="show-border no-bottom">{{ @$contact_detail->emailid }}</p>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"></div>
			</div>

			<div class="form-group">
				{!! Form::label('Website', 'Website',  ['class'=>'col-lg-3 col-md-4 col-sm-4 col-xs-12 control-label']) !!} 
				<div class="col-lg-7 col-md-7 col-sm-7 col-xs-10">
					<p class="show-border no-bottom">{{ @$contact_detail->website }}</p>
				</div>
				<div class="col-md-1 col-sm-1 col-xs-2"></div>
			</div>
		</div><!-- /.box-body ends -->
	</div><!-- Box Contact Person Ends -->
</div><!--  Right side Content Ends -->

<!-- Modal Light Box starts -->  
<div id="form-address-modal" class="modal fade in">
    @include ('practice/layouts/usps_form_modal')
</div><!-- Modal Light Box Ends --> 

@stop