@extends('admin')

@section('toolbar')
<div class="row toolbar-header">

    <section class="content-header">
        <h1>
            <small class="toolbar-heading"><i class="fa {{Config::get('cssconfigs.Practicesmaster.practice')}} font14"></i> Practice <i class="fa fa-angle-double-right med-breadcrum" data-name="angle-wide-right"></i> <span>Notes</span></small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('practice/'.$practice->id)}}"><i class="fa {{Config::get('cssconfigs.common.back')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Back"></i></a></li>

            <!--li><a href="javascript:void(0);" class="js-print"><i class="fa {{Config::get('cssconfigs.common.print')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Print"></i></a></li-->

            <li class="dropdown messages-menu hide"><a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><i class="fa {{Config::get('cssconfigs.common.export')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Export"></i></a>
                @include('layouts.practice_module_export', ['url' => 'api/practicenotesreports/practice/'.$practice->id.'/'])
            </li>

            <li><a href="#js-help-modal" data-url="{{url('help/practice')}}" class="js-help hide" data-toggle="modal"><i class="fa {{Config::get('cssconfigs.common.help')}}" data-placement="bottom"  data-toggle="tooltip" data-original-title="Help"></i></a></li>
        </ol>
    </section>

</div>
@stop

@section('practice-info')
	@include ('practice/practice/practice-tabs')
@stop

@section('practice')

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-20">

    <div class="box box-info no-shadow">
        <div class="box-block-header">
            <i class="fa fa-sticky-note font14 med-green"></i><h3 class="box-title">Notes</h3>
            <div class="box-tools pull-right margin-t-2">
                @if($checkpermission->check_url_permission('notes/create') == 1)			
                <a class="js-notes font600 font14 margin-r-10" href="#"  data-toggle = 'modal' data-target="#create_notes" data-url="notes/create" tabindex="-1"><i class="fa {{Config::get('cssconfigs.common.plus_circle')}}"></i> New Note
                </a>
                @endif	
            </div>
        </div><!-- /.box-header -->
        <!-- form start -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            @if(Session::get('message')!== null) 
            <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
            @endif
        </div>

        <div class="box-body space20"><!-- Box Body Starts -->     
            @if(count($notes) > 0)
            @foreach($notes as $note)
            <?php $note_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($note->id,'encode'); ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!-- Inner width Starts -->

                <div class="msg-time-chat1">

                    <div class="">
                        <div class="text notes pat-notes" style="border-bottom: 1px solid #f0f0f0; margin-bottom: 5px;">
                            <p class="attribution" style="margin-bottom: 2px;">
                                <a class="font600" href="notes/{{ $note->id }}/edit">{{ $note->title }} - <span class="med-orange">{{ App\Http\Helpers\Helpers::dateFormat($note->created_at,'date')}}</span></a>  
                                <span class='notesdate'>
                                    {{Auth::user()->short_name}}
                                    <span class="med-gray-dark"> |</span> 
                                    <a class="js-notes" href="#"  data-toggle = 'modal' data-target="#create_notes" data-url="notes/{{$note_id}}/edit" tabindex="-1"><i class="livicon tooltips" style="margin-right: 2px; margin-left: 3px;" data-placement="bottom"  data-name="edit" data-color="#009595" data-size="17" data-title='Edit Note' data-hovercolor="#009595" title="Edit"></i> </a>
                                    <span class="med-gray-dark"> |</span> 
                                    <a class="js-delete-confirm" data-text="Are you sure to delete the entry?" href="{{ url('notes/delete/'.$note_id) }}"><i class="livicon tooltips" data-placement="bottom"  data-name="trash" data-color="#009595" data-size="16" data-title='Delete Note' data-hovercolor="#009595"></i></a>
                                </span>
                            </p>
                            <p class="align-break" style="background: transparent;">
								{{ $note->content }}
							</p>
                        </div>                                    
                    </div>
                </div>
            </div><!-- Inner width Ends -->    
            @endforeach 
            @else
            <p class="med-gray text-center no-bottom">No Records Found</p>
            @endif
        </div> <!-- Box Body Ends -->  

    </div><!-- /.box-body -->                
</div><!-- /.box -->  
@stop 