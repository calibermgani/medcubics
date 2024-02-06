@extends('admin')

@section('toolbar')
	<div class="row toolbar-header">
		<section class="content-header">
			<h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.user')}} med-breadcrum med-green"></i> Profile <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span> Edit </span></small>
        </h1>
                    <ol class="breadcrumb">
                        <?php $id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(Auth::user()->id,'encode'); ?>
                        <li><a href="javascript:void(0)" data-url="{{ url('profile/personaldetailsview/'.$id) }}" class="js_next_process"><i class="fa fa-reply" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>
                        @if($checkpermission->check_adminurl_permission('help/{type}') == 1) 
                        <li><a href="#js-help-modal" data-url="{{url('help/user')}}" class="js-help hide" data-toggle="modal"><i class="fa fa-question-circle" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
                        @endif
                    </ol>
		</section>
	</div>
@stop

@section('practice')	
	{!! Form::model(@$customers, ['method'=>'PATCH', 'url'=>'profile/updatepersonal/'. App\Http\Helpers\Helpers::getEncodeAndDecodeOfId(@$customers->id,'encode'), 'id'=>'js_bootstrap_validator','files'=>true,'name'=>'medcubicsform','class'=>'medcubicsform']) !!}	
    @include ('profile/personaldetail/form',['submitBtn'=>'Save'])
    {!! Form::close() !!}
@stop            