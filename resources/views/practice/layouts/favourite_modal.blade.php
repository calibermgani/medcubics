<!-- Favourites Alert Window Starts  -->
<div id="js-favourite-confirm" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center" id="js-favourite-message"></div>
					</div>
				</ul>                   
				<div class="modal-footer">
					<button class="confirm btn btn-medcubics-small width-60" id="true" type="button" data-dismiss="modal">Yes</button>
					<button class="confirm btn btn-medcubics-small width-60" id="false" type="button" data-dismiss="modal">No</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 
<!-- Favourites Alert Window Ends  -->

<!-- Favourites Alert Window Starts  -->
<?php $procedure_category =   App\Http\Helpers\Helpers::getProcedureCategory() ?>
<div id="js-favourite-category-update" class="modal fade">
	<div class="modal-sm-usps">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Alert</h4>
			</div>
			<div class="modal-body">
				<ul class="nav nav-list line-height-26">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 med-green font600 text-center" id="js-favourite-message"></div>
						<div class="form-group row">
							{!! Form::label('Procedure Category', 'Procedure Category', ['class'=>'col-lg-6 col-md-6 col-sm-6 col-xs-12 control-label']) !!}
							<div class="col-lg-6 col-md-6 col-sm-6 @if($errors->first('pos_id')) error @endif ">
								{!! Form::select('popup_procedure_category',$procedure_category,  null,['class'=>'form-control select2','id'=>'popup_procedure_category']) !!}
								{!! $errors->first('procedure_category', '<p> :message</p>')  !!}  
							</div>
						</div>
						<div class="col-sm-12"></div>
					</div>
					<span class="med-orange proc-category-error"><i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Procedure category is mandatory to add a CPT as favorites </i></span>
				</ul>                   
				<div class="modal-footer">
					<button class="confirm btn btn-medcubics-small width-60 popup_procedure_category_btn" id="update" type="button" data-dismiss="modal">Update</button>
					<button class="confirm btn btn-medcubics-small width-60 popup_procedure_category_btn" id="add" type="button" style="display: none;" data-dismiss="modal">Add</button>
				</div>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- Modal Light Box Ends --> 
<!-- Favourites Alert Window Ends  -->