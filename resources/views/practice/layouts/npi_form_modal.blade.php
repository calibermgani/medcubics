<!-- Modal Light Box starts -->  
<div id="form-npi-modal" class="modal fade in">
    <div class="modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close npi-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">NPI Information </h4>
            </div>
            <div class="modal-body npi-scroll">
                <div class=" @if($npi_flag['is_valid_npi'] != 'Yes') hide @endif" id="npi_modal_success">                
                    <?php
						$except_values = ['id', 'company_name', 'type', 'type_id', 'type_category', 'created_at', 'updated_at', 'is_valid_npi', 'npi_error_message', 'created_by', 'updated_by', 'deleted_at'];
						$heading_title = ['location_address_1', 'mailling_address_1', 'basic_credential', 'identifiers_code', 'taxonomies_code'];

						$npi1 = ['basic_credential', 'basic_first_name', 'basic_last_name', 'basic_middle_name', 'basic_gender', 'basic_name_prefix', 'basic_sole_proprietor'];
						$npi2 = ['basic_authorized_official_credential', 'basic_authorized_official_first_name', 'basic_authorized_official_last_name', 'basic_authorized_official_name_prefix', 'basic_authorized_official_telephone_number', 'basic_authorized_official_title_or_position', 'basic_organization_name', 'basic_organizational_subpart'];
						$npi1class = '';
						$npi2class = '';
						if ($npi_flag['enumeration_type'] == 'NPI-1') {
							$npi1class = '';
							$npi2class = 'hide';
						} else if ($npi_flag['enumeration_type'] == 'NPI-2') {
							$npi1class = 'hide';
							$npi2class = '';
						}
                    ?>                   
                        
                    @foreach($npi_flag as $key=>$value)
                    @if(!in_array($key,$except_values))
                    @if(in_array($key,$heading_title))
                    <table class="table table-striped-view yes-border l-green-b">
						<tr class="id-heading"><td><span class="med-orange font600">{{str_replace('location_address_1','Location Address',str_replace('mailling_address_1','Mailling Address',str_replace('basic_credential','Basic Details',str_replace('identifiers_code','Identifiers',str_replace('taxonomies_code','Taxonomy',$key)))))}}</span><td></tr>
						@endif
							<?php
								$class = '';
								if (in_array($key, $npi1))
									$class = 'clsNPI1 ' . $npi1class;
								elseif (in_array($key, $npi2))
									$class = 'clsNPI2 ' . $npi2class;
							?>					   
							<td class="{{$class}}"><span class="med-green" style="text-transform: capitalize">{{str_replace('_',' ',str_replace('identifiers_','',str_replace('taxonomies_','',str_replace('basic_','',str_replace('location_','',str_replace('mailling_','',$key))))))}}</span></td>
							<td><span id="modal_{{$key}}"> @if($key=="basic_enumeration_date"  || $value == '0000-00-00') -Nil- @elseif(($key=="basic_enumeration_date" ) || ($key == "basic_last_updated" )) {{ App\Http\Helpers\Helpers::dateFormat($value,'dob')}} @else {{$value}} @endif</span></td>
						</tr>                   
                    @endif
                    @endforeach
                    </table>
                </div>                 
                <p id="npi_modal_error" @if($npi_flag['is_valid_npi'] != 'No') class="hide" @endif>
                   <span id="modal_npi_error_message">{{$npi_flag['npi_error_message']}}</span>
                </p>	
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends -->  