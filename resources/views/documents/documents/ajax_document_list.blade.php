@if($document_data->type != 'summery')
<?php $user_type = Auth::user()->practice_user_type; ?>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"  id="js_ajax_part">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-l-0">
        <div class="box box-view no-shadow ">
            <?php
				$rendering_provider = App\Models\Provider::typeBasedProviderlist('Rendering'); 
				$billing_provider 	= App\Models\Provider::typeBasedProviderlist('Billing'); 
				$reffering_provider = App\Models\Provider::typeBasedProviderlist('Referring'); 
			?>
        </div>
    </div>

    <div class="btn-group col-lg-8 col-md-8 col-sm-8 col-xs-12 font13 hidden-print margin-b-4" style="position: absolute; z-index:9; left:0px; margin-top: 12px; margin-left: 100px;">
        <div>
            @if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
            <a class="js-document-action font600 form-cursor" data-type = "delete"><i class="fa font16 {{Config::get('cssconfigs.common.delete')}}"></i> Delete</a>
            <span class="margin-l-5 margin-r-5">|</span>
            @endif
            <a class="js-document-action font600 form-cursor" data-type = "download"><i class="fa font16 {{Config::get('cssconfigs.common.download')}}"></i> Download</a> <span class="margin-l-5 margin-r-5">|</span> 
            <a class="js-tab-document font600 form-cursor"><i class="fa font16 {{Config::get('cssconfigs.common.view')}}"></i> View</a> 
        </div>
    </div> 

    @endif
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding margin-t-15">
        <div class="col-lg-12 margin-t-m-10 no-padding">
            <div class="box-body form-horizontal  bg-white no-padding">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding ">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-bottom: 15px;">
                        <!-- Inner width Starts -->  
                        <?php
							$show_class = "style=display:none";
							if ($document_data->type == 'summery') {
								$show_class = "style=display:block";
							}
                        ?>
                        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12" <?php echo $show_class; ?>>
                            <h4 class="margin-b-1 med-orange dynamic-title">{{ $document_data->title }}</h4>
                        </div>
                        @if($document_data->type == 'summery')

                        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 p-r-0">
                            <h4 class="margin-b-1 med-orange text-right">Count</h4>
                        </div>
						</div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reports-table-tr js_search_part report-menu" style="padding-bottom: 2px; padding-top: 0px;">
								<!-- Inner width Starts -->  
                                <table class="table">
                                    <tbody>
										<tr class="@if($document_data->patient_document > 0) tab_details form-cursor @endif" data-tab-id="pat_doc" data-tab-name="Patient Documents" data-tab-title="Patient Documents" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->patient_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i>  Patient Documents :  <span class="normal-font font13"><i>Registration Documents, Insurance Card Copy, Driving License, Consent Forms...</i></span></h4></td>                                    
											<td class="med-orange font600 text-right font16">{{ $document_data->patient_document }}</td>
										</tr>
										
                                        <tr class="@if($document_data->eligibility_document > 0) tab_details form-cursor @endif" data-tab-id="elig_doc" data-tab-name="Eligibility" data-tab-title="Eligibility & Benefits" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->eligibility_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i>  Eligibility & Benefits : <span class="normal-font font13"><i>Payer Eligibility Reports, Benefit Verification Forms...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->eligibility_document }}</td>
										</tr>
										
                                        <tr class="@if($document_data->authorization_document > 0) tab_details form-cursor @endif" data-tab-id="auth_doc" data-tab-name="Authorization" data-tab-title="Authorization Documents" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->authorization_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Authorization Forms :   <span class="normal-font font13"><i> Authorization Forms, Referral Forms...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->authorization_document }}</td>
										</tr>
										
                                        <tr class="@if($document_data->procedure_document > 0) tab_details form-cursor @endif" data-tab-id="prod_doc" data-tab-name="Procedure Doc" data-tab-title="Procedure Documents" data-tab-model="patients">  
											<td> <h4 class="font16 @if($document_data->procedure_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i>  Procedure Documents :  <span class="normal-font font13"><i>Superbills, Surgery Reports, Procedure Reports, Medical Records...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->procedure_document }}</td>
										</tr>
									
                                        <tr class="@if($document_data->edi_document > 0) tab_details form-cursor @endif" data-tab-id="edi_doc" data-tab-name="EDI Reports" data-tab-title="EDI Reports" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->edi_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> EDI Reports : <span class="normal-font font13"><i>Clearinghouse Reports, Payer Acknowledgments, Rejections...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->edi_document }}</td>
										</tr>
									
                                        <tr class="@if($document_data->payer_document > 0) tab_details form-cursor @endif" data-tab-id="pay_doc" data-tab-name="Payer Reports" data-tab-title="Payer Reports" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->payer_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Payer Reports : <span class="normal-font font13"><i>ERA/EOB, Correspondence Letter, Appeal Letters...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->payer_document }}</td>
										</tr>
										
                                        <tr class="@if($document_data->clinical_document > 0) tab_details form-cursor @endif" data-tab-id="cli_doc" data-tab-name="Clinical Doc" data-tab-title="Clinical Documents" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->clinical_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Clinical Documents : <span class="normal-font font13"><i>Signed Clinical Notes, CT/MRI Reports, X-ray Reports, Lab Results...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->clinical_document }}</td>
										</tr>
										
                                        <tr class="@if($document_data->patient_corresp_document > 0) tab_details form-cursor @endif" data-tab-id="pat_let" data-tab-name="Patient Letter" data-tab-title="Patient Letters" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->patient_corresp_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Patient Correspondence : <span class="normal-font font13"><i>Patient Statements, Patient Payments Letter, Collection Letter...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->patient_corresp_document }}</td>
										</tr>
										
                                        <tr class="@if($document_data->prescription_document > 0) tab_details form-cursor @endif" data-tab-id="pres_doc" data-tab-name="Prescription" data-tab-title="Prescription" data-tab-model="patients">
											<td> <h4 class="font16 @if($document_data->prescription_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Prescriptions : <span class="normal-font font13"><i>Prescriptions, E-prescriptions Logs, Medications...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->prescription_document }}</td>
										</tr>
										
										<tr class="@if($document_data->other_document > 0) tab_details form-cursor @endif" data-tab-id="oth_doc" data-tab-name="Other Documen" data-tab-title="Other Documents" data-tab-model="patients">
											<td><h4 class="font16 @if($document_data->other_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Other Documents : <span class="normal-font font13"><i>Scan Files, Fax Documents...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->other_document }}</td>
										</tr>
										
										<tr class="@if($document_data->facility_document > 0) tab_details form-cursor @endif" data-tab-id="fac_doc" data-tab-name="Facility Docu" data-tab-title="Facility Documents" data-tab-model="facility">
											<td> <h4 class="font16 @if($document_data->facility_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Facility Documents : <span class="normal-font font13"><i>Payer Contracts, Other Documents...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->facility_document }}</td>
										</tr>
										
										<!-- 
										Added new category for provider document 
										Revision 1 - Ref: MR-2472 08 Aug 2019: Selva 
										-->
										<tr class="@if($document_data->provider_document > 0) tab_details form-cursor @endif" data-tab-id="prov_doc" data-tab-name="Provider Doc" data-tab-title="Provider Documents" data-tab-model="provider">
											<td> <h4 class="font16 @if($document_data->provider_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Provider Documents : <span class="normal-font font13"><i>NPI Letter, W9 Form, State Medical License, Facesheet/Superbill...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->provider_document }}</td>
										</tr>
										
										<tr class="@if($document_data->group_document > 0) tab_details form-cursor @endif" data-tab-id="group_doc" data-tab-name="Group Doc" data-tab-title="Group Documents" data-tab-model="group">
											<td> <h4 class="font16 @if($document_data->group_document > 0) med-green @else med-grey @endif"><i class="fa fa-angle-double-right med-orange font20"></i> Group Documents : <span class="normal-font font13"><i>Common...</i></span></h4></td>
											<td class="med-orange font600 text-right font16">{{ $document_data->group_document }}</td>
										</tr>

                                    </tbody>
                                </table>
                            </div><!-- Inner width Ends -->   
                        </div>
                        @elseif($document_data->type == 'assigned')
                        <div class="box-body no-padding">
                            <div class="table-responsive no-padding">
                                <table id="documents" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th style="border-bottom-width: 0px !important;"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Check / EFT Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                                        </tr>
                                        <tr>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Facility</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Provider</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Patient</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Claim No</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Payer</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check No</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Amount</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($document_data->document_dynamic_patient_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input type="checkbox" name = "document" class="js-prevent-action" data-url = "{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"><label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                             <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>NA</td>
                                            <td>NA</td>
                                            <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |
                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
													|
													<span class="js-common-delete-document" data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($document_data->document_dynamic_facility_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>{{ @$list->facility->facility_name }}</td>
                                            <td>NA</td>
                                            <td>NA</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="js-prevent-action fa {{Config::get('cssconfigs.common.download')}}" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |
                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="js-prevent-action fa {{Config::get('cssconfigs.common.view')}}" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
													|
													<span class="js-common-delete-document" data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($document_data->document_dynamic_provider_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                             <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>NA</td>
                                            <td>{{ (!empty($list->provider->provider_name)) ? $list->provider->provider_name : 'NA' }}</td>
                                            <td>NA</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |

                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|

                                                <span class="js-common-delete-document " data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- /.box-body -->
                        @elseif($document_data->type == 'all')
                        <div class="box-body no-padding">
                            <div class="table-responsive no-padding">
                                <table id="documents" class="table table-bordered table-striped table-responsive">
                                    <thead>
                                        <tr>
                                            <th style="border-bottom-width: 0px !important;"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Check / EFT Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                                        </tr>
                                        <tr>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Facility</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Provider</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Patient</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Claim No</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Payer</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check No</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Amount</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>                                    	
                                        @foreach($document_data->document_dynamic_patient_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>NA</td>
                                            <td>NA</td>
                                            <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>
                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |
                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
                                                @if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|
                                                <span class="js-common-delete-document " data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
                                                @endif

                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($document_data->document_dynamic_facility_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>
												@endif
                                                {{ $extType }}
                                            </td>
                                            <td>{{ @$list->facility->facility_name }}</td>
                                            <td>NA</td>
                                            <td>NA</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |

                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
                                                @if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|
                                                <span class="js-common-delete-document" data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
                                                @endif

                                            </td>
                                        </tr>
                                        @endforeach										
										@if(isset($document_data->document_dynamic_group_list))
										@foreach($document_data->document_dynamic_group_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
                                            $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                            $data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>{{ @$list->facility->facility_name }}</td>
                                            <td>NA</td>
                                            <td>NA</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |

                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
                                                @if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|
                                                <span class="js-common-delete-document" data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
                                                @endif

                                            </td>
                                        </tr>
                                        @endforeach
										@endif
                                        @foreach($document_data->document_dynamic_provider_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
                                            $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                            $data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                             <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td></td>
                                            <td>{{ (!empty($list->provider->provider_name)) ? $list->provider->provider_name : 'NA' }}</td>
                                            <td></td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>
                                            
                                            <td>

                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |

                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class="js-prevent-action"><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span> 
                                                @if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|
                                                <span class="js-common-delete-document" data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
                                                @endif

                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div><!-- /.box-body -->
                        @elseif($document_data->type == 'common')
                        <div class="box-body no-padding">
                            <div class="table-responsive no-padding">
                                <table id="documents" class="table table-bordered table-striped table-responsive">		
                                    @if($document_data->category_type == 'patient')
                                    <thead>
                                        <tr>
                                            <th style="border-bottom-width: 0px !important;"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Check / EFT Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                                        </tr>
                                        <tr>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Patient Name</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Claim No</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Payer</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check No</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Check Amount</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($document_data->document_dynamic_patient_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
                                            $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                            $data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>{{ @$list->patients->last_name." ".@$list->patients->first_name}}</td>
                                            <td>{{ ($list->claim_number_data != '') ? $list->claim_number_data : 'NA' }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::payer_shortname($list->payer) }}</td>
                                            <td>{{ ($list->checkno != '') ? $list->checkno : 'NA' }}</td>
                                            <td>{{ ($list->checkdate != "0000-00-00")?App\Http\Helpers\Helpers::dateFormat($list->checkdate,'date'):"NA"}}</td>
                                            <td>{{ ($list->checkamt != '0.00') ? $list->checkamt : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |
                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|
                                                <span class="js-common-delete-document " data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif

                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        @if($document_data->category_type == 'facility')
                                    <thead>
                                        <tr>
                                            <th style="border-bottom-width: 0px !important;"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="1"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                                        </tr>
                                        <tr>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Facility Name</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($document_data->document_dynamic_facility_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>{{ @$list->facility->facility_name }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |

                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|

                                                <span class="js-common-delete-document " data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
										@if($document_data->category_type == 'group')
                                    <thead>
                                        <tr>
                                            <th style="border-bottom-width: 0px !important;"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="1"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                                        </tr>
                                        <tr>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Facility Name</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($document_data->document_dynamic_group_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
												$doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
												$data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>{{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}</td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>{{ @$list->facility->facility_name }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>                                            
                                            <td>
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |

                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|

                                                <span class="js-common-delete-document " data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                        @if($document_data->category_type == 'provider')
                                     <thead>
                                        <tr>
                                            <th style="border-bottom-width: 0px !important;"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="2" >Created Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="5">Document Info</th>
                                            <th style="border-bottom-width: 0px !important;" colspan="1"></th>
                                            <th style="border-bottom-width: 0px !important;" colspan="4">Assigned Info</th>
                                            <th style="border-bottom-width: 0px !important;" class="td-c-6"></th>
                                        </tr>
                                        <tr>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Date</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Sub Category</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Title</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Pages</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">File Type</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Provider Name</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">User</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Follow up</th>
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;">Status</th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>                                            
                                            <th style="background: rgb(214, 245, 240); text-align: center; font-weight: bold; color: rgb(0, 127, 120) !important;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($document_data->document_dynamic_provider_list as $keys=>$list)
                                        <tr class="form-cursor cur-pointer js_show_document_assigned_list" data-toggle="modal" data-target="#show_document_assigned_list" data-document-id="{{ @$list->id }}" data-url="{{url('patients/'.@$list->type_id.'/document-assigned/'.@$list->id.'/show')}}" data-document-show="js_update_row_{{ @$list->id }}">
                                            <?php
                                            $doc_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->id, 'encode');
                                            $data_url = url('api/documentmodal/get/' . App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id, 'encode') . '/' . $list->document_type . '/' . $list->filename);
                                            ?>
                                            <td class="js-prevent-show"><input name="document" type="checkbox" class="js-prevent-action" data-url="{{$data_url}}" data-id = "{{$doc_id}}" id="doc{{$keys}}"> <label for="doc{{$keys}}" class="no-bottom js-prevent-action">&nbsp;</label></td>
                                            <td>
                                            {{ App\Http\Helpers\Helpers::timezone($list->created_at, 'm/d/y') }}
                                            </td>
                                            <td>{{ App\Http\Helpers\Helpers::shortname($list->created_by) }}</td>
                                            <td>{{ ucfirst(@$list->document_categories->module_name)." - ".@$list->document_categories->category }}</td>
                                            <td>{{ @$list->document_categories->category_value }}</td>
                                            <td><span data-toggle="tooltip" title="{{ ucfirst($list->title) }}">{{ ucfirst(substr($list->title, 0, 20)) }}</span></td>
                                            <td>{{ $list->page }}</td>
                                            <td><?php $extType = strtolower($list->document_extension) ?> 
                                                @if($extType == 'docx' || $extType == 'doc')
                                                 <i class="fa fa-file-word-o med-blue"></i>
                                                @elseif($extType == 'jpeg' || $extType == 'png' || $extType == 'jpg')
                                                 <i class="fa fa-file-image-o med-green"></i>
                                                @elseif($extType == 'pdf')
                                                 <i class="fa fa-file-pdf-o med-red"></i>
                                                @elseif($extType == 'txt')
                                                 <i class="fa fa-file-text-o med-gray"></i>
                                                @elseif($extType == 'xlsx' || $extType == 'xls' || $extType == 'csv')
                                                 <i class="fa fa-file-excel-o med-blue"></i>
                                                @elseif($extType == 'zip')
                                                 <i class="fa fa-file-archive-o med-orange"></i>
                                                @else
                                                 <i class="fa fa-file-o med-green"></i>@endif
                                                 {{ $extType }}
                                            </td>
                                            <td>{{ (!empty($list->provider->provider_name)) ? $list->provider->provider_name : 'NA' }}</td>
                                            <td class="jsuser">{{ App\Http\Helpers\Helpers::shortname(@$list->document_followup->assigned_user_id) }}</td>
                                            <td class="jsfollowup">
                                                <?php $fllowup_date = App\Http\Helpers\Helpers::dateFormat(@$list->document_followup->followup_date,'date'); ?>
                                                @if(date("m/d/y") == $fllowup_date)
                                                <span class="med-orange">{{$fllowup_date}}</span>
                                                @elseif(date("m/d/y") >= $fllowup_date)
                                                <span class="med-red">{{$fllowup_date}}</span>
                                                @else
                                                <span class="med-gray">{{$fllowup_date}}</span>
                                                @endif
                                            </td>
                                            <td class="jsstatus"><span class="font600 {{ @$list->document_followup->status }}" >{{ @$list->document_followup->status }}</span></td>
                                            <td class="jspriority">
                                                <span class="{{@$list->document_followup->priority}}">
                                                    @if(@$list->document_followup->priority == 'High')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-up" data-toggle="tooltip" data-original-title="High" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Low')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrow-down" data-toggle="tooltip" data-original-title="Low" aria-hidden="true"></i>
                                                    @elseif(@$list->document_followup->priority == 'Moderate')
                                                    <span class="hide">{{@$list->document_followup->priority }}</span><i class="fa fa-arrows-h" data-toggle="tooltip" data-original-title="Moderate" aria-hidden="true"></i>
                                                    @endif							
                                                </span>
                                            </td>
                                            <td> 
                                                <span onClick="window.open('{{ url('api/documentdownload/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}')" class=""><a><i class="fa {{Config::get('cssconfigs.common.download')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="Download"></i></a></span> |
                                                <span><a onClick="window.open('{{ url('api/documentmodal/get/'.App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($list->type_id,'encode').'/'.$list->document_type.'/'.$list->filename) }}', '_blank')" class=""><i class="fa {{Config::get('cssconfigs.common.view')}} js-prevent-action" data-placement="bottom"  data-toggle="tooltip" title="View"></i></a></span>
												@if($user_type=="practice_admin" || $user_type=="customer" || Auth::user()->role_id == 1)
												|
                                                <span class="js-common-delete-document js-prevent-action" data-doc-id="{{ $list->id }}"><a><i class="fa {{Config::get('cssconfigs.common.delete')}}" data-placement="bottom"  data-toggle="tooltip" title="Delete"></i></a></span>
												@endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show Problem list start-->
    <div id="show_document_assigned_list" class="modal fade in js_model_show_document_assigned_list"></div><!-- /.modal-dialog -->
</div>	
    <!-- Show Problem list end-->