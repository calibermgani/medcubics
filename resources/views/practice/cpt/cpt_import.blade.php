@extends('admin')

@section('toolbar')
<div class="row toolbar-header"><!-- Toolbar row Starts -->
    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa fa-medkit font14"></i> {{$heading}} <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> CPT / HCPCS <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>CPT / HCPCS Work RVU</span></small>
        </h1>
        <ol class="breadcrumb">
            <?php /*
              <!--li><a href="{{ url('listfavourites') }}" ><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li-->
              <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->
             */ ?>
            @if(count((array)@$favourites) > 0)
            <li class="dropdown messages-menu hide"><a href="" data-target="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/cptfavouritereports/'])
            </li>
            @endif 
            <li><a href="" data-target="#js-help-modal" data-url="{{url('help/cpt')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>
</div><!-- Toolbar row Ends -->
@stop

@section('practice-info')
@include ('practice/cpt/tabs', array('data' => 'active'))
@stop
@section('practice')
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  space20">
    <div class="box box-info  no-shadow">
        <div class="box-header margin-b-10">

            <i class="fa fa-bars"></i><h3 class="box-title">CPT Import</h3>   
            <div class="box-tools pull-right margin-t-2">
                @if(isset($cpt_arr->count) && $cpt_arr->count == 0)
                <a href="" class="selFnCpt med-red font600 font14"><i class="fa fa-plus-circle"></i> Import CPT</a>
                @else	
                @if($checkpermission->check_url_permission('icd/create') == 1)
                <a href="{{ url('/cpt/create') }}" class="font600 font14"><i class="fa fa-plus-circle"></i> New CPT</a>
                @endif
                @endif
            </div>
        </div><!-- /.box-header -->

        {!! Form::open(array('url' => 'cptupdate','method'=>'POST','id'=>'cptimport','files'=>'true')) !!}
        <?php /*
          <!-- <form action="{{url('cptupdate')}}"method="post" accept-charset="UTF-8" name="medcubicsform" class="medcubicsform bv-form" enctype="multipart/form-data" > -->
         */ ?>

        <div class="box-body form-horizontal"><input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            
				<div class="form-group ">
					<label for="upload_type" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label font600">Download Sample Favorite CPT/HCPCS</label>                                                     
					<div class="col-lg-2 col-md-2 col-sm-6 col-xs-10 no-padding text-center"><a href="{{ url('feeschedule_file/cptcode') }}" id="sample_format" class="med-orange font600 download-btn">Download</a></div>                        
				</div>    
					
				<div class="form-group ">
					<label for="upload_type" class="col-lg-3 col-md-4 col-sm-5 col-xs-12 control-label star">CPT Import</label> 
					<div class="fileupload" data-provides="fileupload">
						<div class="col-lg-2 col-md-2 col-sm-6 col-xs-10 no-padding @if($errors->first('conversion_factor')) error @endif">
							<span class="fileContainer" style="padding:1px 20px;margin-left: 0px;">
								<input class="col-lg-2 col-md-2 col-sm-3 form-control" name="sample_file" id="feeScheduleDoc" type="file" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">  Upload </span>
							<span class="">{!! $errors->first('upload_file',  '<p> :message</p>')  !!}</span>
							<br><span class="js-display-error"></span>
						</div>                            
					</div>
				</div>
				
				<div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 form-group margin-t-20 text-center">
					<input type="submit" class="cpt-import btn btn-medcubics" value="Submit"/>
					<a href="javascript:void(0)" data-url="#"><button class="btn btn-medcubics js_cancel_site" type="button">Cancel</button></a>
				</div>                         
			</div>
        </div>
        
        {!! Form::close() !!}
        <!-- </form> -->

    </div><!-- /.box -->
</div>
<!--End-->
@include('practice/layouts/favourite_modal') 
@stop

@push('view.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#cptimport').bootstrapValidator({
            message: 'This value is not valid',
            excluded: ':disabled',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                // invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                sample_file: {
                    validators: {
                        notEmpty: {
                            message: '{{ trans("practice/practicemaster/feeschedule.validation.file") }}'
                        },
                        file: {
                            extension: 'xls,xlsx',
                            type: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            maxSize: 5 * 1024 * 1024, // 5 MB
                            message: '{{ trans("practice/practicemaster/feeschedule.validation.file_size") }}'
                        },
                        callback: {
                            callback: function (value, validator) {
                                $fields = validator.getFieldElements('sample_file');
                                var get_para_text = $fields.closest('div').find('p.med-red').html();
                                if (get_para_text != undefined) {
                                    $fields.closest('div').find('p.med-red').addClass('hide');
                                }
                                return true;
                            }
                        }
                    }
                },
            }
        });
    });
</script>	
@endpush