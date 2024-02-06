<?php 
	$user_type = Auth::user()->practice_user_type;
	if(!isset($get_default_timezone)){
	   $get_default_timezone = \App\Http\Helpers\Helpers::getdefaulttimezone();
	}
?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
    <div class="box box-info no-shadow">
        <div class="box-header margin-b-10">
            <i class="fa fa-bars font14"></i><h3 class="box-title">Documents List</h3>
            <div class="box-tools pull-right ">

            </div>
        </div><!-- /.box-header -->
        <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4" style="position: absolute; z-index:9; left:0px; margin-top: 12px; margin-left: 100px;">                                       

            <!-- <a href = "#" data-toggle="modal" data-tile = "Post Insurance Payment" data-target="#choose_claims" data-url = "" 
               class="js-show-patientsearch js-insurance-popup js-tab-document claimdetail form-cursor font600 p-l-10 p-r-10" style=""> Tab View</a> -->
            <div>@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
                <a class="js-document-action font600 form-cursor" data-type = "delete" data-action="provider"><i class="fa font16 {{Config::get('cssconfigs.common.delete')}} js-prevent-action"></i> Delete</a> 
                <span class="margin-l-5 margin-r-5">|</span>
                @endif
                <a class="js-document-action font600 form-cursor" data-type = "download"><i class="fa font16 {{Config::get('cssconfigs.common.download')}} js-prevent-action"></i> Download</a> <span class="margin-l-5 margin-r-5">|</span> 
                <a class="js-tab-document font600 form-cursor"><i class="fa font16 {{Config::get('cssconfigs.common.view')}} js-prevent-action"></i> View</a></div>
        </div> 
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 hidden-print">
            @if(Session::get('message')!== null) 
            <p class="alert alert-error" id="success-alert">{{ Session::get('message') }}</p>
            @endif
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table id="documents" class="table table-bordered table-striped table-responsive">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Created On</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Title</th>
                            @if($type == 'facility')
                            <th>Facility Name</th>
                            @elseif($type == 'provider')
                            <th>Provider Name</th>
                            @endif
                            <th>Assigned To</th>
                            <th>Follow up Date</th>
                            <th>Status</th>
                            <th>Pages</th>
                            <th>Priority</th>
                            <th>File Type</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($pictures as $keys=>$picture)

                        <?php 
							$doc_id = $picture->id;
							$doc_id_nonencoded = $picture->id;
							$module_name = $picture->document_type;
							$category_name = (@$picture->document_categories =='' || @$picture->document_categories ==null) ? App\Models\Document::getDocumentCategoryName($picture->category): @$picture->document_categories->category_value;
							$picture->type_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($picture->type_id,'encode');
                        	$picture->id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($picture->id,'encode'); 
						?>
                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list"  data-document-id="{{ @$doc_id }}" data-url="{{url('patients/'.@$picture->type_id.'/document-assigned/'.@$doc_id.'/show')}}" data-document-show="js_update_row_{{ @$doc_id }}">
                            <?php
								$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($doc_id, 'encode');
								$data_url = url('api/documentmodal/get/' . $picture->type_id . '/' . $picture->document_type . '/' . $picture->filename);
                            ?>
                            <td><input type="checkbox" name = "document" class="js-prevent-action" data-url = "{{$data_url}}" data-id = "{{$doc_id}}" id="f{{$keys}}"><label for="f{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                            <td>{{ App\Http\Helpers\Helpers::timezone(@$picture->created_at,'m/d/y')}}</td>
                            <td>{{ App\Http\Helpers\Helpers::shortname(@$picture->created_by) }}</td>
                            <td>{{ @$picture->document_categories->module_name." - ".@$picture->document_categories->category }}</td>
                            <td>{{ @$picture->document_categories->category_value  }}</td>
                            <td><span data-toggle="tooltip" title="{{ ucfirst(@$picture->title) }}">{{ ucfirst(substr(@$picture->title, 0, 20)) }}</span></td>
                            @if($type == 'facility')
								<td>{!! @$facility->facility_name !!}</td>
                            @elseif($type == 'provider')
								<td>{!! @$provider->provider_name !!}</td>
                            @endif
                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$picture->document_followup->assigned_user_id) }}</td>
                            <td class="jsfollowup">
                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$picture->document_followup->followup_date,'date'); ?>
                                @if(date("m/d/y") == $fllowup_date)
									<span class="med-orange">{{$fllowup_date}}</span>
                                @elseif(date("m/d/y") >= $fllowup_date)
									<span class="med-red">{{$fllowup_date}}</span>
                                @else
									<span class="med-gray">{{$fllowup_date}}</span>
                                @endif
                            </td>
                            <td class="jsstatus"><span class=" {{@$picture->document_followup->status}}">{{@$picture->document_followup->status}}</span></td>
                            <td>{{ @$picture->page }}</td>
                            <td class="jspriority">
                                <span class="{{@$picture->document_followup->priority}}">
                                    @if(@$picture->document_followup->priority == 'High')
										<span class="hide">{{@$picture->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                    @elseif(@$picture->document_followup->priority == 'Low')
										<span class="hide">{{@$picture->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                    @elseif(@$picture->document_followup->priority == 'Moderate')
										<span class="hide">{{@$picture->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                    @endif							
                                </span>
                            </td>
                            <td>{{@$picture->document_extension}}</td>
                            <td class="td-c-5">
                                <span>
                                    <a href="{{ url('api/documentmodal/get/'.@$picture->type_id.'/'.$module_name.'/'.@$picture->filename) }}" target="_blank"><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action margin-r-5" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a>
                                </span>

                                <span onClick="window.open('{{ url('api/documentdownload/get/'.$picture->type_id.'/'.$picture->document_type.'/'.$picture->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action margin-r-5" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span>

                                @if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
                                <span class="document-delete">
                                    {!! Form::open(array('method'=> 'DELETE','class'=>'displayinline', 'route' =>array('document.destroy', $picture->id))) !!}
									<?php /*		
                                    <!--  <a class="js-delete-confirm" data-text="Are you sure would you like to delete?" 
                                        @if($document_type == 'practice')
                                        data-href="{{ url('document/delete/'.$picture->id) }}"
                                        @elseif($document_type == 'facility')
                                        data-href="{{ url('facility/'.$facility->id.'/facilitydocument/delete/'.$picture->id) }}"
                                        @elseif($document_type == 'provider')
                                        data-href="{{ url('provider/'.$picture->type_id.'/providerdocuments/'.$picture->id.'/delete') }}"
                                        @endif
                                        ><i data-placement="bottom"  data-toggle="tooltip" title="Delete" class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action"></i> </a> -->
									*/ ?>
                                    <span class="js-common-delete-document" data-doc-id="{{$doc_id_nonencoded}}">
                                        <a><i class="fa fa-trash js-prevent-action" data-placement="bottom" data-toggle="tooltip" title="" data-original-title="Delete"></i></a>
                                    </span>
                                    {!! Form::close() !!}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div>
<!-- Show Problem list start-->
<div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
<!-- Show Problem list end-->
@stack('view.scripts')
<script type="text/javascript">

<?php if(isset($get_default_timezone)){?>
     var get_default_timezone = '<?php echo $get_default_timezone;?>';    
<?php }?>
</script>