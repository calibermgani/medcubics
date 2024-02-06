@if($icd_cpt_result != "")
<table id="example2" class="table table-bordered table-striped">
    <thead>				
        <tr class="js-table-click clsCursor">
            @foreach($icd_cpt_column as $icd_cpt_column)
            <th>{{ $icd_cpt_column }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if($search_for == "imo_cpt")
        <?php $count = 0; ?>
        @foreach($icd_cpt_result as $cpt_result)
        @if(@$cpt_result->CPT_CODE !='')
        <tr data-backdrop="false" data-toggle="modal" data-target="#form_modal_view" href="#form_modal_view" data-index="{{ @$count }}" class="js_toggle_view">
            <td>{{ @$cpt_result->CPT_CODE }}</td>
            <td>{{ @$cpt_result->CPT_DESC_SHORT }}</td>
            <td>{{ @$cpt_result->LASTUPDATED }}
                <span class="hide" id="js_view_content_{{ @$count }}">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-10" style="border: 1px solid #8ce5bb">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">CPT Details</span>
                        </div>
                        <table class="popup-table-wo-border-bg-white table no-bottom">                    
                            <tbody>
                                <tr>
                                    <td style="width:30%;" class="font600">CPT Code</td>
                                    @if(@$cpt_result->CPT_CODE != "")
                                    <td class="med-orange">{{ @$cpt_result->CPT_CODE }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td class="font600">Title </td>
                                    @if(@$cpt_result->title != "")
                                    <td>{{ @$cpt_result->title }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>

                                <tr>
                                    <td class="font600">CPT Short Desc</td>
                                    @if(@$cpt_result->CPT_DESC_SHORT != "")
                                    <td>{{ @$cpt_result->CPT_DESC_SHORT }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td class="font600"> CPT Medium Desc</td>
                                    @if(@$cpt_result->CPT_DESC_MEDIUM != "")
                                    <td>{{ @$cpt_result->CPT_DESC_MEDIUM }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td class="font600">CPT Long Desc </td>
                                    @if(@$cpt_result->CPT_DESC_LONG != "")
                                    <td>{{ @$cpt_result->CPT_DESC_LONG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td class="font600">CPT Addl Map</td>
                                    @if(@$cpt_result->CPT_ADDL_MAP_FLAG != "")
                                    <td>{{ @$cpt_result->CPT_ADDL_MAP_FLAG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td class="font600">Gender</td>
                                    @if(@$cpt_result->GENDER_FLAG != "")
                                    <td>{{ @$cpt_result->GENDER_FLAG }}</td>
                                    @else
                                    <td>Both</td>	
                                    @endif
                                </tr>

                                <tr>
                                    <td class="font600 border-radius-4">Last Updated</td>
                                    @if(@$cpt_result->LASTUPDATED != "")
                                    <td class=" border-radius-4"><span class='bg-date' >{{ @$cpt_result->LASTUPDATED }}</span></td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-20" style="border: 1px solid #8ce5bb">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">HCPCS Details</span>
                        </div>
                        <table class="popup-table-wo-border-bg-white table no-bottom">                    
                            <tbody>
                                <tr>
                                    <td>HCPCS Code</td>
                                    @if(@$cpt_result->HCPCS_CODE != "")
                                    <td>{{ @$cpt_result->HCPCS_CODE }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>

                                <tr>
                                    <td>HCPCS Short Desc</td>
                                    @if(@$cpt_result->HCPCS_DESC_SHORT != "")
                                    <td>{{ @$cpt_result->HCPCS_DESC_SHORT }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>HCPCS Long Desc</td>
                                    @if(@$cpt_result->HCPCS_DESC_LONG != "")
                                    <td>{{ @$cpt_result->HCPCS_DESC_LONG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-20" style="border: 1px solid #8ce5bb">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">ICD Details</span>
                        </div>
                        <table class="popup-table-wo-border-bg-white table no-bottom">                    
                            <tbody>
                                <tr>
                                    <td>ICD-09-CM</td>
                                    @if(@$cpt_result->ICDP_CODE != "")
                                    <td>{{ @$cpt_result->ICDP_CODE }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>ICD-10-PCS</td>
                                    @if(@$cpt_result->ICD10PCS_CODE != "")
                                    <td>{{ @$cpt_result->ICD10PCS_CODE }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>ICD-09-CM Short Desc</td>
                                    @if(@$cpt_result->ICDP_DESC_SHORT != "")
                                    <td>{{ @$cpt_result->ICDP_DESC_SHORT }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>ICD-09-CM Long Desc</td>
                                    @if(@$cpt_result->ICDP_DESC_LONG != "")
                                    <td>{{ @$cpt_result->ICDP_DESC_LONG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>ICD-10-PCS Short Desc</td>
                                    @if(@$cpt_result->ICD10PCS_DESC_SHORT != "")
                                    <td>{{ @$cpt_result->ICD10PCS_DESC_SHORT }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>ICD-10-PCS Long Desc</td>
                                    @if(@$cpt_result->ICD10PCS_DESC_LONG != "")
                                    <td>{{ @$cpt_result->ICD10PCS_DESC_LONG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-20" style="border: 1px solid #8ce5bb">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">LOINC Details</span>
                        </div>
                        <table class="popup-table-wo-border-bg-white table no-bottom">                    
                            <tbody>
                                <tr>
                                    <td> LOINC Code </td>
                                    @if(@$cpt_result->LOINC_CODE != "")
                                    <td>{{ @$cpt_result->LOINC_CODE }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>LOINC Short Desc </td>
                                    @if(@$cpt_result->LOINC_DESC_SHORT != "")
                                    <td>{{ @$cpt_result->LOINC_DESC_SHORT }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>LOINC Long Desc </td>
                                    @if(@$cpt_result->LOINC_DESC_LONG != "")
                                    <td>{{ @$cpt_result->LOINC_DESC_LONG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-20" style="border: 1px solid #8ce5bb">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">RxNorm Details</span>
                        </div>
                        <table class="popup-table-wo-border-bg-white table no-bottom ">                    
                            <tbody class="bg-white">
                                <tr>
                                    <td>RxNorm Code</td>
                                    @if(@$cpt_result->RXNORM_CODE != "")
                                    <td>{{ @$cpt_result->RXNORM_CODE }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>

                                <tr>
                                    <td> RxNorm Desc </td>
                                    @if(@$cpt_result->RXNORM_DESC_LONG != "")
                                    <td>{{ @$cpt_result->RXNORM_DESC_LONG }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-20" style="border: 1px solid #8ce5bb">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">SNOMED Details</span>
                        </div>
                        <table class="popup-table-wo-border-bg-white table no-bottom">                    
                            <tbody>
                                <tr>
                                    <td>SNOMED Desc</td>
                                    @if(@$cpt_result->SNOMED_DESCRIPTION != "")
                                    <td>{{ @$cpt_result->SNOMED_DESCRIPTION }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>
                                <tr>
                                    <td>SNOMED Search</td>
                                    @if(@$cpt_result->searchpayload != "")
                                    <td>{{ str_replace("Mapped to:","",@$cpt_result->searchpayload) }}</td>
                                    @else
                                    <td class="disable-color">None</td>	
                                    @endif
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </span>
            </td>
        </tr>
        @endif
        <?php $count++; ?>
        @endforeach
        @elseif($search_for == "imo_icd")
		<?php $icd_count = 0; ?>
        @foreach($icd_cpt_result as $icd_result)					
        <tr data-backdrop="false" data-toggle="modal" data-target="#form_modal_view" href="#form_modal_view" data-index="{{ @$icd_count }}" class="js_toggle_view">

            <td>{{ @$icd_result->ICD10CM_CODE }}</td>
            <td>{{ @$icd_result->ICD10CM_TITLE }}
                <span class="hide" id="js_view_content_{{ @$icd_count }}">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-10" style="border: 1px solid #8ce5bb">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">ICD-10 Details</span>
                        </div>

                        <table class="table table-striped-view">
							<tr>
                                <td style="width:35%;">Preferred ICD-10-CM Code(s)</td>
                                <td>:</td>
                                <td>{{ @$icd_result->ICD10CM_CODE }}</td>
                                <td>{{ @Config('app.ICD10CM_CODE') }}</td>
                            </tr>
                            <tr>
                                <td> ICD-10CM Title</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_TITLE != "")
                                <td>{{ @$icd_result->ICD10CM_TITLE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM Non Specific Code</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_NON_SPECIFIC_CODE != "")
                                <td>{{ @$icd_result->ICD10CM_NON_SPECIFIC_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM HCC Model CAT</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_HCC_MODEL_CAT != "")
                                <td>{{ @$icd_result->ICD10CM_HCC_MODEL_CAT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM HCC Comm Factors</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_HCC_COMM_FACTORS != "")
                                <td>{{ @$icd_result->ICD10CM_HCC_COMM_FACTORS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM HCC Inst Factors</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_HCC_INST_FACTORS != "")
                                <td>{{ @$icd_result->ICD10CM_HCC_INST_FACTORS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM HCC Model CAT Next</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_HCC_MODEL_CAT_NEXT != "")
                                <td>{{ @$icd_result->ICD10CM_HCC_MODEL_CAT_NEXT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM HCC Comm Factors Next </td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_HCC_COMM_FACTORS_NEXT != "")
                                <td>{{ @$icd_result->ICD10CM_HCC_COMM_FACTORS_NEXT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10CM HCC Inst Factors Next</td>
                                <td>:</td>
                                @if(@$icd_result->ICD10CM_HCC_INST_FACTORS_NEXT != "")
                                <td>{{ @$icd_result->ICD10CM_HCC_INST_FACTORS_NEXT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
							 <tr>
                                <td> Secondary ICD-10 Code1</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_CODE1 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_CODE1 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Text1 </td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_TEXT1 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_TEXT1 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Code2 </td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_CODE2 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_CODE2 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Text2</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_TEXT2 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_TEXT2 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Code3</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_CODE3 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_CODE3 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Text3</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_TEXT3 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_TEXT3 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Code4 </td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_CODE4 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_CODE4 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-10 Text4</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD10_TEXT4 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD10_TEXT4 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
							<tr>
                                <td> ICD-09 Ref Flag </td>
                                <td>:</td>
                                @if(@$icd_result->ICD9_REF_FLAG != "")
                                <td>{{ @$icd_result->ICD9_REF_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-10 Ref Flag </td>
                                <td>:</td>
                                @if(@$icd_result->ICD10_REF_FLAG != "")
                                <td>{{ @$icd_result->ICD10_REF_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                        </table>
                    </div>	
					 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-20" style="border: 1px solid #8ce5bb">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">ICD-09 Details</span>
                        </div>

                        <table class="table table-striped-view">
							 <tr>
                                <td>Tittle</td>
                                <td>:</td>
                                <td>{{ @$icd_result->title  }}</td>
                            </tr>  
							 </tbody>
							<tr>
                                <td>Preferred ICD-09 Code(s)	</td>
                                <td>:</td>
                                <td>{{ @$icd_result->kndg_code }}, {{ @$icd_result->kndg_title }}</td>
                            </tr>
                            <tr>
                                <td>ICD-09 Non Speecific Code </td>
                                <td>:</td>
                                <td>{{ @$icd_result->NON_SPECIFIC_CODE }}</td>
                            </tr>	
                            <tr>
                                <td>ICD-09 Non Primary Code</td>
                                <td>:</td>
                                <td>{{ @$icd_result->NON_PRIMARY_CODE }}</td>
                            </tr>
							 <tr>
                                <td>ICD-09 HCC Model CAT</td>
                                <td>:</td>
                                @if(@$icd_result->HCC_MODEL_CAT != "")
                                <td>{{ @$icd_result->HCC_MODEL_CAT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 HCC Community Factors</td>
                                <td>:</td>
                                @if(@$icd_result->HCC_COMMUNITY_FACTORS != "")
                                <td>{{ @$icd_result->HCC_COMMUNITY_FACTORS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>ICD-09 HCC Institution Factors</td>
                                <td>:</td>
                                @if(@$icd_result->HCC_INSTITUTION_FACTORS != "")
                                <td>{{ @$icd_result->HCC_INSTITUTION_FACTORS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Model CAT</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_MODEL_CAT != "")
                                <td>{{ @$icd_result->RXHCC_MODEL_CAT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif	
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Relative Factors</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_RELATIVE_FACTORS != "")
                                <td>{{ @$icd_result->RXHCC_RELATIVE_FACTORS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif	
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Dollar Coefficients</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_DOLLAR_COEFFICIENTS != "")
                                <td>{{ @$icd_result->RXHCC_DOLLAR_COEFFICIENTS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 HCC Model CAT Next</td>
                                <td>:</td>
                                @if(@$icd_result->HCC_MODEL_CAT_NEXT != "")
                                <td>{{ @$icd_result->HCC_MODEL_CAT_NEXT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 HCC Community Factors Next</td>
                                <td>:</td>
                                @if(@$icd_result->HCC_COMMUNITY_FACTORS_NEXT != "")
                                <td>{{ @$icd_result->HCC_COMMUNITY_FACTORS_NEXT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 HCC Institution Factors Next</td>
                                <td>:</td>
                                @if(@$icd_result->HCC_INSTITUTION_FACTORS_NEXT != "")
                                <td>{{ @$icd_result->HCC_INSTITUTION_FACTORS_NEXT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Code1</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_CODE1 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_CODE1 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Text1</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_TEXT1 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_TEXT1 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Code2</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_CODE2 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_CODE2 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Text2</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_TEXT2 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_TEXT2 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Code3</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_CODE3 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_CODE3 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Text3</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_TEXT3 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_TEXT3 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>  Secondary ICD-09 Code4</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_CODE4 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_CODE4 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Secondary ICD-09 Text4</td>
                                <td>:</td>
                                @if(@$icd_result->SECONDARY_ICD9_TEXT4 != "")
                                <td>{{ @$icd_result->SECONDARY_ICD9_TEXT4 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 SCT Concept ID</td>
                                <td>:</td>
                                @if(@$icd_result->SCT_CONCEPT_ID != "")
                                <td>{{ @$icd_result->SCT_CONCEPT_ID }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 Snomed Description</td>
                                <td>:</td>
                                @if(@$icd_result->SNOMED_DESCRIPTION != "")
                                <td>{{ @$icd_result->SNOMED_DESCRIPTION }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 Relationship Type</td>
                                <td>:</td>
                                @if(@$icd_result->RELATIONSHIP_TYPE != "")
                                <td>{{ @$icd_result->RELATIONSHIP_TYPE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr><tr>
                                <td> ICD-09 SCT Us Concept ID</td>
                                <td>:</td>
                                @if(@$icd_result->SCT_US_CONCEPT_ID != "")
                                <td>{{ @$icd_result->SCT_US_CONCEPT_ID }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 Snomed Us Description</td>
                                <td>:</td>
                                @if(@$icd_result->SNOMED_US_DESCRIPTION != "")
                                <td>{{ @$icd_result->SNOMED_US_DESCRIPTION }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC_Nonlowincome 65ANDOVER</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_NONLOWINCOME_65ANDOVER != "")
                                <td>{{ @$icd_result->RXHCC_NONLOWINCOME_65ANDOVER }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Nonlowincome UNDER65</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_NONLOWINCOME_UNDER65 != "")
                                <td>{{ @$icd_result->RXHCC_NONLOWINCOME_UNDER65 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Lowincome 65ANDOVER</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_LOWINCOME_65ANDOVER != "")
                                <td>{{ @$icd_result->RXHCC_LOWINCOME_65ANDOVER }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Lowincome UNDER65</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_LOWINCOME_UNDER65 != "")
                                <td>{{ @$icd_result->RXHCC_LOWINCOME_UNDER65 }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> ICD-09 RXHCC Institutional</td>
                                <td>:</td>
                                @if(@$icd_result->RXHCC_INSTITUTIONAL != "")
                                <td>{{ @$icd_result->RXHCC_INSTITUTIONAL }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
						</table>
                    </div>	
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-10" style="border: 1px solid #8ce5bb">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">Clinical Details</span>
                        </div>

                        <table class="table table-striped-view">
							<tr>
                                <td>Clinical Specialties</td>
                                <td>:</td>
                                @if(@$icd_result->clinical_spl_content != "")
                                <td>{{ @$icd_result->clinical_spl_content }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif	
                            </tr>
                            <tr>
                                <td>Categories</td>
                                <td>:</td>
                                @if(@$icd_result->categories != "")
                                <td>{{ @$icd_result->categories }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Pref Lex Flag</td>
                                <td>:</td>
                                @if(@$icd_result->PREF_LEX_FLAG != "")
                                <td>{{ @$icd_result->PREF_LEX_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>                            
                            <tr>
                                <td>Last Updated Date</td>
                                <td>:</td>
                                <td><span class='bg-date'>@if(@$icd_result->LASTUPDATED){{ App\Http\Helpers\Helpers::dateFormat(@$icd_result->LASTUPDATED,'date')}} @endif </span></td>
                            </tr>
						</table>
                    </div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 yes-border border-radius-4 margin-t-10" style="border: 1px solid #8ce5bb">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-orange no-padding margin-t-m-10">
                            <span class="bg-white padding-4">other Details</span>
                        </div>

                        <table class="table table-striped-view">
							<tr>
								<td>Gender Flag</td>
                                <td>:</td>
                                @if(@$icd_result->GENDER_FLAG != "")
                                <td>{{ @$icd_result->GENDER_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Age Flag</td>
                                <td>:</td>
                                @if(@$icd_result->AGE_FLAG != "")
                                <td>{{ @$icd_result->AGE_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>MCC Flag</td>
                                <td>:</td>
                                @if(@$icd_result->MCC_FLAG == 0)
                                <td>Not included in Major Complication and Comorbidity (Major CC) List for the MSDRGs</td>
                                @elseif(@$icd_result->MCC_FLAG == 1)
                                <td>Included in Major CC List for the MS-DRGs</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif	
                            </tr>
                            <tr>
                                <td>CC Flag</td>
                                <td>:</td>
                                @if(@$icd_result->CC_FLAG != "")
                                <td>{{ @$icd_result->CC_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>                           
                            <tr>
                                <td>Post Roote Lex Flag</td>
                                <td>:</td>
                                @if(@$icd_result->POST_COORD_LEX_FLAG != "")
                                <td>{{ @$icd_result->POST_COORD_LEX_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Root Lex Text IMO Code</td>
                                <td>:</td>
                                @if(@$icd_result->ROOT_LEX_TEXT_IMO_CODE != "")
                                <td>{{ @$icd_result->ROOT_LEX_TEXT_IMO_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Root Lex Text Desc</td>
                                <td>:</td>
                                @if(@$icd_result->ROOT_LEX_TEXT_DESC != "")
                                <td>{{ @$icd_result->ROOT_LEX_TEXT_DESC }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Post Coode Map Flag</td>
                                <td>:</td>
                                @if(@$icd_result->POST_COORD_MAP_FLAG != "")
                                <td>{{ @$icd_result->POST_COORD_MAP_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Post Cooed LEX Text IMO Code</td>
                                <td>:</td>
                                @if(@$icd_result->POST_COORD_LEX_TEXT_IMO_CODE != "")
                                <td>{{ @$icd_result->POST_COORD_LEX_TEXT_IMO_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Post Cooed LEX Text Desc</td>
                                <td>:</td>
                                @if(@$icd_result->POST_COORD_LEX_TEXT_DESC != "")
                                <td>{{ @$icd_result->POST_COORD_LEX_TEXT_DESC }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>							
                            <tr>
                                <td> Parent </td>
                                <td>:</td>
                                @if(@$icd_result->PARENT != "")
                                <td>{{ @$icd_result->PARENT }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Children </td>
                                <td>:</td>
                                @if(@$icd_result->CHILDREN != "")
                                <td>{{ @$icd_result->CHILDREN }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Instantiated Mods</td>
                                <td>:</td>
                                @if(@$icd_result->INSTANTIATED_MODS != "")
                                <td>{{ @$icd_result->INSTANTIATED_MODS }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Family Pact Code </td>
                                <td>:</td>
                                @if(@$icd_result->FAMILY_PACT_CODE != "")
                                <td>{{ @$icd_result->FAMILY_PACT_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Family Pact Description </td>
                                <td>:</td>
                                @if(@$icd_result->FAMILY_PACT_DESCRIPTION != "")
                                <td>{{ @$icd_result->FAMILY_PACT_DESCRIPTION }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
							<tr>
                                <td>Word Type </td>
                                <td>:</td>
                                @if(@$icd_result->WORD_TYPE != "")
                                <td>{{ @$icd_result->WORD_TYPE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Team Type </td>
                                <td>:</td>
                                @if(@$icd_result->TERM_TYPE != "")
                                <td>{{ @$icd_result->TERM_TYPE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Clinician Team </td>
                                <td>:</td>
                                @if(@$icd_result->CLINICIAN_TERM != "")
                                <td>{{ @$icd_result->CLINICIAN_TERM }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Pro Lex Flag </td>
                                <td>:</td>
                                @if(@$icd_result->PROF_LEX_FLAG != "")
                                <td>{{ @$icd_result->PROF_LEX_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Cons Lex Flag </td>
                                <td>:</td>
                                @if(@$icd_result->CONS_LEX_FLAG != "")
                                <td>{{ @$icd_result->CONS_LEX_FLAG }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Default Lex Text Imo Code </td>
                                <td>:</td>
                                @if(@$icd_result->DEFAULT_LEX_TEXT_IMO_CODE != "")
                                <td>{{ @$icd_result->DEFAULT_LEX_TEXT_IMO_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Alt Lex Text IMO Code</td>
                                <td>:</td>
                                @if(@$icd_result->ALT_LEX_TEXT_IMO_CODE != "")
                                <td>{{ @$icd_result->ALT_LEX_TEXT_IMO_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Pat Lex Text IMO Code </td>
                                <td>:</td>
                                @if(@$icd_result->PAT_LEX_TEXT_IMO_CODE != "")
                                <td>{{ @$icd_result->PAT_LEX_TEXT_IMO_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>                            
                            <tr>
                                <td> Prev Kndg Code </td>
                                <td>:</td>
                                @if(@$icd_result->prev_kndg_code != "")
                                <td>{{ @$icd_result->prev_kndg_code }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>Prev ICD10CM Code </td>
                                <td>:</td>
                                @if(@$icd_result->PREV_ICD10CM_CODE != "")
                                <td>{{ @$icd_result->PREV_ICD10CM_CODE }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td> Non Principal DX</td>
                                <td>:</td>
                                @if(@$icd_result->NON_PRINCIPAL_DX != "")
                                <td>{{ @$icd_result->NON_PRINCIPAL_DX }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
                            <tr>
                                <td>searchpayload</td>
                                <td>:</td>
                                @if(@$icd_result->searchpayload != "")
                                <td>{{ @$icd_result->searchpayload }}</td>
                                @else
                                <td class="disable-color">None</td>	
                                @endif
                            </tr>
						</table>
                    </div>	<!-- /.box-body -->
                </span>
            </td>
            <td>{{ App\Http\Helpers\Helpers::dateFormat(@$icd_result->LASTUPDATED,'date') }}</td>
        </tr>
		<?php $icd_count++; ?>
        @endforeach
        @endif
    </tbody> 
</table> 
@else
<p>{{ @$message }}</p>
@endif