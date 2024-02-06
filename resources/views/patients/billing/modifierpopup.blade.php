<div id="js-modifier-popup" class="modal fade in">
    <div class="modal-md-500">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close close_popup" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Choose Modifier</h4>
        </div>
        <div class="modal-body">
            <div class="box box-view no-shadow no-border no-bottom"><!--  Box Starts -->
                <div class="box-body no-padding form-horizontal">
                    <div class="col-lg-12 col-md-12 col-sm-12 table-responsive modal-icd-scroll-500">
                        {!! Form::hidden('modifier_code_order',null,['id' => 'modifier_code_order']) !!}             
                        <table id="search_table" class="table table-striped table-bordered">
                          <thead>
                            <tr>
								<th></th> 
								<th>Code</th>   
								<th>Name</th>                                           
                            </tr>
                           </thead>
                           <tbody>
                            @foreach($modifier as $key => $modifier)                                        
                            <tr>
                                <td><input class="chk" name="modifier_search[]" type="checkbox" value="{{$modifier->code}}" data-id="modifier_code_{{$modifier->code}}" id="MOD{{$key}}"><label for="MOD{{$key}}" class="no-bottom med-darkgray">&nbsp;</label></td>            
								<td>{{ $modifier->code }}</td>
								<td>{{ str_limit($modifier->name, 30, '..') }}</td>                                    
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 margin-t-10" style="border-top:1px solid #f0f0f0;">
                        <span class="js-modifier-display med-orange font600"></span>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">
                        <a class= "js-apply-modifier btn btn-medcubics-small margin-l-5" style="display:none;" href="javascript:void(0)">Apply</a>
                    </div>
                </div>
              </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 